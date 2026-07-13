<?php

namespace App\Services\Architect\Validators;

class KnowledgeGraphValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        // In Milestone 1, we validate structure configuration
        if (!isset($data['relations']) || !is_array($data['relations'])) {
            $errors[] = "Knowledge graph is missing the mandatory 'relations' list mapping.";
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
