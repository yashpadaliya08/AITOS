<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use Illuminate\Support\Str;

class CodeGenerationEngine implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        $dbSchema = $state['databaseSchema'] ?? ['tables' => []];
        $tables = $dbSchema['tables'] ?? [];
        
        $mappings = [];

        foreach ($tables as $table) {
            $tableName = $table['name'];
            $singularName = Str::singular($tableName);
            $camelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $singularName)));

            $mappings[] = [
                'entity' => $camelName,
                'model' => $camelName,
                'controller' => $camelName . 'Controller',
                'route' => strtolower($tableName),
                'migration' => 'create_' . strtolower($tableName) . '_table',
                'service' => $camelName . 'Service',
                'repository' => $camelName . 'Repository'
            ];
        }

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'code_generation_model.json',
            'execution_time' => $duration,
            'data' => [
                'mappings' => $mappings
            ]
        ]);
    }

    public function validate(array $output): array
    {
        $errors = [];
        if (!isset($output['mappings']) || !is_array($output['mappings'])) {
            $errors[] = "Code generation mappings array is missing.";
        }
        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => []
        ];
    }

    public function getName(): string
    {
        return 'CodeGenerationEngine';
    }

    public function getDependencies(): array
    {
        return ['requirements', 'databaseSchema', 'apiDesign'];
    }
}
