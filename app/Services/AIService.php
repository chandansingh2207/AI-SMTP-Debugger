<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AIService
{
    /**
     * Analyze email using selected AI provider
     *
     * @param array $data Contains subject, body, smtp_result, smtp_status
     * @param string $provider openai, openrouter, or gemini
     * @return array Structured AI response with issue, fix, improved_subject, improved_body
     * @throws \Exception
     */
    public function analyzeEmail(array $data, string $provider): array
    {
        // Build the prompt
        $prompt = $this->buildPrompt($data);

        // Call appropriate AI provider
        return match ($provider) {
            'openai' => $this->callOpenAI($prompt),
            'openrouter' => $this->callOpenRouter($prompt),
            'gemini' => $this->callGemini($prompt),
            default => throw new \Exception('Invalid AI provider'),
        };
    }

    /**
     * Build the analysis prompt
     */
    private function buildPrompt(array $data): string
    {
        return "You are an email deliverability expert.

Analyze this email and SMTP result.

Check:
* Why email may go to spam
* Issues in subject line
* Content quality
* Missing best practices
* If SMTP error exists, explain it clearly

Return ONLY valid JSON (no markdown, no code blocks):
{
  \"issue\": \"...\",
  \"fix\": \"...\",
  \"improved_subject\": \"...\",
  \"improved_body\": \"...\"
}

Input:
Subject: {$data['subject']}
Body: {$data['body']}
SMTP Result: {$data['smtp_result']}";
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt): array
    {
        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('OPENAI_API_KEY not set in .env');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini', // Using latest model
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API Error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        return $this->parseJsonResponse($content);
    }

    /**
     * Call OpenRouter API
     */
    private function callOpenRouter(string $prompt): array
    {
        $apiKey = env('OPENROUTER_API_KEY');
        if (!$apiKey) {
            throw new \Exception('OPENROUTER_API_KEY not set in .env');
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
            'HTTP-Referer' => url('/'),
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ]);

        if (!$response->successful()) {
            throw new \Exception('OpenRouter API Error: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content');
        return $this->parseJsonResponse($content);
    }

    /**
     * Call Google Gemini API
     */
    private function callGemini(string $prompt): array
    {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            throw new \Exception('GEMINI_API_KEY not set in .env');
        }

        //$modelName = 'gemini-2.0-flash';
        $modelName = 'gemini-2.5-flash-lite';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$apiKey}";

        $response = Http::timeout(60)
            ->connectTimeout(10)
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new \Exception('Gemini API Error: ' . $response->body());
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        return $this->parseJsonResponse($content);
    }

    /**
     * Parse and validate JSON response from AI
     */
    private function parseJsonResponse(string $content): array
    {
        // Remove markdown code blocks if present
        $content = preg_replace('/^```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);

        if (!$decoded || !is_array($decoded)) {
            throw new \Exception('Invalid JSON response from AI provider');
        }

        // Ensure required fields exist
        return [
            'issue' => $decoded['issue'] ?? 'No issue detected',
            'fix' => $decoded['fix'] ?? 'No fix available',
            'improved_subject' => $decoded['improved_subject'] ?? $decoded['improvedSubject'] ?? '',
            'improved_body' => $decoded['improved_body'] ?? $decoded['improvedBody'] ?? '',
        ];
    }
}
