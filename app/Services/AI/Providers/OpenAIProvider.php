<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Prompts\RequirementAnalysisPrompt;
use App\Services\AI\Validators\AnalysisResultValidator;
use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProvider
{
    /**
     * Call OpenAI Chat completions endpoint.
     */
    public function analyze(ProjectContext $context, string $apiKey): array
    {
        $model = config('ai.providers.openai.model', 'gpt-4o-mini');
        $temperature = (float) config('ai.providers.openai.temperature', 0.2);
        $timeout = (int) config('ai.providers.openai.timeout', 30);

        $systemPrompt = RequirementAnalysisPrompt::getSystemPrompt();
        $userMessage = RequirementAnalysisPrompt::getUserMessage($context->toArray());

        $url = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json'
        ];

        // Check if OpenRouter key is passed
        if (str_starts_with($apiKey, 'sk-or-')) {
            $url = 'https://openrouter.ai/api/v1/chat/completions';
            $headers['HTTP-Referer'] = 'http://localhost';
            $headers['X-Title'] = 'AITOS';

            // Resolve model to OpenRouter namespaced format
            if (!str_contains($model, '/')) {
                if (str_contains($model, 'gpt-4o-mini')) {
                    $model = 'openai/gpt-4o-mini';
                } else if (str_contains($model, 'gemini')) {
                    $model = 'google/gemini-2.5-flash';
                } else {
                    $model = 'openai/gpt-4o-mini'; // default fallback
                }
            }
        }

        $maxTokens = 3000;
        if (str_contains($model, ':free')) {
            $maxTokens = 8000;
        }

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens
        ];

        if (!str_starts_with($apiKey, 'sk-or-') || str_contains($model, 'openai/') || str_contains($model, 'gpt-')) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withHeaders($headers)->timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("OpenAI API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $choices = $response->json('choices');
        if (empty($choices) || !isset($choices[0]['message']['content'])) {
            throw new \Exception("Empty response choices returned from OpenAI API.");
        }

        $text = $choices[0]['message']['content'];

        return AnalysisResultValidator::validateAndClean($text);
    }
}
