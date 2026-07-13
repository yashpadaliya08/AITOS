<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\Utils\PromptOptimizer;

class PromptPackGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        $projectName = $state['projectName'] ?? 'AITOS_Project';
        $framework = $state['techStack']['framework'] ?? 'laravel';
        $database = $state['techStack']['database'] ?? 'sqlite';
        $frontend = $state['techStack']['frontend'] ?? 'blade';
        $rules = $state['requirements']['businessRules'] ?? '';
        $blueprints = $state['blueprints'] ?? [];

        $prompts = [];

        // Common template builder incorporating Context, Blueprints, Rules and Next Steps
        $buildPrompt = function($role, $instructions) use ($projectName, $framework, $database, $frontend, $rules, $blueprints) {
            $raw = "=== {$role} DEVELOPMENT PROMPT PACK ===\n\n" .
                "You are implementing the {$role} layer for project: **{$projectName}**.\n" .
                "Target Framework: " . strtoupper($framework) . "\n" .
                "Target Database: " . strtoupper($database) . "\n" .
                "Target Frontend: " . strtoupper($frontend) . "\n\n" .
                "## Active Scope Instructions\n" .
                "{$instructions}\n\n" .
                "## Global Business & Architectural Rules\n" .
                "{$rules}\n\n" .
                "## Target Blueprints\n" .
                "- Business: " . substr($blueprints['business'] ?? '', 0, 150) . "...\n" .
                "- Technical: " . substr($blueprints['technical'] ?? '', 0, 150) . "...\n\n" .
                "## Next Steps\n" .
                "1. Load CURRENT_CONTEXT.md from the repository workspace.\n" .
                "2. Align code naming styles to code_generation_model.json mappings.\n" .
                "3. Perform unit/integration validations matching testing.md guidelines.\n";

            return PromptOptimizer::optimize($raw);
        };

        // 1. backend.md
        $prompts['backend.md'] = $buildPrompt("Backend", "Implement the data objects services, Eloquent or SQLAlchemy schemas, controllers, and repository query parameters.");
        
        // 2. frontend.md
        $prompts['frontend.md'] = $buildPrompt("Frontend", "Build the layout scripts, Tailwind or CSS stylesheet grids, and pages components matching the UI structures.");

        // 3. database.md
        $prompts['database.md'] = $buildPrompt("Database", "Write physical migrations scripts, key relations definitions, index optimizations, and SQL seed files.");

        // 4. api.md
        $prompts['api.md'] = $buildPrompt("API Design", "Configure REST route endpoints, payload validators, JSON structures, status codes, and error handlers.");

        // 5. testing.md
        $prompts['testing.md'] = $buildPrompt("Testing", "Construct automated test coverage suites, mock database connections, and assert JSON body responses.");

        // 6. review.md
        $prompts['review.md'] = $buildPrompt("Architecture Review", "Audit naming styles, verify directory hierarchy alignments, assert database normalization keys, and inspect controllers permissions.");

        // 7. bugfix.md
        $prompts['bugfix.md'] = $buildPrompt("Bugfix", "Analyze system logs, capture runtime database errors, trace stack parameters, and repair logic errors.");

        // 8. architecture.md
        $prompts['architecture.md'] = $buildPrompt("Architecture", "Examine modular designs, verify design patterns boundaries, layout interfaces, and maintain clean separation of concerns.");

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'prompts.json',
            'execution_time' => $duration,
            'data' => [
                'files' => $prompts
            ]
        ]);
    }

    public function validate(array $output): array
    {
        $errors = [];
        if (empty($output['files']) || !is_array($output['files'])) {
            $errors[] = "Prompt files array is missing.";
        }
        return [
            'success' => count($errors) === 0,
            'errors' => $errors,
            'warnings' => []
        ];
    }

    public function getName(): string
    {
        return 'PromptPackGenerator';
    }

    public function getDependencies(): array
    {
        return ['requirements', 'techStack', 'compiledContexts'];
    }
}
