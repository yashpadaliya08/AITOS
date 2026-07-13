<?php

namespace App\Services\Architect\Validators;

class ApiValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        if (!isset($data['resources']) || !is_array($data['resources'])) {
            $errors[] = "API design config is missing 'resources' mapping definitions.";
        } else {
            foreach ($data['resources'] as $resIdx => $res) {
                if (empty($res['resource'])) {
                    $errors[] = "API Resource at index {$resIdx} is missing a resource identifier.";
                }
                if (empty($res['endpoints']) || !is_array($res['endpoints'])) {
                    $warnings[] = "Resource '" . ($res['resource'] ?? $resIdx) . "' defines no endpoints.";
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
