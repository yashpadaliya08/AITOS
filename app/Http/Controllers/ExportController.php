<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ZipArchive;

class ExportController extends Controller
{
    /**
     * Download the compiled AITOS project configuration as a ZIP package.
     */
    public function download(Request $request)
    {
        // Parse the sent JSON payload
        $stateJson = $request->input('project_state');
        if (!$stateJson) {
            return redirect()->back()->with('error', 'No compilation state found.');
        }

        $state = json_decode($stateJson, true);
        if (!$state || !isset($state['projectName'])) {
            return redirect()->back()->with('error', 'Invalid compilation state.');
        }

        $projectName = trim($state['projectName']);
        // Sanitize project name for directory use
        $safeProjectName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $projectName ?: 'AITOS_Project');
        
        // Setup temporary file path
        $zipFileName = tempnam(sys_get_temp_dir(), 'aitos_') . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['error' => 'Failed to initialize Zip compiler.'], 500);
        }

        // 1. Core Markdown Files in root folder
        $readmeContent = $this->buildReadme($state);
        $startHereContent = $this->buildStartHere($state);
        $summaryContent = $this->buildSummary($state);

        $zip->addFromString($safeProjectName . '/README.md', $readmeContent);
        $zip->addFromString($safeProjectName . '/START_HERE.md', $startHereContent);
        $zip->addFromString($safeProjectName . '/PROJECT_SUMMARY.md', $summaryContent);

        // 2. Data Register Files (.aitos/data/)
        $zip->addFromString($safeProjectName . '/.aitos/data/project.json', json_encode([
            'name' => $state['projectName'],
            'description' => $state['projectDescription'],
            'goal' => $state['projectGoal'],
            'version' => '1.0.0',
            'created_at' => date('c')
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $zip->addFromString($safeProjectName . '/.aitos/data/business_blueprint.json', json_encode([
            'version' => '1.0.0',
            'blueprint' => $state['blueprints']['business'] ?? ''
        ], JSON_PRETTY_PRINT));

        $zip->addFromString($safeProjectName . '/.aitos/data/database_blueprint.json', json_encode([
            'version' => '1.0.0',
            'blueprint' => $state['blueprints']['database'] ?? ''
        ], JSON_PRETTY_PRINT));

        $zip->addFromString($safeProjectName . '/.aitos/data/technical_blueprint.json', json_encode([
            'version' => '1.0.0',
            'blueprint' => $state['blueprints']['technical'] ?? ''
        ], JSON_PRETTY_PRINT));

        $zip->addFromString($safeProjectName . '/.aitos/data/ui_blueprint.json', json_encode([
            'version' => '1.0.0',
            'blueprint' => $state['blueprints']['ui'] ?? ''
        ], JSON_PRETTY_PRINT));

        $zip->addFromString($safeProjectName . '/.aitos/data/team.json', json_encode($state['teamMembers'] ?? [], JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/tasks.json', json_encode($state['tasks'] ?? [], JSON_PRETTY_PRINT));

        $zip->addFromString($safeProjectName . '/.aitos/data/rules.json', json_encode($this->splitLines($state['requirements']['businessRules'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/user_stories.json', json_encode($this->splitLines($state['requirements']['userStories'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/non_functional_requirements.json', json_encode($this->splitLines($state['requirements']['nonFunctionalRequirements'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/suggested_folder_structure.json', json_encode($this->splitLines($state['requirements']['suggestedFolderStructure'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/implementation_phases.json', json_encode($this->splitLines($state['requirements']['implementationPhases'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/ai_notes.json', json_encode($this->splitLines($state['requirements']['aiNotes'] ?? ''), JSON_PRETTY_PRINT));
        $zip->addFromString($safeProjectName . '/.aitos/data/decisions.json', json_encode($state['decisions'] ?? [], JSON_PRETTY_PRINT));

        // 3. AI Context Markdown Layers (.aitos/context/)
        $currentContext = $this->buildCurrentContext($state);
        $backendContext = $this->buildBackendContext($state);
        $frontendContext = $this->buildFrontendContext($state);
        $databaseContext = $this->buildDatabaseContext($state);
        $genericContext = $this->buildGenericContext($state);

        $zip->addFromString($safeProjectName . '/.aitos/context/CURRENT_CONTEXT.md', $currentContext);
        $zip->addFromString($safeProjectName . '/.aitos/context/BACKEND_CONTEXT.md', $backendContext);
        $zip->addFromString($safeProjectName . '/.aitos/context/FRONTEND_CONTEXT.md', $frontendContext);
        $zip->addFromString($safeProjectName . '/.aitos/context/DATABASE_CONTEXT.md', $databaseContext);
        $zip->addFromString($safeProjectName . '/.aitos/context/GENERIC_CONTEXT.md', $genericContext);

        // 4. Config register (.aitos/config/config.json)
        $zip->addFromString($safeProjectName . '/.aitos/config/config.json', json_encode([
            'aitos_version' => '1.0.0',
            'last_compile_date' => date('c'),
            'git_sync_enabled' => $state['config']['git_sync_enabled'] ?? true
        ], JSON_PRETTY_PRINT));

        // 5. Snapshot backup (.aitos/snapshots/v1.0.0-snapshot.json)
        $zip->addFromString($safeProjectName . '/.aitos/snapshots/v1.0.0-snapshot.json', json_encode([
            'snapshot_id' => 'snap-v1.0.0',
            'timestamp' => date('c'),
            'blueprint_version' => '1.0.0',
            'project_name' => $projectName,
            'md5' => md5($stateJson)
        ], JSON_PRETTY_PRINT));

        $zip->close();

        // Send file stream response, and register callback to remove temp zip
        return response()->download($zipFileName, $safeProjectName . '_aitos_package.zip')->deleteFileAfterSend(true);
    }

    private function buildReadme($state)
    {
        return "# " . $state['projectName'] . "\n\n" .
            $state['projectDescription'] . "\n\n" .
            "## Development Stack\n" .
            "- **Framework:** " . ($state['techStack']['framework'] ?? 'Laravel') . "\n" .
            "- **Database:** " . ($state['techStack']['database'] ?? 'SQLite') . "\n" .
            "- **Frontend:** " . ($state['techStack']['frontend'] ?? 'Blade') . "\n\n" .
            "## AITOS Context System\n" .
            "This repository is pre-configured with AITOS (AI Team Operating System) context layers.\n" .
            "The `.aitos/` directory holds structured project requirements, blueprints, planning data, and optimized markdown summaries for AI-assisted development tools.\n\n" .
            "Please review `START_HERE.md` to trigger AI workspace alignments.\n";
    }

    private function buildStartHere($state)
    {
        return "# AITOS Workspace Alignment\n\n" .
            "> **AITOS Philosophy:** Humans Decide. AI Builds. AITOS Remembers.\n\n" .
            "Welcome, AI Developer Agent. You have been opened in this workspace. Before writing code or proposing edits, synchronize your parameters with this repository context.\n\n" .
            "### Synchronization Instructions\n" .
            "1. Load `PROJECT_SUMMARY.md` to understand the product vision.\n" .
            "2. Read files inside `.aitos/context/` directory to align core technology scopes:\n" .
            "   - `CURRENT_CONTEXT.md` for your active task list and feature assignments.\n" .
            "   - `BACKEND_CONTEXT.md` for entities, models, and databases.\n" .
            "   - `FRONTEND_CONTEXT.md` for CSS, bootstrap grids, and layouts.\n" .
            "3. Consult `.aitos/data/rules.json` to verify business logic constraints.\n\n" .
            "Do not create files or change folder routing outside approved specifications.\n";
    }

    private function buildSummary($state)
    {
        return "# Project Summary: " . $state['projectName'] . "\n\n" .
            "## Problem Statement\n" .
            "```text\n" .
            $state['problemStatement'] . "\n" .
            "```\n\n" .
            "## System Requirements Overview\n" .
            ($state['requirements']['requirements'] ?? '') . "\n\n" .
            "## Risks & Assumptions\n" .
            "### Assumptions\n" .
            ($state['requirements']['assumptions'] ?? '') . "\n\n" .
            "### Risks & Mitigations\n" .
            ($state['requirements']['risks'] ?? '') . "\n";
    }

    private function buildCurrentContext($state)
    {
        $tasksStr = '';
        if (isset($state['tasks'])) {
            foreach ($state['tasks'] as $t) {
                $tasksStr .= "- [ ] **" . strtoupper($t['column']) . "**: " . $t['text'] . "\n";
            }
        }

        $decisionsStr = '';
        if (isset($state['decisions'])) {
            foreach ($state['decisions'] as $d) {
                $decisionsStr .= "- **" . $d['date'] . "** *" . $d['title'] . "*: " . $d['desc'] . "\n";
            }
        }

        return "# Current Alignment Context\n\n" .
            "This file details the assigned development boundaries.\n\n" .
            "## Active Sprint Tasks\n" .
            $tasksStr . "\n" .
            "## Decision History Summary\n" .
            $decisionsStr;
    }

    private function buildBackendContext($state)
    {
        return "# Backend Context Layer\n\n" .
            "Contains model logic, API routes, and schema structures.\n\n" .
            "## Technology Stack\n" .
            "- Core: " . ($state['techStack']['framework'] ?? '') . "\n" .
            "- Database: " . ($state['techStack']['database'] ?? '') . "\n\n" .
            "## Entities Map\n" .
            ($state['requirements']['entities'] ?? '') . "\n\n" .
            "## Modules Map\n" .
            ($state['requirements']['modules'] ?? '') . "\n\n" .
            "## Database Specifications\n" .
            ($state['blueprints']['database'] ?? '') . "\n";
    }

    private function buildFrontendContext($state)
    {
        return "# Frontend Context Layer\n\n" .
            "Contains layout structures, CSS frameworks, templates, and styling.\n\n" .
            "## Design Frameworks\n" .
            "- Layouts: " . ($state['techStack']['frontend'] ?? '') . "\n" .
            "- Icons: Bootstrap Icons (CDN)\n\n" .
            "## User Interface Specifications\n" .
            ($state['blueprints']['ui'] ?? '') . "\n";
    }

    private function buildDatabaseContext($state)
    {
        return "# Database Schema Context\n\n" .
            "## Design Schema\n" .
            ($state['blueprints']['database'] ?? '') . "\n";
    }

    private function buildGenericContext($state)
    {
        return "# Generic Development Guidelines\n\n" .
            "1. **Strict Coding Standards:** Maintain standard formatting rules matching the primary framework convention.\n" .
            "2. **Commit Policy:** Commit regularly. Each commit message must start with a context marker matching the active module (e.g. `feat(auth)`).\n" .
            "3. **No Overwrites:** Do not rewrite files or delete sections unless specified. Write modular additions.\n";
    }

    private function splitLines($text)
    {
        $lines = array_map('trim', explode("\n", $text));
        $filtered = array_filter($lines, function($line) {
            return $line !== '';
        });
        return array_values(array_map(function($line) {
            return ltrim($line, "•*-\t ");
        }, $filtered));
    }
}
