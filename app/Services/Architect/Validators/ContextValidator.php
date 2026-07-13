<?php

namespace App\Services\Architect\Validators;

class ContextValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        $contextKeys = ['CURRENT_CONTEXT.md', 'BACKEND_CONTEXT.md', 'FRONTEND_CONTEXT.md', 'DATABASE_CONTEXT.md'];
        foreach ($contextKeys as $key) {
            if (empty($data[$key])) {
                $warnings[] = "Context layer '{$key}' has not been compiled or is blank.";
            }
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
