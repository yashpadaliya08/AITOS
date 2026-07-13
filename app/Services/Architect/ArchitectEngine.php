<?php

namespace App\Services\Architect;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\PipelineReportDto;
use App\Services\Architect\DTO\ManifestDto;
use App\Services\Architect\Engines\ProjectIntelligenceEngine;
use App\Services\Architect\Engines\BlueprintGenerator;
use App\Services\Architect\Engines\KnowledgeGraphEngine;
use App\Services\Architect\Engines\DatabaseGenerator;
use App\Services\Architect\Engines\ApiGenerator;
use App\Services\Architect\Engines\CodeGenerationEngine;
use App\Services\Architect\Engines\UiGenerator;
use App\Services\Architect\Engines\PlanningGenerator;
use App\Services\Architect\Engines\DocumentationGenerator;
use App\Services\Architect\Engines\ContextCompiler;
use App\Services\Architect\Engines\PromptPackGenerator;
use App\Services\Architect\Engines\WorkspaceEngine;
use App\Services\Architect\Engines\RepositoryGenerator;
use Illuminate\Support\Facades\Log;

class ArchitectEngine
{
    /**
     * The sequential list of architectural engine compilers.
     */
    protected array $pipeline = [];

    public function __construct()
    {
        $this->pipeline = [
            new ProjectIntelligenceEngine(),
            new BlueprintGenerator(),
            new KnowledgeGraphEngine(),
            new DatabaseGenerator(),
            new ApiGenerator(),
            new CodeGenerationEngine(),
            new UiGenerator(),
            new PlanningGenerator(),
            new DocumentationGenerator(),
            new ContextCompiler(),
            new PromptPackGenerator(),
            new WorkspaceEngine(),
            new RepositoryGenerator(),
        ];
    }

    /**
     * Run the compilation pipeline on the active project state.
     *
     * @param array $state The active project state
     * @return array Structure: ['success' => bool, 'state' => array, 'report' => array]
     */
    public function execute(array $state): array
    {
        $startTime = microtime(true);
        $executedEngines = [];
        $errors = [];
        $warnings = [];
        
        // Define produced state keys to track dependency resolution
        $produced = array_keys($state);

        foreach ($this->pipeline as $engine) {
            $engineName = $engine->getName();
            $engineStart = microtime(true);

            Log::info("ArchitectEngine: Starting stage {$engineName}");

            // 1. Dependency Validation Check
            $dependencies = $engine->getDependencies();
            $missing = array_diff($dependencies, $produced);
            if (!empty($missing)) {
                $errorMsg = "Dependency check failed for {$engineName}. Missing keys: " . implode(', ', $missing);
                Log::error("ArchitectEngine: {$errorMsg}");
                $errors[] = [
                    'engine' => $engineName,
                    'message' => $errorMsg
                ];
                break;
            }

            try {
                // 2. Run Engine Compilation - returns EngineResultDto
                $result = $engine->run($state);

                // 3. Validation Layer Check on DTO data payload
                $validation = $engine->validate($result->data);
                
                if (!empty($validation['warnings'])) {
                    foreach ($validation['warnings'] as $warning) {
                        $warnings[] = [
                            'engine' => $engineName,
                            'message' => $warning
                        ];
                    }
                }

                if (!$result->success || (isset($validation['success']) && !$validation['success'])) {
                    $errMsgs = array_merge($result->errors, $validation['errors'] ?? []);
                    $errors[] = [
                        'engine' => $engineName,
                        'message' => "Validation/Execution failed: " . implode(', ', $errMsgs)
                    ];
                    Log::error("ArchitectEngine: Validation failed for stage {$engineName}");
                    break;
                }

                // 4. Update state variables and produced list
                $stateKey = $this->getStateKeyForEngine($engineName);
                if ($stateKey) {
                    $state[$stateKey] = $result->data;
                    $produced[] = $stateKey;
                }

                $duration = (microtime(true) - $engineStart) * 1000;
                $executedEngines[] = [
                    'name' => $engineName,
                    'status' => 'completed',
                    'duration_ms' => $duration
                ];

                Log::info("ArchitectEngine: Stage {$engineName} completed in {$duration}ms");

                // 5. Dispatch Stage Completed Domain Event
                $this->dispatchEventForEngine($engineName, $state);

            } catch (\Exception $e) {
                $duration = (microtime(true) - $engineStart) * 1000;
                $executedEngines[] = [
                    'name' => $engineName,
                    'status' => 'failed',
                    'duration_ms' => $duration
                ];
                $errors[] = [
                    'engine' => $engineName,
                    'message' => "Execution exception: " . $e->getMessage()
                ];
                Log::error("ArchitectEngine: Stage {$engineName} failed: " . $e->getMessage());
                break;
            }
        }

        $totalDuration = (microtime(true) - $startTime) * 1000;
        $totalStages = count($this->pipeline);
        $completedStages = count($executedEngines);
        $completionPercentage = $totalStages > 0 ? (int) (($completedStages / $totalStages) * 100) : 0;

        $report = new PipelineReportDto([
            'executedEngines' => $executedEngines,
            'duration' => $totalDuration,
            'warnings' => $warnings,
            'errors' => $errors,
            'completionPercentage' => $completionPercentage
        ]);

        return [
            'success' => count($errors) === 0,
            'state' => $state,
            'report' => $report->toArray()
        ];
    }

    /**
     * Map Engine Class names to their respective Project State property keys.
     */
    protected function getStateKeyForEngine(string $engineName): ?string
    {
        return match ($engineName) {
            'ProjectIntelligenceEngine' => 'projectModel',
            'BlueprintGenerator' => 'blueprints',
            'KnowledgeGraphEngine' => 'knowledgeGraph',
            'DatabaseGenerator' => 'databaseSchema',
            'ApiGenerator' => 'apiDesign',
            'CodeGenerationEngine' => 'codeGenerationModel',
            'UiGenerator' => 'uiBlueprint',
            'PlanningGenerator' => 'projectPlan',
            'DocumentationGenerator' => 'documentation',
            'ContextCompiler' => 'compiledContexts',
            'PromptPackGenerator' => 'promptPacks',
            'WorkspaceEngine' => 'workspaceState',
            'RepositoryGenerator' => 'repositoryScaffold',
            default => null
        };
    }

    /**
     * Dispatch matching domain events.
     */
    protected function dispatchEventForEngine(string $engineName, array $state): void
    {
        $eventClass = match ($engineName) {
            'BlueprintGenerator' => \App\Services\Architect\Events\BlueprintGenerated::class,
            'KnowledgeGraphEngine' => \App\Services\Architect\Events\RequirementsApproved::class,
            'DatabaseGenerator' => \App\Services\Architect\Events\DatabaseGenerated::class,
            'ApiGenerator' => \App\Services\Architect\Events\ApiGenerated::class,
            'CodeGenerationEngine' => \App\Services\Architect\Events\CodeGenerationCompleted::class,
            'UiGenerator' => \App\Services\Architect\Events\UiGenerated::class,
            'PlanningGenerator' => \App\Services\Architect\Events\PlanningGenerated::class,
            'DocumentationGenerator' => \App\Services\Architect\Events\DocumentationGenerated::class,
            'ContextCompiler' => \App\Services\Architect\Events\ContextCompiled::class,
            'PromptPackGenerator' => \App\Services\Architect\Events\PromptPackGenerated::class,
            'WorkspaceEngine' => \App\Services\Architect\Events\WorkspacePrepared::class,
            'RepositoryGenerator' => \App\Services\Architect\Events\RepositoryGenerated::class,
            default => null
        };

        if ($eventClass) {
            event(new $eventClass($state));
        }
    }
}
