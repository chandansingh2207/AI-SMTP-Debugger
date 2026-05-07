<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Services\SmtpTesterService;
use App\Services\EmailAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    /**
     * Show the email debugger form
     */
    public function index()
    {
        return view('email');
    }

    /**
     * Analyze email via SMTP and AI
     */
    public function analyzeEmail(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'sometimes|in:tls,ssl,none',
            'smtp_timeout' => 'sometimes|integer|min:5|max:60',
            'from_email' => 'required|email',
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'is_html' => 'sometimes|boolean',
            'ai_provider' => 'required|in:openai,openrouter,gemini',
        ]);

        $smtpResult = 'Pending...';
        $smtpStatus = 'pending';

        try {
            // Dynamically configure SMTP settings
            $encryption = $validated['smtp_encryption'] ?? 'tls';
            $smtpConfig = [
                'mail.mailers.smtp.host' => $validated['smtp_host'],
                'mail.mailers.smtp.port' => $validated['smtp_port'],
                'mail.mailers.smtp.username' => $validated['smtp_username'],
                'mail.mailers.smtp.password' => $validated['smtp_password'],
                'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
                'mail.mailers.smtp.timeout' => $validated['smtp_timeout'] ?? 10,
                'mail.from.address' => $validated['from_email'],
                'mail.from.name' => 'AI Email Debugger',
            ];
            config($smtpConfig);

            // Attempt to send test email
            $isHtml = $validated['is_html'] ?? false;
            
            if ($isHtml) {
                Mail::html($validated['body'], function ($message) use ($validated) {
                    $message->to($validated['to_email'])
                        ->subject($validated['subject']);
                });
            } else {
                Mail::raw($validated['body'], function ($message) use ($validated) {
                    $message->to($validated['to_email'])
                        ->subject($validated['subject']);
                });
            }

            $smtpResult = 'Email sent successfully!';
            $smtpStatus = 'success';
        } catch (\Exception $e) {
            $smtpResult = 'SMTP Error: ' . $e->getMessage();
            $smtpStatus = 'error';
        }

        // Prepare data for AI analysis
        $analysisData = [
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'smtp_result' => $smtpResult,
            'smtp_status' => $smtpStatus,
        ];

        // Get AI analysis
        try {
            $aiService = new AIService();
            $aiAnalysis = $aiService->analyzeEmail($analysisData, $validated['ai_provider']);

            return response()->json([
                'success' => true,
                'smtp' => [
                    'status' => $smtpStatus,
                    'message' => $smtpResult,
                ],
                'ai' => $aiAnalysis,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'smtp' => [
                    'status' => $smtpStatus,
                    'message' => $smtpResult,
                ],
                'error' => 'AI Analysis Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test SMTP connection without sending email
     */
    public function testSmtp(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'sometimes|string',
            'smtp_password' => 'sometimes|string',
            'smtp_encryption' => 'sometimes|in:tls,ssl,none',
            'smtp_timeout' => 'sometimes|integer|min:5|max:60',
        ]);

        $smtpTester = new SmtpTesterService();
        
        $config = [
            'host' => $validated['smtp_host'],
            'port' => $validated['smtp_port'],
            'encryption' => $validated['smtp_encryption'] ?? 'tls',
            'username' => $validated['smtp_username'] ?? '',
            'password' => $validated['smtp_password'] ?? '',
            'timeout' => $validated['smtp_timeout'] ?? 10,
        ];

        $result = $smtpTester->testConnection($config);

        return response()->json($result);
    }

    /**
     * Check email authentication (SPF/DKIM/DMARC/MX)
     */
    public function checkAuth(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string',
            'dkim_selector' => 'sometimes|string',
        ]);

        $emailAuthService = new EmailAuthService();
        
        $dkimSelector = $validated['dkim_selector'] ?? null;
        $results = $emailAuthService->checkAll($validated['domain'], $dkimSelector);

        return response()->json([
            'success' => true,
            'domain' => $validated['domain'],
            'results' => $results,
        ]);
    }
}
