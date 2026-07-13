<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;

class WorkspaceEngine implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);

        $projectName = $state['projectName'] ?? 'AITOS_Project';
        $team = $state['teamMembers'] ?? [];
        $tasks = $state['tasks'] ?? [];
        $decisions = $state['decisions'] ?? [];
        $framework = $state['techStack']['framework'] ?? 'laravel';
        $tables = $state['databaseSchema']['tables'] ?? [];
        
        // 1. Generate workspace.json categories
        $currentTasks = [];
        $completedTasks = [];
        $blockedTasks = [];
        foreach ($tasks as $t) {
            $col = strtolower($t['column'] ?? '');
            if ($col === 'completed' || $col === 'done') {
                $completedTasks[] = $t;
            } elseif ($col === 'blocked') {
                $blockedTasks[] = $t;
            } else {
                $currentTasks[] = $t;
            }
        }

        $workspaceJson = [
            'members' => $team,
            'current_tasks' => $currentTasks,
            'completed_tasks' => $completedTasks,
            'blocked_tasks' => $blockedTasks,
            'current_sprint' => 'Sprint 1',
            'current_phase' => 'Development Phase',
            'dependencies' => []
        ];

        // 2. Generate task_context.json for developers/AIs
        $taskContexts = [];
        foreach ($tasks as $t) {
            $taskText = $t['text'] ?? 'Task';
            $taskContexts[] = [
                'task' => $taskText,
                'description' => 'Implementation task: ' . $taskText,
                'dependencies' => [],
                'required_files' => [],
                'related_modules' => [],
                'related_api' => [],
                'related_database_tables' => [],
                'required_documentation' => []
            ];
        }

        // 3. Generate handoff.md
        $handoffMd = "# AI Development Handoff Report\n\n" .
            "Allow another AI tool or teammate to continue working immediately on project **{$projectName}**.\n\n" .
            "## Current Sprint Progress\n" .
            "- **Sprint:** Sprint 1\n" .
            "- **Active Tasks Count:** " . count($currentTasks) . "\n" .
            "- **Completed Tasks Count:** " . count($completedTasks) . "\n" .
            "- **Blocked Tasks Count:** " . count($blockedTasks) . "\n\n" .
            "## Decisions Mapped\n";
        
        if (empty($decisions)) {
            $handoffMd .= "- No decisions recorded yet.\n";
        } else {
            foreach ($decisions as $d) {
                $handoffMd .= "- **" . ($d['title'] ?? 'Decision') . "**: " . ($d['desc'] ?? '') . "\n";
            }
        }

        $handoffMd .= "\n## Suggested Next Prompts\n" .
            "1. `Read CURRENT_CONTEXT.md to begin implementing active sprint tasks.`\n" .
            "2. `Review requirements.json to verify entity models configurations.`\n";

        // 4. Generate Project Memory files under .aitos/context/
        $workspaceSummary = "# Workspace Summary\n\nActive developer environment for project {$projectName}.\nFramework: " . strtoupper($framework);
        $currentState = "# Current State\n\nActive Sprint Tasks: " . count($currentTasks) . "\nCompleted Tasks: " . count($completedTasks);
        $nextSteps = "# Next Steps\n\n1. Complete active models scaffolding.\n2. Bind API routing definitions.";
        $teamNotes = "# Team Collaboration Notes\n\nActive Developer counts: " . count($team) . " members.";
        $archSummary = "# Architecture Summary\n\nTarget Stack: " . strtoupper($framework) . "\nEntity counts: " . count($tables) . " models mapped.";

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'workspace_details.json',
            'execution_time' => $duration,
            'data' => [
                'workspace' => $workspaceJson,
                'task_contexts' => $taskContexts,
                'handoff' => $handoffMd,
                'memory' => [
                    'workspace_summary.md' => $workspaceSummary,
                    'current_state.md' => $currentState,
                    'next_steps.md' => $nextSteps,
                    'team_notes.md' => $teamNotes,
                    'architecture_summary.md' => $archSummary
                ]
            ]
        ]);
    }

    public function validate(array $output): array
    {
        return [
            'success' => isset($output['workspace']) && isset($output['handoff']),
            'errors' => [],
            'warnings' => []
        ];
    }

    public function getName(): string
    {
        return 'WorkspaceEngine';
    }

    public function getDependencies(): array
    {
        return ['requirements', 'techStack'];
    }
}
