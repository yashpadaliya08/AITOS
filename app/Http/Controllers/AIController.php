<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Cache\AnalysisCache;
use App\Services\AI\Prompts\BlueprintGenerationPrompt;
use App\Services\AI\Prompts\BlueprintRefinementPrompt;

class AIController extends Controller
{
    /**
     * Safely override the model config only if the requested model is compatible
     * with the selected provider. This prevents cross-provider model mismatches
     * (e.g. sending a Qwen model to the Gemini API).
     */
    private function applyModelOverride(Request $request, string $providerName): void
    {
        if (!$request->filled('model')) {
            return;
        }

        $requestedModel = trim($request->input('model'));
        if (empty($requestedModel)) {
            return;
        }

        $isCompatible = match ($providerName) {
            'gemini'    => str_contains($requestedModel, 'gemini'),
            'anthropic' => str_contains($requestedModel, 'claude'),
            'openai'    => true, // OpenAI/OpenRouter accepts any model identifier
            default     => true,
        };

        if ($isCompatible) {
            config(["ai.providers.{$providerName}.model" => $requestedModel]);
            Log::info("AIController: Model override applied [{$providerName}] → {$requestedModel}");
        } else {
            Log::warning("AIController: Ignoring incompatible model '{$requestedModel}' for provider '{$providerName}'. Using provider default.");
        }
    }

    /**
     * Analyze project context and return structured requirements analysis JSON.
     */
    public function analyze(Request $request): JsonResponse
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $request->validate([
            'project_name'      => 'required|string|max:255',
            'project_goal'      => 'required|string',
            'problem_statement' => 'required|string',
            'api_key'           => 'nullable|string',
        ]);

        $projectName      = $request->input('project_name');
        $projectGoal      = $request->input('project_goal');
        $problemStatement = $request->input('problem_statement');
        $providerName     = $request->input('provider', config('ai.default_provider', 'gemini'));
        $apiKey           = $request->input('api_key') ?: config("ai.providers.{$providerName}.api_key");

        $this->applyModelOverride($request, $providerName);

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "API key for provider '{$providerName}' is missing. Please configure it in Settings or your backend .env file.",
            ], 400);
        }

        // Generate SHA-256 context hash for cache lookup
        $hash = AnalysisCache::generateHash($projectName, $projectGoal, $problemStatement);

        // Return cached result if available — avoids redundant AI API calls
        if (AnalysisCache::has($hash)) {
            return response()->json([
                'success'  => true,
                'hash'     => $hash,
                'analysis' => AnalysisCache::get($hash),
                'cached'   => true,
            ]);
        }

        $context = new ProjectContext([
            'project_name'        => $projectName,
            'project_description' => $request->input('project_description', ''),
            'project_goal'        => $projectGoal,
            'problem_statement'   => $problemStatement,
            'preferred_stack'     => $request->input('preferred_stack', []),
        ]);

        try {
            $provider       = AIProviderFactory::make($providerName);
            $analysisResult = $provider->analyze($context, $apiKey);

            // Persist to cache
            AnalysisCache::store($hash, $analysisResult);

            return response()->json([
                'success'  => true,
                'hash'     => $hash,
                'analysis' => $analysisResult,
                'cached'   => false,
            ]);

        } catch (\Exception $e) {
            Log::error("AIController::analyze failed [{$providerName}]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Refine an architectural blueprint using the selected AI provider.
     */
    public function refineBlueprint(Request $request): JsonResponse
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $request->validate([
            'blueprint_type'    => 'required|string',
            'blueprint_content' => 'required|string',
            'instruction'       => 'required|string',
            'api_key'           => 'nullable|string',
        ]);

        $content      = $request->input('blueprint_content');
        $instruction  = $request->input('instruction');
        $providerName = $request->input('provider', config('ai.default_provider', 'gemini'));
        $apiKey       = $request->input('api_key') ?: config("ai.providers.{$providerName}.api_key");

        $this->applyModelOverride($request, $providerName);

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "API key for provider '{$providerName}' is missing. Please configure it in Settings or your backend .env file.",
            ], 400);
        }

        $systemPrompt = BlueprintRefinementPrompt::getSystemPrompt();
        $userMessage  = BlueprintRefinementPrompt::getUserMessage($content, $instruction);

        try {
            $provider      = AIProviderFactory::make($providerName);
            $modelResponse = $provider->chat($systemPrompt, $userMessage, $apiKey, false);

            return response()->json([
                'success'         => true,
                'refined_content' => trim($modelResponse),
            ]);

        } catch (\Exception $e) {
            Log::error("AIController::refineBlueprint failed [{$providerName}]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate custom blueprints dynamically from approved project requirements.
     */
    public function generateBlueprints(Request $request): JsonResponse
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        $request->validate([
            'project_name'      => 'required|string|max:255',
            'project_goal'      => 'required|string',
            'problem_statement' => 'required|string',
            'requirements'      => 'required|string',
            'api_key'           => 'nullable|string',
        ]);

        $providerName = $request->input('provider', config('ai.default_provider', 'gemini'));
        $apiKey       = $request->input('api_key') ?: config("ai.providers.{$providerName}.api_key");

        $this->applyModelOverride($request, $providerName);

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "API key for provider '{$providerName}' is missing.",
            ], 400);
        }

        $systemPrompt = BlueprintGenerationPrompt::getSystemPrompt();
        $userMessage  = BlueprintGenerationPrompt::getUserMessage(
            $request->input('project_name'),
            $request->input('project_goal'),
            $request->input('problem_statement'),
            $request->input('requirements')
        );

        try {
            $provider      = AIProviderFactory::make($providerName);
            $modelResponse = $provider->chat($systemPrompt, $userMessage, $apiKey, true);

            Log::info("AIController::generateBlueprints raw response [{$providerName}]: " . substr($modelResponse, 0, 500));

            // Extract JSON boundaries to strip any surrounding text
            $jsonStart = strpos($modelResponse, '{');
            $jsonEnd   = strrpos($modelResponse, '}');
            if ($jsonStart !== false && $jsonEnd !== false) {
                $modelResponse = substr($modelResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
            }

            $decoded = json_decode($modelResponse, true);
            if (!$decoded || !is_array($decoded)) {
                throw new \Exception("Response is not valid JSON: " . json_last_error_msg());
            }

            $blueprints = BlueprintGenerationPrompt::normalizeResponse($decoded);

            return response()->json([
                'success'    => true,
                'blueprints' => $blueprints,
            ]);

        } catch (\Exception $e) {
            Log::error("AIController::generateBlueprints failed [{$providerName}]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
