<?php

namespace App\Services\AI\Validators;

class AnalysisResultValidator
{
    /**
     * Clean and validate the raw text response from the AI provider.
     */
    public static function validateAndClean(string $rawResponse): array
    {
        $cleaned = trim($rawResponse);

        // Strip markdown codeblocks (```json ... ```)
        if (str_starts_with($cleaned, '```')) {
            $cleaned = preg_replace('/^```(?:json)?|```$/i', '', $cleaned);
            $cleaned = trim($cleaned);
        }

        // Try decoding
        $decoded = json_decode($cleaned, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("The AI did not return a valid JSON object: " . json_last_error_msg() . "\nRaw Response: " . substr($rawResponse, 0, 500));
        }

        // Ensure all required keys exist, fill in defaults if missing
        $requiredKeys = [
            'projectSummary' => 'string',
            'entities' => 'array',
            'modules' => 'array',
            'roles' => 'array',
            'businessRules' => 'array',
            'functionalRequirements' => 'array',
            'nonFunctionalRequirements' => 'array',
            'assumptions' => 'array',
            'risks' => 'array',
            'userStories' => 'array',
            'suggestedFolderStructure' => 'array',
            'suggestedTechnologyStack' => 'assoc',
            'implementationPhases' => 'array',
            'aiNotes' => 'array'
        ];

        foreach ($requiredKeys as $key => $type) {
            if (!isset($decoded[$key])) {
                if ($type === 'string') {
                    $decoded[$key] = '';
                } elseif ($type === 'assoc') {
                    $decoded[$key] = [];
                } else {
                    $decoded[$key] = [];
                }
            } else {
                // Type safety compliance checks
                if ($type === 'array' && !is_array($decoded[$key])) {
                    $decoded[$key] = [$decoded[$key]];
                }
            }
        }

        return $decoded;
    }
}
