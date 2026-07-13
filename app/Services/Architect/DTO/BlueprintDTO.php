<?php

namespace App\Services\Architect\DTO;

class BlueprintDTO
{
    public string $business = '';
    public string $database = '';
    public string $technical = '';
    public string $ui = '';
    public string $version = '1.0.0';
    public string $status = 'Draft';

    public function __construct(array $data = [])
    {
        $this->business = $data['business'] ?? '';
        $this->database = $data['database'] ?? '';
        $this->technical = $data['technical'] ?? '';
        $this->ui = $data['ui'] ?? '';
        $this->version = $data['version'] ?? '1.0.0';
        $this->status = $data['status'] ?? 'Draft';
    }

    public function toArray(): array
    {
        return [
            'business' => $this->business,
            'database' => $this->database,
            'technical' => $this->technical,
            'ui' => $this->ui,
            'version' => $this->version,
            'status' => $this->status,
        ];
    }
}
