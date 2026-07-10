<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Prompts\RequirementAnalysisPrompt;
use App\Services\AI\Validators\AnalysisResultValidator;
use Illuminate\Support\Facades\Http;

class GeminiProvider implements AIProvider
{
    /**
     * Call Google Gemini model endpoint.
     */
    public function analyze(ProjectContext $context, string $apiKey): array
    {
        $model = config('ai.providers.gemini.model', 'gemini-1.5-flash');
        $temperature = (float) config('ai.providers.gemini.temperature', 0.2);
        $timeout = (int) config('ai.providers.gemini.timeout', 30);

        $systemPrompt = RequirementAnalysisPrompt::getSystemPrompt();
        $userMessage = RequirementAnalysisPrompt::getUserMessage($context->toArray());

        // Gemini REST API JSON compilation
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userMessage]
                    ]
                ]
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ],
            'generationConfig' => [
                'temperature' => $temperature,
                'responseMimeType' => 'application/json',
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("Gemini API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $candidates = $response->json('candidates');
        if (empty($candidates) || !isset($candidates[0]['content']['parts'][0]['text'])) {
            throw new \Exception("Empty response candidates returned from Gemini API.");
        }

        $text = $candidates[0]['content']['parts'][0]['text'];
        
        return AnalysisResultValidator::validateAndClean($text);
    }
}
