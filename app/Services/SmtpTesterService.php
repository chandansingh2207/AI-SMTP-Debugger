<?php

namespace App\Services;

use Exception;

class SmtpTesterService
{
    private $connection;
    private $log = [];
    private $timeout = 10;

    /**
     * Test SMTP connection without sending email
     *
     * @param array $config Connection config
     * @return array Test results with log
     */
    public function testConnection(array $config): array
    {
        $this->log = [];
        $host = $config['host'] ?? '';
        $port = (int) ($config['port'] ?? 587);
        $encryption = $config['encryption'] ?? 'tls';
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $this->timeout = (int) ($config['timeout'] ?? 10);

        try {
            // Build connection string based on encryption
            $connectionString = $this->buildConnectionString($host, $port, $encryption);
            
            $this->log("Attempting to connect to {$connectionString} (timeout: {$this->timeout}s)");
            
            // Connect to SMTP server
            $this->connection = stream_socket_client(
                $connectionString,
                $errno,
                $errstr,
                $this->timeout,
                STREAM_CLIENT_CONNECT
            );

            if (!$this->connection) {
                throw new Exception("Connection failed: {$errstr} (Error: {$errno})");
            }

            $this->log("Connected successfully");

            // Read initial greeting
            $greeting = $this->readResponse();
            $this->log("Server greeting: {$greeting}");

            // Send EHLO/HELO
            $this->sendCommand("EHLO " . gethostname());
            $ehloResponse = $this->readResponse();
            $this->log("EHLO response: {$ehloResponse}");

            // Check for STARTTLS support if using TLS
            if ($encryption === 'tls' && strpos($ehloResponse, 'STARTTLS') !== false) {
                $this->sendCommand("STARTTLS");
                $tlsResponse = $this->readResponse();
                $this->log("STARTTLS response: {$tlsResponse}");

                // Enable crypto on the connection
                if (!stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new Exception("Failed to enable TLS encryption");
                }
                $this->log("TLS encryption enabled successfully");

                // Re-send EHLO after TLS
                $this->sendCommand("EHLO " . gethostname());
                $ehloResponse = $this->readResponse();
                $this->log("EHLO after TLS: {$ehloResponse}");
            }

            // Test authentication if credentials provided
            if (!empty($username) && !empty($password)) {
                $authResult = $this->testAuthentication($username, $password);
                $this->log("Authentication test: " . ($authResult['success'] ? "SUCCESS" : "FAILED - " . $authResult['message']));
            }

            $this->log("Connection test completed successfully");
            
            return [
                'success' => true,
                'message' => 'SMTP connection test passed',
                'log' => $this->log,
                'server_info' => $this->parseServerInfo($ehloResponse)
            ];

        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'log' => $this->log,
                'server_info' => []
            ];
        } finally {
            if ($this->connection) {
                @fclose($this->connection);
            }
        }
    }

    /**
     * Build connection string based on encryption type
     */
    private function buildConnectionString(string $host, int $port, string $encryption): string
    {
        switch (strtolower($encryption)) {
            case 'ssl':
                return "ssl://{$host}:{$port}";
            case 'tls':
            case 'none':
            default:
                return "tcp://{$host}:{$port}";
        }
    }

    /**
     * Test SMTP authentication
     */
    private function testAuthentication(string $username, string $password): array
    {
        try {
            // Send AUTH LOGIN command
            $this->sendCommand("AUTH LOGIN");
            $response = $this->readResponse();
            $this->log("AUTH LOGIN response: {$response}");

            // Send username (base64 encoded)
            $this->sendCommand(base64_encode($username));
            $response = $this->readResponse();
            $this->log("Username response: {$response}");

            // Send password (base64 encoded)
            $this->sendCommand(base64_encode($password));
            $response = $this->readResponse();
            $this->log("Password response: {$response}");

            if (strpos($response, '235') === 0) {
                return ['success' => true, 'message' => 'Authentication successful'];
            } else {
                return ['success' => false, 'message' => 'Authentication failed: ' . $response];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send SMTP command
     */
    private function sendCommand(string $command): void
    {
        $this->log(">> {$command}");
        fwrite($this->connection, $command . "\r\n");
    }

    /**
     * Read SMTP response
     */
    private function readResponse(): string
    {
        $response = '';
        while ($line = fgets($this->connection, 515)) {
            $response .= $line;
            // Check if this is the last line (no continuation code)
            if (isset($line[3]) && $line[3] !== '-') {
                break;
            }
        }
        return trim($response);
    }

    /**
     * Add entry to log
     */
    private function log(string $message): void
    {
        $this->log[] = date('H:i:s') . ' - ' . $message;
    }

    /**
     * Parse EHLO response for server capabilities
     */
    private function parseServerInfo(string $ehloResponse): array
    {
        $info = [];
        $lines = explode("\n", $ehloResponse);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^250-([A-Za-z0-9]+)/', $line, $matches)) {
                $info['capabilities'][] = $matches[1];
            }
            if (preg_match('/^250-SIZE (\d+)/', $line, $matches)) {
                $info['max_size'] = (int) $matches[1];
            }
        }
        
        return $info;
    }
}