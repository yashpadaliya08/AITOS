<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\DTO\BlueprintDTO;
use App\Services\Architect\Validators\BlueprintValidator;

class BlueprintGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        $projectModel = new ProjectModelDTO($state);
        
        $bpData = $state['blueprints'] ?? [];
        
        // Auto-compile text from model parameters if empty
        if (empty($bpData['business'])) {
            $bpData['business'] = "# Business Blueprint\n\n## Summary\n" . $projectModel->projectDescription . "\n\n## Core Rules\n- " . implode("\n- ", $projectModel->businessRules);
        }
        if (empty($bpData['database'])) {
            $bpData['database'] = "# Database Blueprint\n\n## Target Entities\n- " . implode("\n- ", $projectModel->entities);
        }
        if (empty($bpData['technical'])) {
            $techStack = $projectModel->suggestedTechnologyStack;
            $framework = $techStack['framework'] ?? 'Laravel';
            $database = $techStack['database'] ?? 'SQLite';
            $frontend = $techStack['frontend'] ?? 'Blade';
            $bpData['technical'] = "# Technical Blueprint\n\n- Framework: {$framework}\n- Database: {$database}\n- Frontend: {$frontend}\n\n## Mapped Modules\n- " . implode("\n- ", $projectModel->modules);
        }
        if (empty($bpData['ui'])) {
            $bpData['ui'] = "# UI Blueprint\n\n## Mapped Pages\n- Dashboard\n- " . implode("\n- ", $projectModel->modules);
        }

        $blueprint = new BlueprintDTO($bpData);
        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'blueprints.json',
            'execution_time' => $duration,
            'data' => $blueprint->toArray()
        ]);
    }

    public function validate(array $output): array
    {
        return BlueprintValidator::validate($output);
    }

    public function getName(): string
    {
        return 'BlueprintGenerator';
    }

    public function getDependencies(): array
    {
        return ['projectModel'];
    }
}
