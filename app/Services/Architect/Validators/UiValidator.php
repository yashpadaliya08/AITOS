<?php

namespace App\Services\Architect\Validators;

class UiValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        if (!isset($data['pages']) || !is_array($data['pages'])) {
            $errors[] = "UI blueprint config is missing the mandatory 'pages' list.";
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
