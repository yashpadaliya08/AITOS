<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Architect\ArchitectEngine;
use App\Services\Architect\Engines\ImportEngine;
use App\Services\Architect\DTO\ManifestDto;
use App\Services\Architect\Registry\HookSystem;
use App\Services\Architect\Registry\FrameworkRegistry;
use App\Services\Architect\Utils\AISessionManager;
use Illuminate\Support\Str;
use ZipArchive;

class ExportController extends Controller
{
    /**
     * Download the compiled AITOS project configuration as a ZIP package.
     */
    public function download(Request $request)
    {
        $stateJson = $request->input('project_state');
        if (!$stateJson) {
            return redirect()->back()->with('error', 'No compilation state found.');
        }

        $state = json_decode($stateJson, true);
        if (!$state || !isset($state['projectName'])) {
            return redirect()->back()->with('error', 'Invalid compilation state.');
        }

        // Run the ArchitectEngine pipeline
        $orchestrator  = new ArchitectEngine();
        $pipelineResult = $orchestrator->execute($state);

        $compiledState = $pipelineResult['state'];
        $report        = $pipelineResult['report'];

        $projectName     = trim($compiledState['projectName']);
        $safeProjectName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $projectName ?: 'AITOS_Project');

        HookSystem::doAction('BeforeExport', $compiledState);

        // Create temp file — must be cleaned up even on errors
        $zipFileName = tempnam(sys_get_temp_dir(), 'aitos_') . '.zip';

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Failed to initialize Zip compiler.');
            }

            // ---- 1. Root Manifest & Reports ----
            $manifest = new ManifestDto([
                'projectName'      => $projectName,
                'framework'        => $compiledState['techStack']['framework'] ?? 'laravel',
                'blueprintVersion' => $compiledState['blueprints']['version'] ?? '1.0.0',
            ]);
            $zip->addFromString($safeProjectName . '/.aitos/manifest.json', $manifest->toJson());

            // Derive real readiness values from the actual pipeline report
            $executedCount = count($report['executed_engines'] ?? []);
            $totalEngines  = 13; // total engines in the pipeline
            $warningCount  = count($report['warnings'] ?? []);
            $errorCount    = count($report['errors']   ?? []);
            $readinessPct  = $totalEngines > 0
                ? round(($executedCount / $totalEngines) * 100) . '%'
                : '0%';

            $generationLog = [
                'engines'  => array_map(fn($e) => $e['name'], $report['executed_engines'] ?? []),
                'duration' => round(($report['duration_ms'] ?? 0) / 1000, 2) . 's',
                'warnings' => array_map(fn($w) => $w['message'], $report['warnings'] ?? []),
            ];
            $zip->addFromString($safeProjectName . '/.aitos/reports/generation_log.json',     json_encode($generationLog, JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/reports/pipeline_report.json',    json_encode($report, JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/reports/architecture_review.json', json_encode([
                'status'    => $errorCount > 0 ? 'failed' : ($warningCount > 0 ? 'passed_with_warnings' : 'approved'),
                'reviewer'  => 'AITOS Compliance Gate',
                'timestamp' => date('c'),
                'metrics'   => [
                    'readiness_score' => $readinessPct,
                    'checks_passed'   => $executedCount,
                    'total_checks'    => $totalEngines,
                    'warnings'        => $warningCount,
                    'errors'          => $errorCount,
                ],
            ], JSON_PRETTY_PRINT));

            $zip->addFromString($safeProjectName . '/.aitos/ai_sessions/.gitkeep', '');

            // ---- 2. Root Markdown Files ----
            $docs = $compiledState['documentation'] ?? [];
            $zip->addFromString($safeProjectName . '/README.md',           $docs['README.md']           ?? '# Project');
            $zip->addFromString($safeProjectName . '/START_HERE.md',       $docs['START_HERE.md']       ?? '# Start Here');
            $zip->addFromString($safeProjectName . '/PROJECT_SUMMARY.md',  $docs['PROJECT_SUMMARY.md']  ?? '# Summary');

            // ---- 3. Data JSON Files ----
            $zip->addFromString($safeProjectName . '/.aitos/data/project.json', json_encode([
                'name'        => $projectName,
                'description' => $compiledState['projectDescription'] ?? '',
                'goal'        => $compiledState['projectGoal']        ?? '',
                'version'     => '1.0.0',
                'created_at'  => date('c'),
            ], JSON_PRETTY_PRINT));

            $zip->addFromString($safeProjectName . '/.aitos/data/requirements.json',       json_encode($compiledState['requirements']      ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/knowledge_graph.json',    json_encode($compiledState['knowledgeGraph']     ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/database_schema.json',    json_encode($compiledState['databaseSchema']     ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/api_design.json',         json_encode($compiledState['apiDesign']          ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/ui_blueprint.json',       json_encode($compiledState['uiBlueprint']        ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/project_plan.json',       json_encode($compiledState['projectPlan']        ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/decisions.json',          json_encode($compiledState['decisions']          ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/code_generation_model.json', json_encode($compiledState['codeGenerationModel'] ?? [], JSON_PRETTY_PRINT));
            $zip->addFromString($safeProjectName . '/.aitos/data/rules.json',              json_encode($this->splitLines($compiledState['requirements']['businessRules'] ?? ''), JSON_PRETTY_PRINT));

            // ---- 4. Blueprint JSON Files ----
            $blueprints = $compiledState['blueprints'] ?? [];
            foreach (['business', 'database', 'technical', 'ui'] as $bpKey) {
                $zip->addFromString(
                    $safeProjectName . "/.aitos/blueprints/{$bpKey}_blueprint.json",
                    json_encode([
                        'version'   => $blueprints['version'] ?? '1.0.0',
                        'status'    => $blueprints['status']  ?? 'Draft',
                        'blueprint' => $blueprints[$bpKey]    ?? '',
                    ], JSON_PRETTY_PRINT)
                );
            }

            // ---- 5. Planning ----
            $zip->addFromString($safeProjectName . '/.aitos/planning/project_plan.json', json_encode($compiledState['projectPlan'] ?? [], JSON_PRETTY_PRINT));

            // ---- 6. Context Markdown Files ----
            $contexts = $compiledState['compiledContexts'] ?? [];
            foreach (['CURRENT_CONTEXT.md', 'BACKEND_CONTEXT.md', 'FRONTEND_CONTEXT.md', 'DATABASE_CONTEXT.md', 'ARCHITECTURE_CONTEXT.md', 'TEAM_CONTEXT.md'] as $ctxFile) {
                $zip->addFromString($safeProjectName . '/.aitos/context/' . $ctxFile, $contexts[$ctxFile] ?? "# {$ctxFile}");
            }

            // Workspace memory markdown files
            $workspaceMemory = $compiledState['workspaceState']['memory'] ?? [];
            foreach ($workspaceMemory as $filename => $content) {
                $zip->addFromString($safeProjectName . '/.aitos/context/' . $filename, $content);
            }

            // ---- 6.3 Workspace JSON Files ----
            $workspaceState = $compiledState['workspaceState'] ?? [];
            if (isset($workspaceState['workspace'])) {
                $zip->addFromString($safeProjectName . '/.aitos/workspace/workspace.json',    json_encode($workspaceState['workspace'], JSON_PRETTY_PRINT));
            }
            if (isset($workspaceState['task_contexts'])) {
                $zip->addFromString($safeProjectName . '/.aitos/workspace/task_context.json', json_encode($workspaceState['task_contexts'], JSON_PRETTY_PRINT));
            }

            // ---- 6.4 Handoff ----
            if (isset($workspaceState['handoff'])) {
                $zip->addFromString($safeProjectName . '/.aitos/handoff/handoff.md', $workspaceState['handoff']);
            }

            // ---- 6.5 Prompt Packs ----
            $prompts = $compiledState['promptPacks']['files'] ?? [];
            foreach ($prompts as $filename => $content) {
                $zip->addFromString($safeProjectName . '/.aitos/prompts/' . $filename, $content);
            }

            // ---- 6.6 AI Sessions ----
            $aiSessions = $compiledState['aiSessions'] ?? [];
            if (!empty($aiSessions)) {
                $formattedSessions = AISessionManager::formatSessions($aiSessions);
                foreach ($formattedSessions as $filename => $content) {
                    $zip->addFromString($safeProjectName . '/.aitos/ai_sessions/' . $filename, $content);
                }
            }

            // ---- 6.7 Framework Config Files ----
            $framework = $compiledState['techStack']['framework'] ?? 'laravel';
            $provider  = FrameworkRegistry::get($framework);
            if ($provider) {
                foreach ($provider->getConfigFileTemplates() as $filename => $content) {
                    $zip->addFromString($safeProjectName . '/.aitos/framework/' . $filename, $content);
                }
            }

            // ---- 7. Documentation Markdown ----
            foreach (['README.md', 'START_HERE.md', 'PROJECT_SUMMARY.md', 'ARCHITECTURE.md', 'TEAM_GUIDE.md', 'API_GUIDE.md', 'DATABASE_GUIDE.md'] as $docFile) {
                $zip->addFromString(
                    $safeProjectName . '/.aitos/documentation/' . $docFile,
                    $docs[$docFile] ?? "# {$docFile}"
                );
            }

            // ---- 7.5 Framework Scaffold Files ----
            $scaffold = $compiledState['repositoryScaffold'] ?? [];
            if (!empty($scaffold['files'])) {
                foreach ($scaffold['files'] as $filepath => $filecontent) {
                    $zip->addFromString($safeProjectName . '/' . $filepath, $filecontent);
                }
            }

            // ---- 8. Config Register ----
            $zip->addFromString($safeProjectName . '/.aitos/config/config.json', json_encode([
                'aitos_version'     => '1.5.0',
                'last_compile_date' => date('c'),
                'git_sync_enabled'  => true,
            ], JSON_PRETTY_PRINT));

            $zip->close();

        } catch (\Throwable $e) {
            // Always clean up the temp file even on failure
            if (file_exists($zipFileName)) {
                @unlink($zipFileName);
            }
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }

        HookSystem::doAction('AfterExport', $compiledState);

        return response()->download($zipFileName, $safeProjectName . '_aitos_package.zip')->deleteFileAfterSend(true);
    }

    /**
     * Split a string or array of business rules into a clean array of strings.
     */
    private function splitLines(mixed $text): array
    {
        if (is_array($text)) {
            return $text;
        }

        $lines    = array_map('trim', explode("\n", (string) $text));
        $filtered = array_filter($lines, fn($line) => $line !== '');

        return array_values(array_map(fn($line) => ltrim($line, "\u{2022}*-\t "), $filtered));
    }

    // =========================================================================
    // PRE-COMPILE PROMPT PREVIEW
    // =========================================================================

    /**
     * Preview generated prompt packs for a selected AI model target.
     * Returns JSON with the prompt file contents so the UI can show a live
     * preview before the user customizes and downloads the ZIP.
     */
    public function previewPrompts(Request $request)
    {
        $stateJson = $request->input('project_state');
        $state = is_string($stateJson) ? json_decode($stateJson, true) : $request->all();

        if (!$state || !isset($state['projectName'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing project state.',
            ], 422);
        }

        $modelTarget = $request->input('model_target', 'cursor');

        $generator = new \App\Services\Architect\Engines\PromptPackGenerator();
        $result = $generator->run($state, $modelTarget);

        if (!$result->success) {
            return response()->json([
                'success' => false,
                'message' => 'Prompt generation failed.',
                'errors'  => $result->errors,
            ], 500);
        }

        return response()->json([
            'success'      => true,
            'model_target' => $result->data['model_target'],
            'model_label'  => $result->data['model_label'],
            'prompts'      => $result->data['files'],
            'profiles'     => \App\Services\Architect\Engines\PromptPackGenerator::getModelProfiles(),
        ]);
    }

    // =========================================================================
    // PROJECT BRIEF EXPORT
    // =========================================================================

    /**
     * Generate and download the AITOS Project Brief as a ZIP bundle containing:
     *   - project_brief.html  (rich, interactive HTML with Mermaid diagrams)
     *   - project_brief.pdf   (clean, normal PDF for printing / mentor review)
     *   - diagrams/er_diagram.mmd  (Mermaid source for offline rendering)
     */
    public function downloadBrief(Request $request)
    {
        $stateJson = $request->input('project_state');
        if (!$stateJson) {
            return redirect()->back()->with('error', 'No compilation state found.');
        }

        $state = json_decode($stateJson, true);
        if (!$state || !isset($state['projectName'])) {
            return redirect()->back()->with('error', 'Invalid compilation state.');
        }

        // Run the full ArchitectEngine pipeline to get compiled state
        $orchestrator   = new ArchitectEngine();
        $pipelineResult = $orchestrator->execute($state);
        $compiledState  = $pipelineResult['state'];
        $report         = $pipelineResult['report'];

        // Build the Mermaid ER diagram string from schema + knowledge graph
        $mermaidDiagram = $this->buildMermaidDiagram($compiledState);

        // Flatten helper: ensure a value is always a clean string array
        $toArray = fn($val) => is_array($val)
            ? array_values(array_filter(array_map('trim', $val)))
            : array_values(array_filter(array_map('trim', explode("\n", (string) $val))));

        $projectModel = $compiledState['projectModel'] ?? [];
        $requirements = $compiledState['requirements'] ?? [];

        // Normalise all list fields into proper arrays for the Blade views
        $viewData = [
            'projectName'        => trim($compiledState['projectName'] ?? 'Untitled Project'),
            'projectDescription' => trim($compiledState['projectDescription'] ?? ''),
            'projectGoal'        => trim($compiledState['projectGoal'] ?? ''),
            'generatedAt'        => date('F j, Y \a\t g:i A'),
            'techStack'          => $compiledState['techStack'] ?? [],
            'requirements'       => $requirements,
            'projectModel'       => [
                'project_name'                => $projectModel['project_name']                ?? $compiledState['projectName'] ?? '',
                'description'                 => $projectModel['description']                 ?? '',
                'goal'                        => $projectModel['goal']                        ?? '',
                'entities'                    => $projectModel['entities']                    ?? [],
                'relationships'               => $projectModel['relationships']               ?? [],
                'modules'                     => $toArray($projectModel['modules']            ?? []),
                'roles'                       => $toArray($projectModel['roles']              ?? []),
                'business_rules'              => $toArray($projectModel['business_rules']     ?? $requirements['businessRules'] ?? []),
                'functional_requirements'     => $toArray($projectModel['functional_requirements']     ?? $requirements['functionalRequirements']    ?? []),
                'non_functional_requirements' => $toArray($projectModel['non_functional_requirements'] ?? $requirements['nonFunctionalRequirements'] ?? []),
                'assumptions'                 => $toArray($projectModel['assumptions']        ?? $requirements['assumptions']  ?? []),
                'risks'                       => $toArray($projectModel['risks']              ?? $requirements['risks']        ?? []),
                'user_stories'                => $toArray($projectModel['user_stories']       ?? $requirements['userStories']  ?? []),
                'phases'                      => $toArray($projectModel['phases']             ?? $requirements['implementationPhases'] ?? []),
                'ai_notes'                    => $toArray($projectModel['ai_notes']           ?? $requirements['aiNotes']      ?? []),
                'tech_stack'                  => $projectModel['tech_stack']                  ?? [],
            ],
            'blueprints'     => $compiledState['blueprints']    ?? [],
            'databaseSchema' => $compiledState['databaseSchema'] ?? ['tables' => [], 'relationships' => [], 'migration_order' => []],
            'apiDesign'      => $compiledState['apiDesign']     ?? ['resources' => [], 'error_responses' => []],
            'projectPlan'    => $compiledState['projectPlan']   ?? [],
            'knowledgeGraph' => $compiledState['knowledgeGraph'] ?? ['relations' => []],
            'pipelineReport' => $report,
            'mermaidDiagram' => $mermaidDiagram,
        ];

        $safeProjectName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $viewData['projectName'] ?: 'AITOS_Project');

        // 1) Render the rich HTML brief
        $htmlContent = view('reports.project_brief', $viewData)->render();

        // 2) Render the clean PDF brief using DomPDF
        $pdfHtml = view('reports.project_brief_pdf', $viewData)->render();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($pdfHtml);
        $pdf->setPaper('a4', 'portrait');
        $pdfContent = $pdf->output();

        // 3) Build ZIP bundle
        $zipFileName = tempnam(sys_get_temp_dir(), 'aitos_brief_') . '.zip';

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Failed to create ZIP archive for brief.');
            }

            // Add the rich HTML file
            $zip->addFromString($safeProjectName . '_Project_Brief.html', $htmlContent);

            // Add the clean PDF file
            $zip->addFromString($safeProjectName . '_Project_Brief.pdf', $pdfContent);

            // Add Mermaid diagram source file (if available)
            if (!empty($mermaidDiagram)) {
                $zip->addFromString('diagrams/er_diagram.mmd', $mermaidDiagram);

                // Also add a README for the diagrams folder
                $zip->addFromString('diagrams/README.txt',
                    "ER Diagram (Mermaid Source)\n"
                    . "===========================\n\n"
                    . "The file 'er_diagram.mmd' contains a Mermaid diagram definition.\n\n"
                    . "To render it:\n"
                    . "  1. Open https://mermaid.live and paste the contents\n"
                    . "  2. Or install the Mermaid CLI: npm install -g @mermaid-js/mermaid-cli\n"
                    . "     Then run: mmdc -i er_diagram.mmd -o er_diagram.png\n\n"
                    . "The HTML brief file also renders this diagram automatically\n"
                    . "when opened in a browser with internet access.\n"
                );
            }

            $zip->close();

        } catch (\Throwable $e) {
            if (file_exists($zipFileName)) {
                @unlink($zipFileName);
            }
            return response()->json(['error' => 'Brief export failed: ' . $e->getMessage()], 500);
        }

        return response()->download(
            $zipFileName,
            $safeProjectName . '_Project_Brief.zip'
        )->deleteFileAfterSend(true);
    }

    /**
     * Build a Mermaid erDiagram definition string from the compiled database schema
     * and knowledge graph relations. Limits to 12 tables for readability.
     */
    private function buildMermaidDiagram(array $compiledState): string
    {
        $tables    = $compiledState['databaseSchema']['tables']    ?? [];
        $relations = $compiledState['knowledgeGraph']['relations'] ?? [];

        if (empty($tables)) {
            return '';
        }

        // Cap at 12 tables to keep the diagram readable
        $displayTables = array_slice($tables, 0, 12);
        $tableNames    = array_map(fn($t) => strtoupper($t['name']), $displayTables);

        $lines = ['erDiagram'];

        foreach ($displayTables as $table) {
            $entityName = strtoupper($table['name']);
            $lines[]    = "    {$entityName} {";

            // Only show PK, FK, UNIQUE, and common named columns to keep blocks compact
            $keyColumns = array_filter($table['columns'], function ($col) {
                return ($col['primary_key'] ?? false)
                    || isset($col['foreign_key'])
                    || ($col['unique'] ?? false)
                    || in_array($col['name'], ['name', 'title', 'email', 'status', 'type', 'slug']);
            });

            // If nothing matched, just show the first 3 columns
            if (empty($keyColumns)) {
                $keyColumns = array_slice($table['columns'], 0, 3);
            }

            foreach ($keyColumns as $col) {
                // Extract first word of type and strip parenthetical size specs
                $rawType  = strtolower(explode(' ', $col['type'])[0]);
                $type     = preg_replace('/\(.*\)/', '', $rawType) ?: 'string';
                $colName  = $col['name'];
                $suffix   = '';
                if ($col['primary_key']   ?? false) $suffix .= ' PK';
                if (isset($col['foreign_key']))      $suffix .= ' FK';
                if (($col['unique'] ?? false) && !($col['primary_key'] ?? false)) $suffix .= ' UK';

                $lines[] = "        {$type} {$colName}{$suffix}";
            }

            $lines[] = '    }';
        }

        // Add relationship lines — map source/target to actual pluralised table names
        $addedRelations = [];
        foreach ($relations as $rel) {
            $sourceTable = strtoupper(Str::plural($rel['source'] ?? ''));
            $targetTable = strtoupper(Str::plural($rel['target'] ?? ''));

            $key = "{$sourceTable}|{$targetTable}";

            if (
                !isset($addedRelations[$key])
                && in_array($sourceTable, $tableNames, true)
                && in_array($targetTable, $tableNames, true)
            ) {
                $label           = str_replace('_', ' ', $rel['label'] ?? 'has');
                $lines[]         = "    {$sourceTable} ||--o{ {$targetTable} : \"{$label}\"";
                $addedRelations[$key] = true;
            }
        }

        return implode("\n", $lines);
    }

    // =========================================================================
    // PROJECT IMPORT
    // =========================================================================

    /**
     * Import an AITOS ZIP package and restore the project state.
     * This wires up the existing ImportEngine that was previously unreachable.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:51200', // 50MB max
        ]);

        $file = $request->file('file');
        $tempDir = storage_path('app/temp/import_' . Str::random(8));

        try {
            // Create temp directory
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Move uploaded file to temp location
            $zipPath = $tempDir . '/import.zip';
            $file->move($tempDir, 'import.zip');

            // Use the ImportEngine to parse the ZIP
            $importEngine = new ImportEngine();
            $restoredState = $importEngine->import($zipPath);

            if (empty($restoredState) || !isset($restoredState['projectName'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'The ZIP file does not contain a valid AITOS project package.',
                ], 422);
            }

            // Mark imported state flags
            $restoredState['wizardCompleted'] = true;
            $restoredState['requirementsApproved'] = !empty($restoredState['requirements']);
            $restoredState['blueprintApproved'] = !empty($restoredState['blueprints']);

            return response()->json([
                'success' => true,
                'state'   => $restoredState,
                'message' => 'Project imported successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ], 500);

        } finally {
            // Clean up temp directory
            if (is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
        }
    }

    /**
     * Recursively delete a directory and its contents.
     */
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}
