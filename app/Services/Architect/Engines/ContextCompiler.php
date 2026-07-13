<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\Validators\ContextValidator;

class ContextCompiler implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start        = microtime(true);
        $projectModel = new ProjectModelDTO($state);

        $dbSchema    = $state['databaseSchema']  ?? ['tables' => [], 'relationships' => []];
        $apiDesign   = $state['apiDesign']        ?? ['resources' => []];
        $projectPlan = $state['projectPlan']      ?? [];
        $blueprints  = $state['blueprints']       ?? [];
        $team        = $state['teamMembers']      ?? [];

        $projectName  = $projectModel->projectName;
        $framework    = $projectModel->suggestedTechnologyStack['framework'] ?? ($state['techStack']['framework'] ?? 'N/A');
        $database     = $projectModel->suggestedTechnologyStack['database']  ?? ($state['techStack']['database']  ?? 'N/A');
        $frontend     = $projectModel->suggestedTechnologyStack['frontend']  ?? ($state['techStack']['frontend']  ?? 'N/A');

        // ---- CURRENT_CONTEXT.md ----
        $currentContext  = "# {$projectName} — Current AI Context\n\n";
        $currentContext .= "## Project Summary\n{$projectModel->projectDescription}\n\n";
        $currentContext .= "## Goal\n{$projectModel->projectGoal}\n\n";
        $currentContext .= "## Active Stack\n- Framework: {$framework}\n- Database: {$database}\n- Frontend: {$frontend}\n\n";
        $currentContext .= "## Core Modules\n";
        foreach ($projectModel->modules as $mod) {
            $currentContext .= "- {$mod}\n";
        }
        $currentContext .= "\n## Business Rules\n";
        foreach ($projectModel->businessRules as $rule) {
            $currentContext .= "- {$rule}\n";
        }
        $currentContext .= "\n## Implementation Phases\n";
        foreach ($projectModel->implementationPhases as $phase) {
            $currentContext .= "- {$phase}\n";
        }

        // ---- BACKEND_CONTEXT.md ----
        $backendContext  = "# Backend Development Context — {$projectName}\n\n";
        $backendContext .= "## Framework: {$framework}\n\n";
        $backendContext .= "## Entity Models\n";
        foreach ($projectModel->entities as $entity) {
            $name  = is_array($entity) ? ($entity['name'] ?? '') : $entity;
            $attrs = is_array($entity) ? ($entity['attributes'] ?? []) : [];
            $backendContext .= "### {$name}\n";
            if (!empty($attrs)) {
                $backendContext .= "**Attributes:** " . implode(', ', $attrs) . "\n";
            }
        }
        $backendContext .= "\n## API Resources\n";
        foreach ($apiDesign['resources'] ?? [] as $resource) {
            $backendContext .= "### {$resource['resource']}\n";
            foreach ($resource['endpoints'] ?? [] as $ep) {
                $backendContext .= "- `{$ep['method']} {$ep['path']}` — {$ep['description']}\n";
            }
        }
        $backendContext .= "\n## Business Rules\n";
        foreach ($projectModel->businessRules as $rule) {
            $backendContext .= "- {$rule}\n";
        }
        $backendContext .= "\n## Functional Requirements\n";
        foreach ($projectModel->functionalRequirements as $req) {
            $backendContext .= "- {$req}\n";
        }

        // ---- FRONTEND_CONTEXT.md ----
        $frontendContext  = "# Frontend Development Context — {$projectName}\n\n";
        $frontendContext .= "## Frontend Stack: {$frontend}\n\n";
        $frontendContext .= "## Modules & Pages\n";
        foreach ($projectModel->modules as $mod) {
            $frontendContext .= "- {$mod}\n";
        }
        $frontendContext .= "\n## UI Specification Excerpt\n";
        $frontendContext .= $blueprints['ui'] ?? '_No UI blueprint available yet._';
        $frontendContext .= "\n\n## User Roles\n";
        foreach ($projectModel->roles as $role) {
            $frontendContext .= "- {$role}\n";
        }

        // ---- DATABASE_CONTEXT.md ----
        $databaseContext  = "# Database Context — {$projectName}\n\n";
        $databaseContext .= "## Engine: {$database}\n\n";
        $databaseContext .= "## Tables\n";
        foreach ($dbSchema['tables'] as $table) {
            $databaseContext .= "### `{$table['name']}`\n";
            foreach ($table['columns'] ?? [] as $col) {
                $flags = [];
                if ($col['primary_key'] ?? false)  $flags[] = 'PK';
                if ($col['auto_increment'] ?? false) $flags[] = 'AI';
                if ($col['unique'] ?? false)        $flags[] = 'UNIQUE';
                if (isset($col['foreign_key']))     $flags[] = "FK→{$col['foreign_key']['on']}";
                $flagStr = $flags ? ' [' . implode(', ', $flags) . ']' : '';
                $databaseContext .= "- `{$col['name']}` ({$col['type']}){$flagStr}\n";
            }
            $databaseContext .= "\n";
        }
        $databaseContext .= "## Migration Order\n";
        foreach ($dbSchema['migration_order'] ?? [] as $tbl) {
            $databaseContext .= "1. `{$tbl}`\n";
        }

        // ---- ARCHITECTURE_CONTEXT.md ----
        $architectureContext  = "# Architecture Context — {$projectName}\n\n";
        $architectureContext .= "## Business Blueprint\n" . ($blueprints['business'] ?? '_Not generated._') . "\n\n";
        $architectureContext .= "## Technical Blueprint\n" . ($blueprints['technical'] ?? '_Not generated._') . "\n\n";
        $architectureContext .= "## AI Notes\n";
        foreach ($projectModel->aiNotes as $note) {
            $architectureContext .= "- {$note}\n";
        }
        $architectureContext .= "\n## Non-Functional Requirements\n";
        foreach ($projectModel->nonFunctionalRequirements as $req) {
            $architectureContext .= "- {$req}\n";
        }

        // ---- TEAM_CONTEXT.md ----
        $teamContext  = "# Team Context — {$projectName}\n\n";
        $teamContext .= "## Team Members\n";
        if (empty($team)) {
            $teamContext .= "_No team members assigned yet._\n";
        } else {
            foreach ($team as $member) {
                $role = $member['role'] ?? 'Developer';
                $teamContext .= "- **{$member['name']}** ({$role})\n";
            }
        }
        $teamContext .= "\n## Module Ownership\n";
        foreach ($projectPlan['module_ownership'] ?? [] as $own) {
            $teamContext .= '- **' . $own['module'] . '** → ' . $own['owner'] . ' (' . $own['role'] . ') | Priority: ' . $own['priority'] . ' | Complexity: ' . $own['estimated_complexity'] . "\n";
        }
        $teamContext .= "\n## Development Roadmap\n";
        foreach ($projectPlan['development_order'] ?? [] as $step) {
            $teamContext .= "- {$step}\n";
        }
        $teamContext .= "\n## Assumptions\n";
        foreach ($projectModel->assumptions as $assumption) {
            $teamContext .= "- {$assumption}\n";
        }
        $teamContext .= "\n## Risks\n";
        foreach ($projectModel->risks as $risk) {
            $teamContext .= "- {$risk}\n";
        }

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success'        => true,
            'warnings'       => [],
            'errors'         => [],
            'generated_file' => 'contexts.json',
            'execution_time' => $duration,
            'data'           => [
                'CURRENT_CONTEXT.md'      => $currentContext,
                'BACKEND_CONTEXT.md'      => $backendContext,
                'FRONTEND_CONTEXT.md'     => $frontendContext,
                'DATABASE_CONTEXT.md'     => $databaseContext,
                'ARCHITECTURE_CONTEXT.md' => $architectureContext,
                'TEAM_CONTEXT.md'         => $teamContext,
            ],
        ]);
    }

    public function validate(array $output): array
    {
        return ContextValidator::validate($output);
    }

    public function getName(): string
    {
        return 'ContextCompiler';
    }

    public function getDependencies(): array
    {
        return ['requirements', 'databaseSchema', 'projectPlan'];
    }
}
