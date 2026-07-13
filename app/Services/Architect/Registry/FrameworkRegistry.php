<?php

namespace App\Services\Architect\Registry;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class FrameworkRegistry
{
    protected static array $providers = [];

    /**
     * Register a framework provider.
     */
    public static function register(string $name, FrameworkProviderInterface $provider): void
    {
        self::$providers[strtolower($name)] = $provider;
    }

    /**
     * Resolve the framework provider by name.
     */
    public static function get(string $name): ?FrameworkProviderInterface
    {
        return self::$providers[strtolower($name)] ?? null;
    }

    /**
     * Get all registered framework providers.
     */
    public static function all(): array
    {
        return self::$providers;
    }
}
