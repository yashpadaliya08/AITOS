<?php

namespace App\Services\AI;

use App\Services\AI\Providers\AIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\AnthropicProvider;

class AIProviderFactory
{
    /**
     * Resolve and return the correct AI Provider interface.
     */
    public static function make(string $providerName): AIProvider
    {
        return match (strtolower(trim($providerName))) {
            'openai' => new OpenAIProvider(),
            'anthropic' => new AnthropicProvider(),
            default => new GeminiProvider(),
        };
    }
}
