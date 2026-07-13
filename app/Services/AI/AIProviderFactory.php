<?php

namespace App\Services\AI;

use App\Services\AI\Providers\AIProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\OpenAIProvider;
use App\Services\AI\Providers\AnthropicProvider;

class AIProviderFactory
{
    /**
     * Registry of dynamically registered custom providers.
     */
    protected static array $registry = [];

    /**
     * The set of built-in provider names for validation error messages.
     */
    private const KNOWN_PROVIDERS = ['openai', 'anthropic', 'gemini'];

    /**
     * Register a pluggable AI Provider under a custom name.
     */
    public static function register(string $name, AIProvider $provider): void
    {
        self::$registry[strtolower(trim($name))] = $provider;
    }

    /**
     * Resolve and return the correct AI Provider instance.
     *
     * @throws \InvalidArgumentException When an unrecognised provider name is given.
     */
    public static function make(string $providerName): AIProvider
    {
        $name = strtolower(trim($providerName));

        // Check custom-registered providers first
        if (isset(self::$registry[$name])) {
            return self::$registry[$name];
        }

        return match ($name) {
            'openai'    => new OpenAIProvider(),
            'anthropic' => new AnthropicProvider(),
            'gemini'    => new GeminiProvider(),
            default     => throw new \InvalidArgumentException(
                "Unknown AI provider: '{$name}'. Supported providers are: " . implode(', ', array_merge(self::KNOWN_PROVIDERS, array_keys(self::$registry))) . "."
            ),
        };
    }

    /**
     * Return the list of all registered provider names (built-in + custom).
     */
    public static function available(): array
    {
        return array_unique(array_merge(self::KNOWN_PROVIDERS, array_keys(self::$registry)));
    }
}
