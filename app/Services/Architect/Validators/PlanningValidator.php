<?php

namespace App\Services\Architect\Validators;

class PlanningValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        if (!isset($data['module_ownership']) || !is_array($data['module_ownership'])) {
            $warnings[] = "Sprint project plan is missing ownership assignments mapping.";
        }
        if (!isset($data['development_order']) || !is_array($data['development_order'])) {
            $warnings[] = "Sprint project plan is missing development order roadmaps.";
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
