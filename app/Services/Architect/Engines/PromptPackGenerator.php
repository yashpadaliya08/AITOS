<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\Utils\PromptOptimizer;

class PromptPackGenerator implements EngineInterface
{
    /**
     * Supported AI model targets with their formatting profiles.
     */
    public const MODEL_PROFILES = [
        'cursor' => [
            'label'       => 'Cursor AI',
            'icon'        => 'bi-cursor-fill',
            'style'       => 'rules',     // .cursorrules format
            'description' => 'Optimized for Cursor .cursorrules and Composer chat with structured rule blocks.',
        ],
        'claude' => [
            'label'       => 'Claude (Anthropic)',
            'icon'        => 'bi-chat-square-dots-fill',
            'style'       => 'system',    // Claude system prompt XML style
            'description' => 'Formatted as Claude system prompts with XML-tagged context sections for maximum precision.',
        ],
        'gemini' => [
            'label'       => 'Gemini (Google)',
            'icon'        => 'bi-gem',
            'style'       => 'structured', // Gemini structured instructions
            'description' => 'Structured instructions with clear section headers optimized for Gemini large context windows.',
        ],
        'copilot' => [
            'label'       => 'GitHub Copilot',
            'icon'        => 'bi-github',
            'style'       => 'inline',     // Copilot inline comment style
            'description' => 'Inline-comment-style instructions designed for GitHub Copilot Workspace and Chat.',
        ],
    ];

    /**
     * Generate prompt packs for a specific AI model target (or default to 'cursor').
     *
     * @param array  $state       The compiled project state
     * @param string $modelTarget One of: cursor, claude, gemini, copilot
     * @return EngineResultDto
     */
    public function run(array $state, string $modelTarget = ''): EngineResultDto
    {
        $start = microtime(true);

        $modelTarget  = strtolower($modelTarget ?: ($state['promptModelTarget'] ?? 'cursor'));
        $profile      = self::MODEL_PROFILES[$modelTarget] ?? self::MODEL_PROFILES['cursor'];
        $formatStyle  = $profile['style'];

        $projectName = $state['projectName'] ?? 'AITOS_Project';
        $framework   = $state['techStack']['framework'] ?? 'laravel';
        $database    = $state['techStack']['database']  ?? 'sqlite';
        $frontend    = $state['techStack']['frontend']  ?? 'blade';
        $rules       = $state['requirements']['businessRules'] ?? '';
        $blueprints  = $state['blueprints'] ?? [];

        $prompts = [];

        // Sequential prompt steps with role, filename, and instructions
        $steps = [
            ['step' => '01', 'role' => 'Environment Setup',     'file' => '01_environment_setup.md',   'instructions' => "Initialize the project repository, install dependencies ({$framework}), configure the database connection ({$database}), and set up the frontend build pipeline ({$frontend})."],
            ['step' => '02', 'role' => 'Database & Migrations',  'file' => '02_database_migrations.md', 'instructions' => "Write physical migration scripts based on the database schema. Define primary keys, foreign key constraints, indexes, and seed data files. Follow the migration order defined in DATABASE_CONTEXT.md."],
            ['step' => '03', 'role' => 'Models & Repositories',  'file' => '03_models_repositories.md', 'instructions' => "Create Eloquent/ORM model classes for each database entity. Define relationships (hasMany, belongsTo, etc.), fillable/guarded attributes, and repository classes for data access abstraction."],
            ['step' => '04', 'role' => 'API Controllers',        'file' => '04_api_controllers.md',     'instructions' => "Implement RESTful API controllers with CRUD operations. Add request validation, proper HTTP status codes, JSON response formatting, and error handling middleware."],
            ['step' => '05', 'role' => 'Frontend Views',         'file' => '05_frontend_views.md',      'instructions' => "Build the frontend layout, page components, and forms matching the UI blueprint. Implement responsive design, form validation, and API integration for data fetching."],
            ['step' => '06', 'role' => 'Business Logic',         'file' => '06_business_logic.md',      'instructions' => "Implement service classes containing business rules, calculations, and workflow logic. Keep controllers thin by delegating complex operations to these services."],
            ['step' => '07', 'role' => 'Testing',                'file' => '07_testing.md',             'instructions' => "Write automated test suites covering unit tests for models/services, feature tests for API endpoints, and integration tests for critical user workflows."],
            ['step' => '08', 'role' => 'Architecture Review',    'file' => '08_architecture_review.md', 'instructions' => "Audit naming conventions, verify directory structure alignment, validate database normalization, inspect controller permissions, and ensure clean separation of concerns."],
        ];

        foreach ($steps as $stepData) {
            $content = $this->formatForModel(
                $formatStyle,
                $stepData['step'],
                $stepData['role'],
                $projectName,
                $framework,
                $database,
                $frontend,
                $rules,
                $blueprints,
                $stepData['instructions'],
                $profile['label']
            );
            $prompts[$stepData['file']] = PromptOptimizer::optimize($content);
        }

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success'        => true,
            'warnings'       => [],
            'errors'         => [],
            'generated_file' => 'prompts.json',
            'execution_time' => $duration,
            'data'           => [
                'files'        => $prompts,
                'model_target' => $modelTarget,
                'model_label'  => $profile['label'],
            ],
        ]);
    }

    /**
     * Format a prompt according to the target AI model's preferred style.
     */
    private function formatForModel(
        string $style,
        string $step,
        string $role,
        string $projectName,
        string $framework,
        string $database,
        string $frontend,
        string $rules,
        array  $blueprints,
        string $instructions,
        string $modelLabel
    ): string {
        $businessBp  = substr($blueprints['business']  ?? '', 0, 200);
        $technicalBp = substr($blueprints['technical'] ?? '', 0, 200);

        return match ($style) {
            // ── Cursor: .cursorrules block format ──
            'rules' => implode("\n", [
                "# Step {$step}: {$role}",
                "# Target: {$modelLabel}",
                "",
                "## Project",
                "- Name: {$projectName}",
                "- Framework: " . strtoupper($framework),
                "- Database: "  . strtoupper($database),
                "- Frontend: "  . strtoupper($frontend),
                "",
                "## Instructions",
                $instructions,
                "",
                "## Business Rules",
                $rules,
                "",
                "## Reference Blueprints",
                "- Business: {$businessBp}...",
                "- Technical: {$technicalBp}...",
                "",
                "## Workflow",
                "1. Load CURRENT_CONTEXT.md from the .aitos/ workspace.",
                "2. Align code naming to code_generation_model.json mappings.",
                "3. Run validation tests after implementation.",
            ]),

            // ── Claude: XML-tagged system prompt ──
            'system' => implode("\n", [
                "<system>",
                "You are implementing Step {$step}: {$role} for project **{$projectName}**.",
                "Target AI: {$modelLabel}",
                "</system>",
                "",
                "<project_context>",
                "Framework: " . strtoupper($framework),
                "Database: "  . strtoupper($database),
                "Frontend: "  . strtoupper($frontend),
                "</project_context>",
                "",
                "<instructions>",
                $instructions,
                "</instructions>",
                "",
                "<business_rules>",
                $rules,
                "</business_rules>",
                "",
                "<blueprints>",
                "Business: {$businessBp}...",
                "Technical: {$technicalBp}...",
                "</blueprints>",
                "",
                "<workflow>",
                "1. Read CURRENT_CONTEXT.md from the .aitos/ workspace for latest project state.",
                "2. Follow naming conventions defined in code_generation_model.json.",
                "3. Validate implementation against testing.md guidelines.",
                "</workflow>",
            ]),

            // ── Gemini: Structured markdown with clear headers ──
            'structured' => implode("\n", [
                "=== STEP {$step}: {$role} ===",
                "=== Target AI: {$modelLabel} ===",
                "",
                "**Project:** {$projectName}",
                "**Framework:** " . strtoupper($framework),
                "**Database:** "  . strtoupper($database),
                "**Frontend:** "  . strtoupper($frontend),
                "",
                "--- SCOPE INSTRUCTIONS ---",
                $instructions,
                "",
                "--- BUSINESS & ARCHITECTURAL RULES ---",
                $rules,
                "",
                "--- REFERENCE BLUEPRINTS ---",
                "Business: {$businessBp}...",
                "Technical: {$technicalBp}...",
                "",
                "--- NEXT STEPS ---",
                "1. Load CURRENT_CONTEXT.md from the repository workspace.",
                "2. Align code naming styles to code_generation_model.json mappings.",
                "3. Perform unit/integration validations matching testing.md guidelines.",
            ]),

            // ── Copilot: Inline comment-style ──
            'inline' => implode("\n", [
                "// ============================================================",
                "// STEP {$step}: {$role}",
                "// Target: {$modelLabel} | Project: {$projectName}",
                "// ============================================================",
                "//",
                "// STACK: " . strtoupper($framework) . " + " . strtoupper($database) . " + " . strtoupper($frontend),
                "//",
                "// INSTRUCTIONS:",
                "// " . wordwrap($instructions, 90, "\n// ", true),
                "//",
                "// BUSINESS RULES:",
                "// " . wordwrap(is_string($rules) ? $rules : implode('; ', $rules), 90, "\n// ", true),
                "//",
                "// BLUEPRINTS:",
                "// Business: {$businessBp}...",
                "// Technical: {$technicalBp}...",
                "//",
                "// WORKFLOW:",
                "// 1. Load CURRENT_CONTEXT.md from the .aitos/ workspace.",
                "// 2. Follow code_generation_model.json naming conventions.",
                "// 3. Run tests after each implementation block.",
                "// ============================================================",
            ]),

            default => "# Step {$step}: {$role}\n\n{$instructions}\n",
        };
    }

    public function validate(array $output): array
    {
        $errors = [];
        if (empty($output['files']) || !is_array($output['files'])) {
            $errors[] = "Prompt files array is missing.";
        }
        return [
            'success'  => count($errors) === 0,
            'errors'   => $errors,
            'warnings' => [],
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

    /**
     * Return the available model profiles for the UI to consume.
     */
    public static function getModelProfiles(): array
    {
        return self::MODEL_PROFILES;
    }
}
