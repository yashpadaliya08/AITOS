<?php

namespace App\Services\AI\Prompts;

class RequirementAnalysisPrompt
{
    /**
     * Get the master system prompt for requirements analysis.
     */
    public static function getSystemPrompt(): string
    {
        return <<<PROMPT
You are a Senior Software Architect and AI Integration Specialist.
Analyze the provided Project Context object and compile a detailed project memory structure.

The response must be valid JSON only. Do not include markdown wraps (like ```json), intro text, or explanation.
Your response will be decoded directly in PHP via json_decode. If your output is not valid JSON, the application will crash.

Adhere strictly to this JSON format and key mapping:
{
    "projectSummary": "Provide a high-fidelity 3-4 sentence paragraph summarizing the product vision, its core purpose, and development goals based on the problem statement.",
    "entities": [
        "Define Key Entity 1 (e.g. User - attributes, relationships)",
        "Define Key Entity 2"
    ],
    "modules": [
        "Define Module 1 (e.g. Authentication Manager - responsibilities)",
        "Define Module 2"
    ],
    "roles": [
        "Define Role 1 (e.g. Administrator - access capabilities)",
        "Define Role 2"
    ],
    "businessRules": [
        "Core Business Rule 1 (strict policy constraints)",
        "Core Business Rule 2"
    ],
    "functionalRequirements": [
        "Functional Requirement 1 (specific feature capability)",
        "Functional Requirement 2"
    ],
    "nonFunctionalRequirements": [
        "Non-functional Requirement 1 (performance, security, scaling metric)",
        "Non-functional Requirement 2"
    ],
    "assumptions": [
        "Technical or User Assumption 1",
        "Technical or User Assumption 2"
    ],
    "risks": [
        "Project Risk 1 and its mitigation",
        "Project Risk 2 and its mitigation"
    ],
    "userStories": [
        "User Story 1: As a [user role], I want to [action] so that [benefit].",
        "User Story 2: As a [user role], I want to [action] so that [benefit]."
    ],
    "suggestedFolderStructure": [
        "app/ - Core backend logic and routes",
        "resources/views/ - Frontend Blade templates"
    ],
    "suggestedTechnologyStack": {
        "framework": "Backend framework matching user selection",
        "database": "Database engine matching user selection",
        "frontend": "Frontend layout engine matching user selection",
        "notes": "Short suggestions for key supporting libraries or utilities"
    },
    "implementationPhases": [
        "Phase 1: Setup & Core Schemas",
        "Phase 2: Authentication & Modules API",
        "Phase 3: Integration & AI Alignment checks"
    ],
    "aiNotes": [
        "AI Architecture suggestion note 1",
        "AI Architecture suggestion note 2"
    ]
}

Ensure all arrays and properties are populated with high-quality descriptions. Avoid general placeholders.
PROMPT;
    }

    /**
     * Get the formatted user message containing the project context.
     */
    public static function getUserMessage(array $projectContext): string
    {
        return json_encode($projectContext, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
