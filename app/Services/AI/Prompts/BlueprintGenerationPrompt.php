<?php

namespace App\Services\AI\Prompts;

class BlueprintGenerationPrompt
{
    /**
     * Get the system prompt for blueprint generation.
     */
    public static function getSystemPrompt(): string
    {
        return <<<PROMPT
You are an expert AI Software Architect. Based on the provided project context and requirements, generate four distinct system blueprints:
1. Business Blueprint (Target audience, Value propositions, business workflows)
2. Database Blueprint (Detailed schema, tables, uuid keys, columns, relationships)
3. Technical Blueprint (Core frameworks structure, state persistence, configuration patterns)
4. UI Spec Blueprint (Responsive grids layouts, professional light theme palettes, components specs)

IMPORTANT CONSTRAINTS:
- Keep the content of each blueprint section extremely CONCISE, high-density, and structured.
- Avoid long introductions, explanations, redundant lists, or verbose text.
- Use short, bulleted points or compact tables where possible.
- Capping each section to a maximum of 300 words is MANDATORY.

Your response MUST be a valid JSON object matching the following structure exactly, with no additional chat text or wrapper fences:
{
  "business": "Markdown content for business blueprint",
  "database": "Markdown content for database blueprint",
  "technical": "Markdown content for technical blueprint",
  "ui": "Markdown content for UI spec blueprint"
}
PROMPT;
    }

    /**
     * Build the user message from project context inputs.
     */
    public static function getUserMessage(
        string $projectName,
        string $projectGoal,
        string $problemStatement,
        string $requirements
    ): string {
        return <<<MSG
Project Context:
- Name: {$projectName}
- Goal: {$projectGoal}
- Problem: {$problemStatement}

Approved Requirements:
{$requirements}
MSG;
    }

    /**
     * Normalise the AI response JSON into the expected blueprint keys.
     * Handles varied key naming from different AI providers.
     */
    public static function normalizeResponse(array $decoded): array
    {
        $normalized = [];

        foreach ($decoded as $key => $val) {
            $lk = strtolower($key);
            if (str_contains($lk, 'business')) {
                $normalized['business'] = $val;
            } elseif (str_contains($lk, 'database') || str_contains($lk, 'schema') || str_contains($lk, 'db')) {
                $normalized['database'] = $val;
            } elseif (str_contains($lk, 'technical') || str_contains($lk, 'architecture') || str_contains($lk, 'tech')) {
                $normalized['technical'] = $val;
            } elseif (str_contains($lk, 'ui') || str_contains($lk, 'interface') || str_contains($lk, 'spec') || str_contains($lk, 'frontend')) {
                $normalized['ui'] = $val;
            }
        }

        return [
            'business'  => $normalized['business']  ?? ($decoded['business']  ?? "### Business Domain Map\n- Context not resolved."),
            'database'  => $normalized['database']  ?? ($decoded['database']  ?? "### Database Schema\n- Schema details not resolved."),
            'technical' => $normalized['technical'] ?? ($decoded['technical'] ?? "### Technical Blueprint\n- Architecture details not resolved."),
            'ui'        => $normalized['ui']        ?? ($decoded['ui']        ?? "### UI Specifications\n- UI spec details not resolved."),
        ];
    }
}
