<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\DTO\ManifestDto;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ImportEngine
{
    /**
     * Import a compiled AITOS ZIP package and restore its project state structure.
     *
     * @param string $zipFilePath Absolute path to the uploaded ZIP file
     * @return array Structure: ['success' => bool, 'state' => array, 'message' => string]
     */
    public function import(string $zipFilePath): array
    {
        if (!file_exists($zipFilePath)) {
            return [
                'success' => false,
                'state' => [],
                'message' => 'Zip file does not exist.'
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) !== true) {
            return [
                'success' => false,
                'state' => [],
                'message' => 'Failed to open Zip archive.'
            ];
        }

        // Initialize state variables
        $state = [
            'projectName' => '',
            'projectDescription' => '',
            'projectGoal' => '',
            'techStack' => [
                'framework' => '',
                'database' => '',
                'frontend' => ''
            ],
            'teamMembers' => [],
            'tasks' => [],
            'decisions' => [],
            'requirements' => [
                'entities' => '',
                'modules' => '',
                'roles' => '',
                'businessRules' => '',
                'requirements' => '',
                'nonFunctionalRequirements' => '',
                'assumptions' => '',
                'risks' => '',
                'userStories' => '',
                'suggestedFolderStructure' => '',
                'implementationPhases' => '',
                'aiNotes' => ''
            ],
            'blueprints' => [
                'business' => '',
                'database' => '',
                'technical' => '',
                'ui' => '',
                'version' => '1.0.0',
                'status' => 'Draft'
            ],
            'knowledgeGraph' => ['relations' => []],
            'databaseSchema' => ['tables' => [], 'relationships' => [], 'migration_order' => []],
            'apiDesign' => ['resources' => [], 'error_responses' => []],
            'uiBlueprint' => ['pages' => [], 'navigation' => []],
            'projectPlan' => ['module_ownership' => [], 'development_order' => [], 'dependencies' => []],
            'wizardCompleted' => true,
            'requirementsApproved' => true,
            'blueprintApproved' => true,
            'teamAssigned' => true,
            'contextCompiled' => true
        ];

        try {
            // Find root directory prefix inside ZIP (usually ProjectName/)
            $rootPrefix = '';
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $parts = explode('/', $stat['name']);
                if (count($parts) > 1 && $parts[0] !== '') {
                    $rootPrefix = $parts[0] . '/';
                    break;
                }
            }

            // 1. Read manifest.json from correct .aitos/ path (matches ExportController)
            $manifestContent = $zip->getFromName($rootPrefix . '.aitos/manifest.json');
            if ($manifestContent) {
                $manifest = json_decode($manifestContent, true);
                $state['projectName']              = $manifest['project_name']     ?? 'Imported Project';
                $state['techStack']['framework']   = $manifest['framework']         ?? 'laravel';
                $state['blueprints']['version']    = $manifest['blueprint_version'] ?? '1.0.0';
            }

            // 2. Read decisions.json
            $decisionsContent = $zip->getFromName($rootPrefix . '.aitos/data/decisions.json');
            if ($decisionsContent) {
                $state['decisions'] = json_decode($decisionsContent, true) ?? [];
            }

            // 3. Read team.json
            $teamContent = $zip->getFromName($rootPrefix . '.aitos/data/team.json');
            if ($teamContent) {
                $state['teamMembers'] = json_decode($teamContent, true) ?? [];
            }

            // 4. Read tasks.json
            $tasksContent = $zip->getFromName($rootPrefix . '.aitos/data/tasks.json');
            if ($tasksContent) {
                $state['tasks'] = json_decode($tasksContent, true) ?? [];
            }

            // 5. Read project.json for descriptions
            $projContent = $zip->getFromName($rootPrefix . '.aitos/data/project.json');
            if ($projContent) {
                $proj = json_decode($projContent, true);
                $state['projectDescription'] = $proj['description'] ?? '';
                $state['projectGoal'] = $proj['goal'] ?? '';
            }

            // 6. Read database_schema.json
            $dbSchemaContent = $zip->getFromName($rootPrefix . '.aitos/data/database_schema.json');
            if ($dbSchemaContent) {
                $state['databaseSchema'] = json_decode($dbSchemaContent, true) ?? [];
            }

            // 7. Read api_design.json
            $apiDesignContent = $zip->getFromName($rootPrefix . '.aitos/data/api_design.json');
            if ($apiDesignContent) {
                $state['apiDesign'] = json_decode($apiDesignContent, true) ?? [];
            }

            // 8. Read ui_blueprint.json
            $uiBlueprintContent = $zip->getFromName($rootPrefix . '.aitos/data/ui_blueprint.json');
            if ($uiBlueprintContent) {
                $state['uiBlueprint'] = json_decode($uiBlueprintContent, true) ?? [];
            }

            // 9. Read project_plan.json
            $planContent = $zip->getFromName($rootPrefix . '.aitos/data/project_plan.json');
            if ($planContent) {
                $state['projectPlan'] = json_decode($planContent, true) ?? [];
            }

            // 9.3 Read workspace details — store under workspaceState to match ExportController
            $workspaceContent = $zip->getFromName($rootPrefix . '.aitos/workspace/workspace.json');
            if ($workspaceContent) {
                $state['workspaceState']['workspace'] = json_decode($workspaceContent, true) ?? [];
            }

            // 9.5 Read task contexts — store under workspaceState.task_contexts
            $taskContextsContent = $zip->getFromName($rootPrefix . '.aitos/workspace/task_context.json');
            if ($taskContextsContent) {
                $state['workspaceState']['task_contexts'] = json_decode($taskContextsContent, true) ?? [];
            }

            // 9.7 Parse AI Sessions
            $state['aiSessions'] = [];
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                $name = $stat['name'];
                if (str_contains($name, '.aitos/ai_sessions/') && !str_ends_with($name, '/')) {
                    $sessionFileContent = $zip->getFromIndex($i);
                    $lines = explode("\n", $sessionFileContent);
                    $session = [
                        'provider' => 'Generic AI',
                        'model' => 'Model',
                        'date' => date('Y-m-d H:i'),
                        'developer' => 'Developer',
                        'task' => 'Development',
                        'summary' => '',
                        'status' => 'completed'
                    ];
                    foreach ($lines as $line) {
                        if (str_contains($line, '- **AI Provider:**')) {
                            $session['provider'] = trim(str_replace('- **AI Provider:**', '', $line));
                        } elseif (str_contains($line, '- **AI Model:**')) {
                            $session['model'] = trim(str_replace('- **AI Model:**', '', $line));
                        } elseif (str_contains($line, '- **Date:**')) {
                            $session['date'] = trim(str_replace('- **Date:**', '', $line));
                        } elseif (str_contains($line, '- **Developer/Operator:**')) {
                            $session['developer'] = trim(str_replace('- **Developer/Operator:**', '', $line));
                        } elseif (str_contains($line, '- **Assigned Task:**')) {
                            $session['task'] = trim(str_replace('- **Assigned Task:**', '', $line));
                        } elseif (str_contains($line, '- **Status:**')) {
                            $session['status'] = strtolower(trim(str_replace('- **Status:**', '', $line)));
                        }
                    }
                    $pos = strpos($sessionFileContent, "## Session Summary\n");
                    if ($pos !== false) {
                        $remaining = substr($sessionFileContent, $pos + strlen("## Session Summary\n"));
                        $endPos = strpos($remaining, "## Files Modified\n");
                        if ($endPos !== false) {
                            $session['summary'] = trim(substr($remaining, 0, $endPos));
                        } else {
                            $session['summary'] = trim($remaining);
                        }
                    }
                    $state['aiSessions'][] = $session;
                }
            }

            // 10. Read blueprints JSON files
            $state['blueprints']['business'] = $this->readBlueprintFile($zip, $rootPrefix . '.aitos/blueprints/business_blueprint.json');
            $state['blueprints']['database'] = $this->readBlueprintFile($zip, $rootPrefix . '.aitos/blueprints/database_blueprint.json');
            $state['blueprints']['technical'] = $this->readBlueprintFile($zip, $rootPrefix . '.aitos/blueprints/technical_blueprint.json');
            $state['blueprints']['ui'] = $this->readBlueprintFile($zip, $rootPrefix . '.aitos/blueprints/ui_blueprint.json');

            $zip->close();
            
            return [
                'success' => true,
                'state' => $state,
                'message' => 'Project state successfully restored from AITOS package.'
            ];

        } catch (\Exception $e) {
            $zip->close();
            Log::error("ImportEngine: Import failed: " . $e->getMessage());
            return [
                'success' => false,
                'state' => [],
                'message' => 'Failed to parse ZIP package: ' . $e->getMessage()
            ];
        }
    }

    protected function readBlueprintFile(ZipArchive $zip, string $path): string
    {
        $content = $zip->getFromName($path);
        if ($content) {
            $data = json_decode($content, true);
            return $data['blueprint'] ?? '';
        }
        return '';
    }
}
