<?php

namespace App\Services\AI\Validators;

class AnalysisResultValidator
{
    /**
     * Clean and validate the raw text response from the AI provider.
     * Returns a fully-typed array matching the expected requirements schema.
     */
    public static function validateAndClean(string $rawResponse): array
    {
        $cleaned = trim($rawResponse);

        // Strip all markdown code fences regardless of position (```json ... ```)
        $cleaned = preg_replace('/```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/```\s*/i', '', $cleaned);
        $cleaned = trim($cleaned);

        // Attempt JSON decode
        $decoded = json_decode($cleaned, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                "The AI did not return a valid JSON object: " . json_last_error_msg() .
                "\nRaw Response (first 500 chars): " . substr($rawResponse, 0, 500)
            );
        }

        if (!is_array($decoded)) {
            throw new \Exception("AI response decoded to a non-array type. Expected JSON object.");
        }

        // Schema definition: key => expected type
        $schema = [
            'projectSummary'            => 'string',
            'entities'                  => 'array',
            'relationships'             => 'array',
            'modules'                   => 'array',
            'roles'                     => 'array',
            'businessRules'             => 'array',
            'functionalRequirements'    => 'array',
            'nonFunctionalRequirements' => 'array',
            'assumptions'               => 'array',
            'risks'                     => 'array',
            'userStories'               => 'array',
            'suggestedFolderStructure'  => 'array',
            'suggestedTechnologyStack'  => 'assoc',
            'implementationPhases'      => 'array',
            'aiNotes'                   => 'array',
        ];

        foreach ($schema as $key => $expectedType) {
            if (!isset($decoded[$key])) {
                // Provide typed defaults for missing keys
                $decoded[$key] = match ($expectedType) {
                    'string' => '',
                    'assoc'  => (object) [],
                    default  => [],
                };
                continue;
            }

            // Coerce types so downstream code always gets consistent shapes
            switch ($expectedType) {
                case 'string':
                    if (!is_string($decoded[$key])) {
                        $decoded[$key] = (string) $decoded[$key];
                    }
                    break;

                case 'array':
                    if (!is_array($decoded[$key])) {
                        // Wrap scalar value into single-item array
                        $decoded[$key] = [$decoded[$key]];
                    }
                    break;

                case 'assoc':
                    if (!is_array($decoded[$key]) || array_is_list($decoded[$key])) {
                        // If it's a sequential list (or not an array), reset to empty assoc
                        $decoded[$key] = [];
                    }
                    break;
            }
        }

        return $decoded;
    }
}
