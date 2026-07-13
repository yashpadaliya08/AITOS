<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ApiDTO;
use App\Services\Architect\Validators\ApiValidator;
use Illuminate\Support\Str;

class ApiGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        
        $dbSchema = $state['databaseSchema'] ?? ['tables' => []];
        $resources = [];

        foreach ($dbSchema['tables'] as $table) {
            $tableName    = $table['name'];
            $singularName = Str::singular($tableName);
            
            $endpoints = [
                [
                    'method' => 'GET',
                    'path' => "/api/{$tableName}",
                    'description' => "Retrieve list of all {$tableName}",
                    'auth_required' => true,
                    'validation_rules' => []
                ],
                [
                    'method' => 'POST',
                    'path' => "/api/{$tableName}",
                    'description' => "Create a new {$singularName} record",
                    'auth_required' => true,
                    'validation_rules' => $this->inferValidationRules($table['columns'])
                ],
                [
                    'method' => 'GET',
                    'path' => "/api/{$tableName}/{id}",
                    'description' => "Retrieve details for a single {$singularName}",
                    'auth_required' => true,
                    'validation_rules' => []
                ],
                [
                    'method' => 'PUT',
                    'path' => "/api/{$tableName}/{id}",
                    'description' => "Update attributes of a single {$singularName}",
                    'auth_required' => true,
                    'validation_rules' => $this->inferValidationRules($table['columns'], true)
                ],
                [
                    'method' => 'DELETE',
                    'path' => "/api/{$tableName}/{id}",
                    'description' => "Hard delete a single {$singularName} record",
                    'auth_required' => true,
                    'validation_rules' => []
                ]
            ];

            $resources[] = [
                'resource' => $tableName,
                'endpoints' => $endpoints,
                'middleware' => ['auth:api', 'throttle:60,1']
            ];
        }

        if (empty($resources)) {
            $resources[] = [
                'resource' => 'users',
                'endpoints' => [
                    [
                        'method' => 'GET',
                        'path' => '/api/users',
                        'description' => 'List users',
                        'auth_required' => true,
                        'validation_rules' => []
                    ]
                ],
                'middleware' => ['auth:api']
            ];
        }

        $apiDto = new ApiDTO([
            'resources' => $resources,
            'error_responses' => [
                '400' => 'Bad Request',
                '401' => 'Unauthorized Access',
                '403' => 'Forbidden Action',
                '404' => 'Resource Not Found',
                '422' => 'Unprocessable Entity (Validation Errors)',
                '429' => 'Too Many Requests (Rate Limit Exceeded)',
                '500' => 'Internal Server Error',
            ]
        ]);

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'api_design.json',
            'execution_time' => $duration,
            'data' => $apiDto->toArray()
        ]);
    }

    protected function inferValidationRules(array $columns, bool $isUpdate = false): array
    {
        $rules = [];
        $prefix = $isUpdate ? 'nullable|' : 'required|';
        
        foreach ($columns as $col) {
            $name = $col['name'];
            if ($name === 'id' || $name === 'created_at' || $name === 'updated_at') {
                continue;
            }

            $type = $col['type'];
            if (str_contains($type, 'VARCHAR')) {
                $rules[$name] = $prefix . 'string|max:255';
            } elseif (str_contains($type, 'TEXT')) {
                $rules[$name] = $prefix . 'string';
            } elseif (str_contains($type, 'INTEGER')) {
                $rules[$name] = $prefix . 'integer';
            } else {
                $rules[$name] = $prefix . 'string';
            }
        }
        return $rules;
    }

    public function validate(array $output): array
    {
        return ApiValidator::validate($output);
    }

    public function getName(): string
    {
        return 'ApiGenerator';
    }

    public function getDependencies(): array
    {
        return ['databaseSchema'];
    }
}
