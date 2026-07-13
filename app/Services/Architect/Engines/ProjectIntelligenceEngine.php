<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;

class ProjectIntelligenceEngine implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        
        // Normalize requirements into standard model structures
        $projectModel = new ProjectModelDTO($state);
        
        $duration = (microtime(true) - $start) * 1000;
        
        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'project_model.json',
            'execution_time' => $duration,
            'data' => $projectModel->toArray()
        ]);
    }

    public function validate(array $output): array
    {
        $errors = [];
        $warnings = [];

        if (empty($output['project_name'])) {
            $errors[] = "Project Name is missing in project brain model.";
        }
        if (empty($output['entities'])) {
            $warnings[] = "Project brain model contains no entity listings.";
        }

        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    public function getName(): string
    {
        return 'ProjectIntelligenceEngine';
    }

    public function getDependencies(): array
    {
        return ['requirements'];
    }
}
