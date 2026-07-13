<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\Validators\DocumentationValidator;

class DocumentationGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        $projectModel = new ProjectModelDTO($state);
        
        $dbSchema = $state['databaseSchema'] ?? ['tables' => []];
        $apiDesign = $state['apiDesign'] ?? ['resources' => []];
        $projectPlan = $state['projectPlan'] ?? [];
        $blueprints = $state['blueprints'] ?? [];

        // 1. Generate README.md
        $readme = "# {$projectModel->projectName}\n\n{$projectModel->projectDescription}\n\n## Get Started\nRead START_HERE.md to launch development sprints.";
        
        // 2. Generate START_HERE.md
        $startHere = "# Start Here Guide\n\nWelcome to {$projectModel->projectName} starter package.\n\n- Project Goal: {$projectModel->projectGoal}\n- Stack: " . implode(', ', $projectModel->suggestedTechnologyStack) . "\n\nReview database schema under .aitos/data/database_schema.json.";

        // 3. Generate PROJECT_SUMMARY.md
        $summary = "# Project Summary\n\n{$projectModel->projectGoal}\n\n## Target Modules\n- " . implode("\n- ", $projectModel->modules);

        // 4. Generate ARCHITECTURE.md
        $architecture = "# Architecture Specification Guide\n\n## Business Blueprint\n" . ($blueprints['business'] ?? '') . "\n\n## Technical Blueprint\n" . ($blueprints['technical'] ?? '');

        // 5. Generate DATABASE_GUIDE.md
        $dbGuide = "# Database Schema Guide\n\n";
        foreach ($dbSchema['tables'] as $table) {
            $dbGuide .= "### Table: " . $table['name'] . "\n\n| Column | Type | Attributes |\n| --- | --- | --- |\n";
            foreach ($table['columns'] as $col) {
                $attrs = [];
                if ($col['primary_key'] ?? false) {
                    $attrs[] = 'PK';
                }
                if ($col['auto_increment'] ?? false) {
                    $attrs[] = 'AI';
                }
                if ($col['unique'] ?? false) {
                    $attrs[] = 'Unique';
                }
                if (isset($col['foreign_key'])) {
                    $attrs[] = 'FK -> ' . $col['foreign_key']['on'];
                }
                $dbGuide .= "| " . $col['name'] . " | " . $col['type'] . " | " . implode(', ', $attrs) . " |\n";
            }
            $dbGuide .= "\n";
        }

        // 6. Generate API_GUIDE.md
        $apiGuide = "# API Specifications Guide\n\n";
        foreach ($apiDesign['resources'] as $res) {
            $apiGuide .= "### Resource: " . $res['resource'] . "\n\n";
            foreach ($res['endpoints'] as $ep) {
                $apiGuide .= "- **" . $ep['method'] . " " . $ep['path'] . "**\n  *Description*: " . $ep['description'] . "\n";
                if (!empty($ep['validation_rules'])) {
                    $apiGuide .= "  *Validation Rules*:\n";
                    foreach ($ep['validation_rules'] as $field => $rule) {
                        $apiGuide .= "    - `{$field}`: `{$rule}`\n";
                    }
                }
            }
            $apiGuide .= "\n";
        }

        // 7. Generate TEAM_GUIDE.md
        $teamGuide = "# Team Workflow & Sprints Roadmap\n\n## Module Ownerships\n";
        if (!empty($projectPlan['module_ownership'])) {
            foreach ($projectPlan['module_ownership'] as $own) {
                $teamGuide .= "- **" . $own['module'] . "**: Assigned to " . $own['owner'] . " (" . $own['role'] . ") | Complexity: " . $own['estimated_complexity'] . "\n";
            }
        }
        $teamGuide .= "\n## Execution Roadmap\n";
        if (!empty($projectPlan['development_order'])) {
            foreach ($projectPlan['development_order'] as $step) {
                $teamGuide .= "- {$step}\n";
            }
        }

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'documentation.json',
            'execution_time' => $duration,
            'data' => [
                'README.md' => $readme,
                'START_HERE.md' => $startHere,
                'PROJECT_SUMMARY.md' => $summary,
                'ARCHITECTURE.md' => $architecture,
                'TEAM_GUIDE.md' => $teamGuide,
                'API_GUIDE.md' => $apiGuide,
                'DATABASE_GUIDE.md' => $dbGuide
            ]
        ]);
    }

    public function validate(array $output): array
    {
        return DocumentationValidator::validate($output);
    }

    public function getName(): string
    {
        return 'DocumentationGenerator';
    }

    public function getDependencies(): array
    {
        return ['projectModel', 'blueprints', 'databaseSchema', 'apiDesign', 'projectPlan'];
    }
}
