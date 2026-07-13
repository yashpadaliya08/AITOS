<?php

namespace App\Services\Architect\Validators;

class BlueprintValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        // Check base fields existence
        $required = ['business', 'database', 'technical', 'ui'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $warnings[] = "Blueprint field '{$field}' is empty or unconfigured.";
            }
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
