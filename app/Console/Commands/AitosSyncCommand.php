<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Architect\Engines\ContextCompiler;

class AitosSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aitos:sync 
                            {--path= : Path to target repository directory containing .aitos folder}
                            {--complete= : Mark a specific task ID or name as completed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize and dynamically update AI context files (.aitos/context/)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDir = rtrim($this->option('path') ?: base_path(), '/\\');
        $aitosDir  = $targetDir . DIRECTORY_SEPARATOR . '.aitos';

        if (!is_dir($aitosDir)) {
            $this->error("No '.aitos' folder found at: {$targetDir}");
            $this->line("Run this command inside a directory initialized by AITOS or pass --path=/path/to/project");
            return Command::FAILURE;
        }

        $this->info("🔄 Reading AITOS state from: {$aitosDir}");

        // Load existing state files
        $state = [];
        $projectJsonPath = $aitosDir . '/data/project.json';
        if (file_exists($projectJsonPath)) {
            $projectData = json_decode(file_get_contents($projectJsonPath), true) ?: [];
            $state['projectName'] = $projectData['name'] ?? 'AITOS_Project';
            $state['projectDescription'] = $projectData['description'] ?? '';
            $state['projectGoal'] = $projectData['goal'] ?? '';
        }

        $requirementsJsonPath = $aitosDir . '/data/requirements.json';
        if (file_exists($requirementsJsonPath)) {
            $state['requirements'] = json_decode(file_get_contents($requirementsJsonPath), true) ?: [];
        }

        $schemaJsonPath = $aitosDir . '/data/database_schema.json';
        if (file_exists($schemaJsonPath)) {
            $state['databaseSchema'] = json_decode(file_get_contents($schemaJsonPath), true) ?: [];
        }

        $apiJsonPath = $aitosDir . '/data/api_design.json';
        if (file_exists($apiJsonPath)) {
            $state['apiDesign'] = json_decode(file_get_contents($apiJsonPath), true) ?: [];
        }

        $planJsonPath = $aitosDir . '/data/project_plan.json';
        if (file_exists($planJsonPath)) {
            $state['projectPlan'] = json_decode(file_get_contents($planJsonPath), true) ?: [];
        }

        // Handle --complete option
        $completedTaskId = $this->option('complete');
        if ($completedTaskId && isset($state['projectPlan']['tasks'])) {
            foreach ($state['projectPlan']['tasks'] as &$task) {
                if (($task['id'] ?? null) === $completedTaskId || ($task['name'] ?? null) === $completedTaskId) {
                    $task['status'] = 'completed';
                    $task['completed_at'] = date('c');
                    $this->info("✅ Marked task '{$completedTaskId}' as completed.");
                }
            }
            unset($task);
            // Save updated plan
            file_put_contents($planJsonPath, json_encode($state['projectPlan'], JSON_PRETTY_PRINT));
        }

        // Re-compile context
        $compiler = new ContextCompiler();
        $engineResult = $compiler->run($state);

        if (!$engineResult->success) {
            $this->error("❌ Failed to synchronize context: " . implode(', ', $engineResult->errors));
            return Command::FAILURE;
        }

        $contextDir = $aitosDir . '/context';
        if (!is_dir($contextDir)) {
            mkdir($contextDir, 0755, true);
        }

        foreach ($engineResult->data as $filename => $content) {
            file_put_contents($contextDir . '/' . $filename, $content);
            $this->line("  📄 Updated: .aitos/context/{$filename}");
        }

        $this->newLine();
        $this->info("✨ Success! AI context synchronized dynamically at " . date('Y-m-d H:i:s'));
        return Command::SUCCESS;
    }
}
