<?php

namespace App\Services\AI\Cache;

class AnalysisCache
{
    /**
     * Generate a SHA256 context hash based on project identity.
     */
    public static function generateHash(string $projectName, string $projectGoal, string $problemStatement): string
    {
        return hash('sha256', trim($projectName) . '|' . trim($projectGoal) . '|' . trim($problemStatement));
    }
}
