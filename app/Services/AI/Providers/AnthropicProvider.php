<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Prompts\RequirementAnalysisPrompt;
use App\Services\AI\Validators\AnalysisResultValidator;
use Illuminate\Support\Facades\Http;

class AnthropicProvider implements AIProvider
{
    /**
     * Call Anthropic Messages endpoint for structured analysis.
     */
    public function analyze(ProjectContext $context, string $apiKey): array
    {
        $systemPrompt = RequirementAnalysisPrompt::getSystemPrompt();
        $userMessage = RequirementAnalysisPrompt::getUserMessage($context->toArray());

        $text = $this->chat($systemPrompt, $userMessage, $apiKey, true);

        return AnalysisResultValidator::validateAndClean($text);
    }

    /**
     * Send a raw system prompt + user message to the Anthropic API and return the raw text response.
     */
    public function chat(string $systemPrompt, string $userMessage, string $apiKey, bool $expectJson = false): string
    {
        $model = config('ai.providers.anthropic.model', 'claude-3-haiku-20240307');
        $maxTokens = (int) config('ai.providers.anthropic.max_tokens', 4000);
        $timeout = (int) config('ai.providers.anthropic.timeout', 120);

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout($timeout)->post('https://api.anthropic.com/v1/messages', [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception("Anthropic API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $content = $response->json('content.0.text');
        if (empty($content)) {
            throw new \Exception("Empty response returned from Anthropic API.");
        }

        return $content;
    }
}
