<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Prompts\RequirementAnalysisPrompt;
use App\Services\AI\Validators\AnalysisResultValidator;
use Illuminate\Support\Facades\Http;

class AnthropicProvider implements AIProvider
{
    /**
     * Call Anthropic Messages endpoint.
     */
    public function analyze(ProjectContext $context, string $apiKey): array
    {
        $model = config('ai.providers.anthropic.model', 'claude-3-haiku-20240307');
        $temperature = (float) config('ai.providers.anthropic.temperature', 0.2);
        $timeout = (int) config('ai.providers.anthropic.timeout', 30);
        $maxTokens = (int) config('ai.providers.anthropic.max_tokens', 4000);

        $systemPrompt = RequirementAnalysisPrompt::getSystemPrompt();
        $userMessage = RequirementAnalysisPrompt::getUserMessage($context->toArray());

        $url = 'https://api.anthropic.com/v1/messages';

        $payload = [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage]
            ]
        ];

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json'
        ])->timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("Anthropic API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $content = $response->json('content');
        if (empty($content) || !isset($content[0]['text'])) {
            throw new \Exception("Empty response content returned from Anthropic API.");
        }

        $text = $content[0]['text'];

        return AnalysisResultValidator::validateAndClean($text);
    }
}
