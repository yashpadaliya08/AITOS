<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTO\ProjectContext;

interface AIProvider
{
    /**
     * Send project context to AI and receive cleaned, validated array results.
     */
    public function analyze(ProjectContext $context, string $apiKey): array;
}
