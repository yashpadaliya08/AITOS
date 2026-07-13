<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\DTO\ProjectContext;
use App\Services\AI\Cache\AnalysisCache;
use App\Services\AI\Prompts\BlueprintGenerationPrompt;
use App\Services\AI\Prompts\BlueprintRefinementPrompt;

class AIController extends Controller
{
    /**
     * Analyze project context and return structured requirements analysis JSON.
     */
    public function analyze(Request $request): JsonResponse
    {
        ini_set('max_execution_time', 180);

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
        ini_set('max_execution_time', 180);

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

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "API key for provider '{$providerName}' is missing. Please configure it in Settings or your backend .env file.",
            ], 400);
        }

        $systemPrompt = BlueprintRefinementPrompt::getSystemPrompt();
        $userMessage  = BlueprintRefinementPrompt::getUserMessage($content, $instruction);

        try {
            $modelResponse = $this->callProvider($providerName, $apiKey, $systemPrompt, $userMessage, false);

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
        ini_set('max_execution_time', 180);

        $request->validate([
            'project_name'      => 'required|string|max:255',
            'project_goal'      => 'required|string',
            'problem_statement' => 'required|string',
            'requirements'      => 'required|string',
            'api_key'           => 'nullable|string',
        ]);

        $providerName = $request->input('provider', config('ai.default_provider', 'gemini'));
        $apiKey       = $request->input('api_key') ?: config("ai.providers.{$providerName}.api_key");

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
            $modelResponse = $this->callProvider($providerName, $apiKey, $systemPrompt, $userMessage, true);

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

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Unified provider call — routes to the correct AI API and returns raw text.
     *
     * @param  bool   $expectJson  Whether the call should request a JSON-formatted response.
     */
    private function callProvider(
        string $providerName,
        string $apiKey,
        string $systemPrompt,
        string $userMessage,
        bool   $expectJson
    ): string {
        return match ($providerName) {
            'openai'    => $this->callOpenAI($apiKey, $systemPrompt, $userMessage, $expectJson),
            'anthropic' => $this->callAnthropic($apiKey, $systemPrompt, $userMessage),
            default     => $this->callGemini($apiKey, $systemPrompt, $userMessage, $expectJson),
        };
    }

    /**
     * Call the OpenAI (or OpenRouter) chat completions endpoint.
     */
    private function callOpenAI(
        string $apiKey,
        string $systemPrompt,
        string $userMessage,
        bool   $expectJson
    ): string {
        $model   = config('ai.providers.openai.model', 'gpt-4o-mini');
        $timeout = (int) config('ai.providers.openai.timeout', 120);

        $url     = 'https://api.openai.com/v1/chat/completions';
        $headers = [
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ];

        // OpenRouter compatibility
        if (str_starts_with($apiKey, 'sk-or-')) {
            $url                    = 'https://openrouter.ai/api/v1/chat/completions';
            $headers['HTTP-Referer'] = 'http://localhost';
            $headers['X-Title']     = 'AITOS';

            if (!str_contains($model, '/')) {
                $model = str_contains($model, 'gpt-4o-mini') ? 'openai/gpt-4o-mini' : 'google/gemini-2.5-flash';
            }
        }

        $maxTokens = str_contains($model, ':free') ? 8000 : 3000;

        $payload = [
            'model'      => $model,
            'messages'   => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $userMessage],
            ],
            'max_tokens' => $maxTokens,
        ];

        // Only add response_format for native OpenAI/OpenAI-namespaced models
        if ($expectJson && (!str_starts_with($apiKey, 'sk-or-') || str_contains($model, 'openai/') || str_contains($model, 'gpt-'))) {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withHeaders($headers)->timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            Log::error("OpenAI API request failed: Status {$response->status()} — " . $response->body());
            throw new \Exception("OpenAI API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $content = $response->json('choices.0.message.content');
        if (empty($content)) {
            throw new \Exception("Empty response returned from OpenAI API.");
        }

        return $content;
    }

    /**
     * Call the Anthropic Messages endpoint.
     */
    private function callAnthropic(
        string $apiKey,
        string $systemPrompt,
        string $userMessage
    ): string {
        $model     = config('ai.providers.anthropic.model', 'claude-3-haiku-20240307');
        $maxTokens = (int) config('ai.providers.anthropic.max_tokens', 4000);
        $timeout   = (int) config('ai.providers.anthropic.timeout', 120);

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type'      => 'application/json',
        ])->timeout($timeout)->post('https://api.anthropic.com/v1/messages', [
            'model'      => $model,
            'max_tokens' => $maxTokens,
            'system'     => $systemPrompt,
            'messages'   => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception("Anthropic API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $content = $response->json('content.0.text');
        if (empty($content)) {
            throw new \Exception("Empty response returned from Anthropic API.");
        }

        return $content;
    }

    /**
     * Call the Google Gemini generateContent endpoint.
     */
    private function callGemini(
        string $apiKey,
        string $systemPrompt,
        string $userMessage,
        bool   $expectJson
    ): string {
        $model   = config('ai.providers.gemini.model', 'gemini-1.5-flash');
        $timeout = (int) config('ai.providers.gemini.timeout', 120);
        $url     = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $userMessage]],
                ],
            ],
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
        ];

        if ($expectJson) {
            $payload['generationConfig'] = ['responseMimeType' => 'application/json'];
        }

        $response = Http::timeout($timeout)->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception("Gemini API request failed: " . ($response->json('error.message') ?? $response->body()));
        }

        $content = $response->json('candidates.0.content.parts.0.text');
        if (empty($content)) {
            throw new \Exception("Empty response returned from Gemini API.");
        }

        return $content;
    }
}
