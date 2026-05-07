<?php

namespace App\Services;

class EmailAuthService
{
    /**
     * Check SPF record for a domain
     *
     * @param string $domain
     * @return array SPF check results
     */
    public function checkSPF(string $domain): array
    {
        try {
            $records = dns_get_record($domain, DNS_TXT);
            $spfRecord = null;

            foreach ($records as $record) {
                if (isset($record['txt']) && strpos($record['txt'], 'v=spf1') === 0) {
                    $spfRecord = $record['txt'];
                    break;
                }
            }

            if (!$spfRecord) {
                return [
                    'success' => false,
                    'status' => 'missing',
                    'message' => 'No SPF record found for this domain',
                    'record' => null,
                    'details' => []
                ];
            }

            $details = $this->parseSPF($spfRecord);

            return [
                'success' => true,
                'status' => 'found',
                'message' => 'SPF record found',
                'record' => $spfRecord,
                'details' => $details
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking SPF: ' . $e->getMessage(),
                'record' => null,
                'details' => []
            ];
        }
    }

    /**
     * Check DKIM record for a domain
     *
     * @param string $domain
     * @param string $selector DKIM selector (default: default)
     * @return array DKIM check results
     */
    public function checkDKIM(string $domain, string $selector = 'default'): array
    {
        try {
            $dkimDomain = "{$selector}._domainkey.{$domain}";
            $records = dns_get_record($dkimDomain, DNS_TXT);

            if (empty($records)) {
                return [
                    'success' => false,
                    'status' => 'missing',
                    'message' => "No DKIM record found for selector '{$selector}'",
                    'record' => null,
                    'selector' => $selector
                ];
            }

            $dkimRecord = $records[0]['txt'] ?? null;

            if (!$dkimRecord || strpos($dkimRecord, 'v=DKIM1') !== 0) {
                return [
                    'success' => false,
                    'status' => 'invalid',
                    'message' => 'DKIM record found but invalid format',
                    'record' => $dkimRecord,
                    'selector' => $selector
                ];
            }

            return [
                'success' => true,
                'status' => 'found',
                'message' => 'DKIM record found and valid',
                'record' => $dkimRecord,
                'selector' => $selector
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking DKIM: ' . $e->getMessage(),
                'record' => null,
                'selector' => $selector
            ];
        }
    }

    /**
     * Check DMARC record for a domain
     *
     * @param string $domain
     * @return array DMARC check results
     */
    public function checkDMARC(string $domain): array
    {
        try {
            $dmarcDomain = "_dmarc.{$domain}";
            $records = dns_get_record($dmarcDomain, DNS_TXT);

            if (empty($records)) {
                return [
                    'success' => false,
                    'status' => 'missing',
                    'message' => 'No DMARC record found',
                    'record' => null,
                    'policy' => null
                ];
            }

            $dmarcRecord = $records[0]['txt'] ?? null;

            if (!$dmarcRecord || strpos($dmarcRecord, 'v=DMARC1') !== 0) {
                return [
                    'success' => false,
                    'status' => 'invalid',
                    'message' => 'DMARC record found but invalid format',
                    'record' => $dmarcRecord,
                    'policy' => null
                ];
            }

            $policy = $this->parseDMARCPolicy($dmarcRecord);

            return [
                'success' => true,
                'status' => 'found',
                'message' => 'DMARC record found',
                'record' => $dmarcRecord,
                'policy' => $policy
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking DMARC: ' . $e->getMessage(),
                'record' => null,
                'policy' => null
            ];
        }
    }

    /**
     * Check MX records for a domain
     *
     * @param string $domain
     * @return array MX check results
     */
    public function checkMX(string $domain): array
    {
        try {
            $records = dns_get_record($domain, DNS_MX);

            if (empty($records)) {
                return [
                    'success' => false,
                    'status' => 'missing',
                    'message' => 'No MX records found for this domain',
                    'records' => []
                ];
            }

            // Sort by priority
            usort($records, function ($a, $b) {
                return ($a['pri'] ?? 0) <=> ($b['pri'] ?? 0);
            });

            $mxList = array_map(function ($record) {
                return [
                    'target' => $record['target'] ?? '',
                    'priority' => $record['pri'] ?? 0
                ];
            }, $records);

            return [
                'success' => true,
                'status' => 'found',
                'message' => count($mxList) . ' MX record(s) found',
                'records' => $mxList
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Error checking MX records: ' . $e->getMessage(),
                'records' => []
            ];
        }
    }

    /**
     * Parse SPF record and extract details
     */
    private function parseSPF(string $spfRecord): array
    {
        $details = [
            'mechanisms' => [],
            'qualifiers' => [],
            'all_mechanism' => null
        ];

        $parts = explode(' ', $spfRecord);

        foreach ($parts as $part) {
            if (strpos($part, 'v=spf1') === 0) continue;

            if (preg_match('/^([+\-~?]?)(.+)$/', $part, $matches)) {
                $qualifier = $matches[1] ?: '+';
                $mechanism = $matches[2];
                $details['mechanisms'][] = $mechanism;
                $details['qualifiers'][$mechanism] = $qualifier;

                if (strpos($mechanism, 'all') === 0) {
                    $details['all_mechanism'] = [
                        'qualifier' => $qualifier,
                        'description' => $this->getQualifierDescription($qualifier)
                    ];
                }
            }
        }

        return $details;
    }

    /**
     * Parse DMARC policy from record
     */
    private function parseDMARCPolicy(string $dmarcRecord): array
    {
        $policy = [
            'p' => null,
            'sp' => null,
            'pct' => 100,
            'rua' => null
        ];

        if (preg_match('/p=(\w+)/', $dmarcRecord, $matches)) {
            $policy['p'] = $matches[1];
        }

        if (preg_match('/sp=(\w+)/', $dmarcRecord, $matches)) {
            $policy['sp'] = $matches[1];
        }

        if (preg_match('/pct=(\d+)/', $dmarcRecord, $matches)) {
            $policy['pct'] = (int) $matches[1];
        }

        if (preg_match('/rua=([^;\s]+)/', $dmarcRecord, $matches)) {
            $policy['rua'] = $matches[1];
        }

        return $policy;
    }

    /**
     * Get description for SPF qualifier
     */
    private function getQualifierDescription(string $qualifier): string
    {
        switch ($qualifier) {
            case '+': return 'Pass (default)';
            case '-': return 'Fail - reject';
            case '~': return 'SoftFail - accept but mark';
            case '?': return 'Neutral - no policy';
            default: return 'Unknown';
        }
    }

    /**
     * Run all authentication checks for a domain
     *
     * @param string $domain
     * @param string|null $dkimSelector
     * @return array All check results
     */
    public function checkAll(string $domain, ?string $dkimSelector = null): array
    {
        return [
            'spf' => $this->checkSPF($domain),
            'dkim' => $this->checkDKIM($domain, $dkimSelector ?? 'default'),
            'dmarc' => $this->checkDMARC($domain),
            'mx' => $this->checkMX($domain)
        ];
    }
}