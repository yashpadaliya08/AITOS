<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;

interface AIProvider
{
    /**
     * Send project context to AI and receive cleaned, validated array results.
     */
    public function analyze(ProjectContext $context, string $apiKey): array;

    /**
     * Send a raw system prompt + user message to the AI and return the raw text response.
     * This is the generic prompt-in/text-out method used by blueprint refinement,
     * blueprint generation, and any future AI features.
     *
     * @param  bool $expectJson  If true, instructs the model to return JSON-formatted output.
     */
    public function chat(string $systemPrompt, string $userMessage, string $apiKey, bool $expectJson = false): string;
}

