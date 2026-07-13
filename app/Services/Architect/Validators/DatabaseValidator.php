<?php

namespace App\Services\Architect\Validators;

class DatabaseValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        if (!isset($data['tables']) || !is_array($data['tables'])) {
            $errors[] = "Database schema database_schema.json is missing 'tables' configuration.";
        } else {
            foreach ($data['tables'] as $idx => $table) {
                if (empty($table['name'])) {
                    $errors[] = "Table at index {$idx} is missing a table name.";
                }
                if (empty($table['columns']) || !is_array($table['columns'])) {
                    $errors[] = "Table '" . ($table['name'] ?? $idx) . "' contains no columns definition.";
                }
            }
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
