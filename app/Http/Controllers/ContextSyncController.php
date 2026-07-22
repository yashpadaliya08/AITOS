<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Architect\Engines\ContextCompiler;
use App\Services\Architect\DTO\ProjectModelDTO;

class ContextSyncController extends Controller
{
    /**
     * Synchronize and dynamically regenerate AI context files (.aitos/context/).
     */
    public function sync(Request $request)
    {
        $stateJson = $request->input('project_state');
        $state = is_string($stateJson) ? json_decode($stateJson, true) : $request->input('state', []);

        if (!$state || !is_array($state)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing project state.',
            ], 422);
        }

        // Apply task updates if provided
        $completedTaskId = $request->input('completed_task_id');
        $updatedTasks = $request->input('tasks', []);

        if (!empty($updatedTasks)) {
            $state['projectPlan']['tasks'] = $updatedTasks;
        }

        if ($completedTaskId && isset($state['projectPlan']['tasks'])) {
            foreach ($state['projectPlan']['tasks'] as &$task) {
                if (($task['id'] ?? null) === $completedTaskId || ($task['name'] ?? null) === $completedTaskId) {
                    $task['status'] = 'completed';
                    $task['completed_at'] = date('c');
                }
            }
            unset($task);
        }

        // Run ContextCompiler engine
        $compiler = new ContextCompiler();
        $engineResult = $compiler->run($state);

        if (!$engineResult->success) {
            return response()->json([
                'success' => false,
                'message' => 'Context synchronization failed.',
                'errors' => $engineResult->errors,
            ], 500);
        }

        $contexts = $engineResult->data;

        return response()->json([
            'success' => true,
            'message' => 'AI Context synchronized successfully.',
            'timestamp' => date('c'),
            'current_context' => $contexts['CURRENT_CONTEXT.md'] ?? '',
            'contexts' => $contexts,
            'updated_state' => $state,
        ]);
    }
}
