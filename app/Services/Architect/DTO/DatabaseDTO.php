<?php

namespace App\Services\Architect\DTO;

class DatabaseDTO
{
    public array $tables = [];
    public array $relationships = [];
    public array $migrationOrder = [];

    public function __construct(array $data = [])
    {
        $this->tables = $data['tables'] ?? [];
        $this->relationships = $data['relationships'] ?? [];
        $this->migrationOrder = $data['migration_order'] ?? $data['migrationOrder'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'tables' => $this->tables,
            'relationships' => $this->relationships,
            'migration_order' => $this->migrationOrder,
        ];
    }
}
