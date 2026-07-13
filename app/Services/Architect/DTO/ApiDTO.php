<?php

namespace App\Services\Architect\DTO;

class ApiDTO
{
    public array $resources = [];
    public array $errorResponses = [];

    public function __construct(array $data = [])
    {
        $this->resources = $data['resources'] ?? [];
        $this->errorResponses = $data['error_responses'] ?? $data['errorResponses'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'resources' => $this->resources,
            'error_responses' => $this->errorResponses,
        ];
    }
}
