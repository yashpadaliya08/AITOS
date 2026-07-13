<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\Validators\PlanningValidator;

class PlanningGenerator implements EngineInterface
{
    /**
     * Keywords that indicate high development complexity.
     */
    private const HIGH_COMPLEXITY_KEYWORDS = [
        'auth', 'authentication', 'payment', 'billing', 'import', 'export',
        'integration', 'sync', 'realtime', 'notification', 'search', 'analytics',
        'ml', 'ai', 'security', 'encryption', 'webhook',
    ];

    /**
     * Keywords that indicate medium development complexity.
     */
    private const MEDIUM_COMPLEXITY_KEYWORDS = [
        'report', 'dashboard', 'workflow', 'approval', 'permission', 'role',
        'upload', 'download', 'calendar', 'schedule', 'audit', 'log',
    ];

    public function run(array $state): EngineResultDto
    {
        $start        = microtime(true);
        $projectModel = new ProjectModelDTO($state);

        $members     = $state['teamMembers'] ?? [];
        $modulesList = $projectModel->modules;

        $moduleOwnership = [];
        $devOrder        = [];
        $dependencies    = [];

        foreach ($modulesList as $idx => $module) {
            $cleanName = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $module));
            if (empty($cleanName)) continue;

            // Assign team member round-robin
            $owner = 'Lead Developer';
            $role  = 'Fullstack';
            if (!empty($members)) {
                $m     = $members[$idx % count($members)];
                $owner = $m['name'] ?? 'Lead Developer';
                $role  = $m['role'] ?? 'Developer';
            }

            $moduleOwnership[] = [
                'module'               => $cleanName,
                'owner'                => $owner,
                'role'                 => $role,
                'estimated_complexity' => $this->estimateComplexity($cleanName),
                'priority'             => $this->assignPriority($idx, count($modulesList)),
            ];
        }

        // Build development order
        if (!empty($modulesList)) {
            $devOrder[] = "Phase 1: Project Scaffolding & Shared Auth Setup";
            foreach ($modulesList as $idx => $module) {
                $cleanName = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $module));
                if (!empty($cleanName)) {
                    $devOrder[] = "Phase " . ($idx + 2) . ": Implement Core Workflows for {$cleanName}";
                }
            }
            $finalPhase = count($modulesList) + 2;
            $devOrder[] = "Phase {$finalPhase}: Integration Audits, API Validations, and Production Release Preview";
        } else {
            $devOrder = [
                'Phase 1: Environment Scaffolding Configuration',
                'Phase 2: Database Schema & Authentication Layer Setup',
                'Phase 3: Core CRUD Modules Development',
                'Phase 4: Interface Integration & End-to-End Testing',
            ];
        }

        // Build dependency statements
        if (count($modulesList) >= 2) {
            $first        = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $modulesList[0]));
            $second       = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $modulesList[1]));
            $dependencies[] = "{$second} workflow modules depend on completed {$first} services.";
        }
        $dependencies[] = "All system endpoints depend on basic Authentication configuration.";

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success'        => true,
            'warnings'       => [],
            'errors'         => [],
            'generated_file' => 'project_plan.json',
            'execution_time' => $duration,
            'data'           => [
                'module_ownership'  => $moduleOwnership,
                'development_order' => $devOrder,
                'dependencies'      => $dependencies,
            ],
        ]);
    }

    /**
     * Estimate module complexity based on keyword analysis of the module name.
     * Returns a human-readable complexity label with story point estimate.
     */
    private function estimateComplexity(string $moduleName): string
    {
        $lower = strtolower($moduleName);

        foreach (self::HIGH_COMPLEXITY_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                return 'High (8 points)';
            }
        }

        foreach (self::MEDIUM_COMPLEXITY_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                return 'Medium (5 points)';
            }
        }

        return 'Low (3 points)';
    }

    /**
     * Assign priority based on module position and total module count.
     */
    private function assignPriority(int $index, int $total): string
    {
        if ($index === 0) return 'P0 (Blocker)';
        if ($index === 1) return 'P1 (Critical)';
        if ($total > 4 && $index === $total - 1) return 'P3 (Low)';
        return 'P2 (Normal)';
    }

    public function validate(array $output): array
    {
        return PlanningValidator::validate($output);
    }

    public function getName(): string
    {
        return 'PlanningGenerator';
    }

    public function getDependencies(): array
    {
        return ['projectModel'];
    }
}
