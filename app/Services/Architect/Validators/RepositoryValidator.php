<?php

namespace App\Services\Architect\Validators;

class RepositoryValidator
{
    public static function validate(array $data): array
    {
        $errors = [];
        $warnings = [];

        if (empty($data['files']) || !is_array($data['files'])) {
            $errors[] = "Repository contains no generated source files.";
        } else {
            $hasConfig = false;
            foreach (array_keys($data['files']) as $path) {
                if (str_contains($path, 'composer.json') || str_contains($path, 'package.json') || str_contains($path, 'requirements.txt') || str_contains($path, '__manifest__.py')) {
                    $hasConfig = true;
                }
            }
            if (!$hasConfig) {
                $warnings[] = "Repository contains no configuration file templates.";
            }
        }

        if (empty($data['framework'])) {
            $errors[] = "Target framework parameter is missing.";
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }
}
