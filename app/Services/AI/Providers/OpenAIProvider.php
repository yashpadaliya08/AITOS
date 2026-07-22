<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Prompts\RequirementAnalysisPrompt;
use App\Services\AI\Validators\AnalysisResultValidator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIProvider implements AIProvider
{
    /**
     * Call OpenAI Chat completions endpoint for structured analysis.
     */
    public function analyze(ProjectContext $context, string $apiKey): array
    {
        $systemPrompt = RequirementAnalysisPrompt::getSystemPrompt();
        $userMessage = RequirementAnalysisPrompt::getUserMessage($context->toArray());

        $text = $this->chat($systemPrompt, $userMessage, $apiKey, true);

        return AnalysisResultValidator::validateAndClean($text);
    }

    /**
     * Send a raw system prompt + user message to OpenAI/OpenRouter and return the raw text response.
     */
    public function chat(string $systemPrompt, string $userMessage, string $apiKey, bool $expectJson = false): string
    {
        $apiKey = !empty($apiKey) ? $apiKey : config('ai.providers.openai.api_key');
        
        $model = config('ai.providers.openai.model', 'nvidia/nemotron-3-ultra-550b-a55b:free');
        if (empty($model) || !str_contains($model, '/')) {
            $model = 'nvidia/nemotron-3-ultra-550b-a55b:free';
        }
        
        $timeout = (int) config('ai.providers.openai.timeout', 240);
        $maxTokens = (int) config('ai.providers.openai.max_tokens', 4000);

        $url = 'https://openrouter.ai/api/v1/chat/completions';
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => 'http://localhost',
            'X-Title' => 'AITOS',
        ];

        Log::info("OpenAIProvider: Strictly calling OpenRouter model '{$model}' | timeout: {$timeout}s | max_tokens: {$maxTokens}");

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'max_tokens' => $maxTokens,
        ];

        $response = Http::withHeaders($headers)->timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            $errMessage = $response->json('error.message') ?? $response->body();
            Log::error("OpenAIProvider API request failed: Status {$response->status()} — " . $response->body());
            throw new \Exception("OpenAI API request failed: " . $errMessage);
        }

        $content = $response->json('choices.0.message.content');
        if (empty($content)) {
            throw new \Exception("Empty response returned from OpenRouter ({$model}).");
        }

        return $content;
    }
}
