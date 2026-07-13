<?php

namespace App\Services\Architect\Validators;

class DocumentationValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Check if documentation keys are present
        $requiredDocs = ['README.md', 'START_HERE.md', 'PROJECT_SUMMARY.md'];
        foreach ($requiredDocs as $doc) {
            if (empty($data[$doc])) {
                $warnings[] = "Documentation file '{$doc}' has not been compiled or is blank.";
            }
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
