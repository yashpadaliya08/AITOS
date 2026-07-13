<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\DTO\DatabaseDTO;
use App\Services\Architect\Validators\DatabaseValidator;
use Illuminate\Support\Str;

class DatabaseGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start        = microtime(true);
        $projectModel = new ProjectModelDTO($state);
        $relations    = $state['knowledgeGraph']['relations'] ?? [];

        $tables         = [];
        $migrationOrder = [];

        foreach ($projectModel->entities as $entity) {
            if (is_array($entity)) {
                $name       = $entity['name']       ?? '';
                $attributes = $entity['attributes'] ?? [];
            } else {
                $name       = (string) $entity;
                $attributes = $this->inferAttributesFromName($name);
            }

            $rawName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $name)));
            if (empty($rawName)) continue;

            // Use Laravel's Str::plural for correct English pluralisation
            $tableName = Str::plural($rawName);

            $columns = [
                ['name' => 'id', 'type' => 'BIGINT UNSIGNED', 'primary_key' => true, 'auto_increment' => true],
            ];

            foreach ($attributes as $attr) {
                if (strtolower($attr) === 'id') continue;

                $columns[] = array_filter([
                    'name'   => strtolower($attr),
                    'type'   => $this->inferColumnType($attr),
                    'unique' => strtolower($attr) === 'email' ? true : null,
                ], fn($v) => !is_null($v));
            }

            // Add foreign keys from knowledge graph relations
            foreach ($relations as $rel) {
                if ($rel['target'] === $rawName) {
                    // Use Str::singular for correct FK column name (e.g. users → user_id)
                    $fkColumn = Str::singular($rel['source']) . '_id';
                    $fkTable  = Str::plural($rel['source']);
                    $exists   = false;
                    foreach ($columns as $col) {
                        if ($col['name'] === $fkColumn) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $columns[] = [
                            'name'        => $fkColumn,
                            'type'        => 'BIGINT UNSIGNED',
                            'foreign_key' => [
                                'references' => 'id',
                                'on'         => $fkTable,
                            ],
                        ];
                    }
                }
            }

            // Standard Laravel timestamps
            $columns[] = ['name' => 'created_at', 'type' => 'TIMESTAMP', 'nullable' => true];
            $columns[] = ['name' => 'updated_at', 'type' => 'TIMESTAMP', 'nullable' => true];

            $tables[]         = [
                'name'        => $tableName,
                'columns'     => $columns,
                'indexes'     => [],
                'constraints' => [],
            ];
            $migrationOrder[] = $tableName;
        }

        // Fallback: always include a users table
        if (empty($tables)) {
            $tables[] = [
                'name'        => 'users',
                'columns'     => [
                    ['name' => 'id',         'type' => 'BIGINT UNSIGNED', 'primary_key' => true, 'auto_increment' => true],
                    ['name' => 'name',        'type' => 'VARCHAR(255)'],
                    ['name' => 'email',       'type' => 'VARCHAR(255)', 'unique' => true],
                    ['name' => 'password',    'type' => 'VARCHAR(255)'],
                    ['name' => 'created_at',  'type' => 'TIMESTAMP', 'nullable' => true],
                    ['name' => 'updated_at',  'type' => 'TIMESTAMP', 'nullable' => true],
                ],
                'indexes'     => [],
                'constraints' => [],
            ];
            $migrationOrder[] = 'users';
        }

        $dbDto    = new DatabaseDTO([
            'tables'          => $tables,
            'relationships'   => $relations,
            'migration_order' => $migrationOrder,
        ]);
        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success'        => true,
            'warnings'       => [],
            'errors'         => [],
            'generated_file' => 'database_schema.json',
            'execution_time' => $duration,
            'data'           => $dbDto->toArray(),
        ]);
    }

    /**
     * Infer sensible SQL column type from the attribute name.
     */
    private function inferColumnType(string $attr): string
    {
        $lower = strtolower($attr);

        if (str_ends_with($lower, '_id') || $lower === 'id') {
            return 'BIGINT UNSIGNED';
        }
        if (str_contains($lower, 'description') || str_contains($lower, 'content') || str_contains($lower, 'body') || str_contains($lower, 'text')) {
            return 'TEXT';
        }
        if (str_contains($lower, 'date') || str_contains($lower, 'dob') || str_contains($lower, 'birthday')) {
            return 'DATE';
        }
        if (str_contains($lower, 'at') && (str_contains($lower, 'created') || str_contains($lower, 'updated') || str_contains($lower, 'deleted'))) {
            return 'TIMESTAMP';
        }
        if (str_contains($lower, 'price') || str_contains($lower, 'amount') || str_contains($lower, 'cost') || str_contains($lower, 'salary')) {
            return 'DECIMAL(10,2)';
        }
        if (str_contains($lower, 'count') || str_contains($lower, 'quantity') || str_contains($lower, 'number') || str_contains($lower, 'age')) {
            return 'INTEGER';
        }
        if (str_contains($lower, 'is_') || str_contains($lower, 'has_') || str_contains($lower, 'active') || str_contains($lower, 'enabled')) {
            return 'BOOLEAN';
        }

        return 'VARCHAR(255)';
    }

    /**
     * Heuristically infer attributes from an entity name when no structured data exists.
     */
    private function inferAttributesFromName(string $name): array
    {
        $lower = strtolower($name);
        $attrs = [];

        if (str_contains($lower, 'email'))       $attrs[] = 'email';
        if (str_contains($lower, 'password'))    $attrs[] = 'password';
        if (str_contains($lower, 'name'))        $attrs[] = 'name';
        if (str_contains($lower, 'title') || str_contains($lower, 'subject')) $attrs[] = 'title';
        if (str_contains($lower, 'description') || str_contains($lower, 'content')) $attrs[] = 'description';
        if (str_contains($lower, 'status'))      $attrs[] = 'status';

        return $attrs;
    }

    public function validate(array $output): array
    {
        return DatabaseValidator::validate($output);
    }

    public function getName(): string
    {
        return 'DatabaseGenerator';
    }

    public function getDependencies(): array
    {
        return ['projectModel', 'knowledgeGraph'];
    }
}
