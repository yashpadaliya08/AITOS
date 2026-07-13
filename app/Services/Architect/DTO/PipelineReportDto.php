<?php

namespace App\Services\Architect\DTO;

class PipelineReportDto
{
    public array $executedEngines = [];
    public float $duration = 0.0;
    public array $warnings = [];
    public array $errors = [];
    public int $completionPercentage = 0;

    public function __construct(array $data = [])
    {
        $this->executedEngines = $data['executedEngines'] ?? [];
        $this->duration = (float) ($data['duration'] ?? 0.0);
        $this->warnings = $data['warnings'] ?? [];
        $this->errors = $data['errors'] ?? [];
        $this->completionPercentage = (int) ($data['completionPercentage'] ?? 0);
    }

    public function toArray(): array
    {
        return [
            'executed_engines' => $this->executedEngines,
            'duration_ms' => $this->duration,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
            'completion_percentage' => $this->completionPercentage,
            'status' => count($this->errors) > 0 ? 'failed' : 'success',
            'timestamp' => date('c')
        ];
    }

    public function toJson(int $options = JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $options);
    }
}
