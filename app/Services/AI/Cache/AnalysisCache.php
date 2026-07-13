<?php

namespace App\Services\AI\Cache;

use Illuminate\Support\Facades\Cache;

class AnalysisCache
{
    /**
     * Default TTL for cached analysis results (seconds).
     * 1 hour — suitable for AI analysis responses that are expensive to regenerate.
     */
    private const TTL = 3600;

    /**
     * Cache key prefix to avoid collisions with other cache entries.
     */
    private const PREFIX = 'aitos_analysis_';

    /**
     * Generate a deterministic SHA-256 context hash for a given project input set.
     */
    public static function generateHash(string $projectName, string $projectGoal, string $problemStatement): string
    {
        return hash('sha256', trim($projectName) . '|' . trim($projectGoal) . '|' . trim($problemStatement));
    }

    /**
     * Check whether a cached analysis result exists for the given hash.
     */
    public static function has(string $hash): bool
    {
        return Cache::has(self::PREFIX . $hash);
    }

    /**
     * Retrieve a cached analysis result. Returns null if not found.
     */
    public static function get(string $hash): ?array
    {
        return Cache::get(self::PREFIX . $hash);
    }

    /**
     * Store an analysis result in cache with the default TTL.
     */
    public static function store(string $hash, array $result): void
    {
        Cache::put(self::PREFIX . $hash, $result, self::TTL);
    }

    /**
     * Invalidate a specific cached analysis result.
     */
    public static function forget(string $hash): void
    {
        Cache::forget(self::PREFIX . $hash);
    }
}
