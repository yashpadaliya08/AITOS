<?php

namespace App\Services\Architect\DTO;

class EngineResultDto
{
    public bool $success;
    public array $warnings = [];
    public array $errors = [];
    public string $generatedFile = '';
    public float $executionTime = 0.0;
    public array $data = [];

    public function __construct(array $data = [])
    {
        $this->success = (bool) ($data['success'] ?? true);
        $this->warnings = $data['warnings'] ?? [];
        $this->errors = $data['errors'] ?? [];
        $this->generatedFile = $data['generated_file'] ?? '';
        $this->executionTime = (float) ($data['execution_time'] ?? 0.0);
        $this->data = $data['data'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'generated_file' => $this->generatedFile,
            'execution_time' => $this->executionTime,
            'data' => $this->data,
        ];
    }
}
