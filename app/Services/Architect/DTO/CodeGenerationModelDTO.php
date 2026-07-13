<?php

namespace App\Services\Architect\DTO;

class CodeGenerationModelDTO
{
    /**
     * Array of entity mappings:
     * [
     *   [
     *     'entity' => 'User',
     *     'model' => 'User',
     *     'controller' => 'UserController',
     *     'route' => 'users',
     *     'migration' => 'create_users_table',
     *     'service' => 'UserService',
     *     'repository' => 'UserRepository'
     *   ],
     *   ...
     * ]
     */
    public array $mappings = [];

    public function __construct(array $data)
    {
        $this->mappings = $data['mappings'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'mappings' => $this->mappings
        ];
    }
}
