<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Cache\AnalysisCache;

class AIController extends Controller
{
    /**
     * Analyze project context and return structured requirements analysis JSON.
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string',
            'project_goal' => 'required|string',
            'problem_statement' => 'required|string',
            'api_key' => 'nullable|string',
        ]);

        $projectName = $request->input('project_name');
        $projectGoal = $request->input('project_goal');
        $problemStatement = $request->input('problem_statement');
        $providerName = $request->input('provider', config('ai.default_provider', 'gemini'));
        
        $apiKey = $request->input('api_key') ?: config("ai.providers.{$providerName}.api_key");

        if (empty($apiKey)) {
            throw new \Exception("API key for provider '{$providerName}' is missing. Please configure it in Settings or your backend .env file.");
        }

        // Assemble Project Context DTO
        $context = new ProjectContext([
            'project_name' => $projectName,
            'project_description' => $request->input('project_description', ''),
            'project_goal' => $projectGoal,
            'problem_statement' => $problemStatement,
            'preferred_stack' => $request->input('preferred_stack', [])
        ]);

        try {
            // Generate SHA256 context hash
            $hash = AnalysisCache::generateHash($projectName, $projectGoal, $problemStatement);

            // Fetch Provider from Factory
            $provider = AIProviderFactory::make($providerName);

            // Call API
            $analysisResult = $provider->analyze($context, $apiKey);

            // Return success with JSON schema
            return response()->json([
                'success' => true,
                'hash' => $hash,
                'analysis' => $analysisResult
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
