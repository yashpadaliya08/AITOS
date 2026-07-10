<?php

namespace App\Services\AI\DTO;

class AnalysisResult
{
    public string $projectSummary;
    public array $entities;
    public array $modules;
    public array $roles;
    public array $businessRules;
    public array $functionalRequirements;
    public array $nonFunctionalRequirements;
    public array $assumptions;
    public array $risks;
    public array $userStories;
    public array $suggestedFolderStructure;
    public array $suggestedTechnologyStack;
    public array $implementationPhases;
    public array $aiNotes;

    public function __construct(array $data)
    {
        $this->projectSummary = $data['projectSummary'] ?? '';
        $this->entities = $data['entities'] ?? [];
        $this->modules = $data['modules'] ?? [];
        $this->roles = $data['roles'] ?? [];
        $this->businessRules = $data['businessRules'] ?? [];
        $this->functionalRequirements = $data['functionalRequirements'] ?? [];
        $this->nonFunctionalRequirements = $data['nonFunctionalRequirements'] ?? [];
        $this->assumptions = $data['assumptions'] ?? [];
        $this->risks = $data['risks'] ?? [];
        $this->userStories = $data['userStories'] ?? [];
        $this->suggestedFolderStructure = $data['suggestedFolderStructure'] ?? [];
        $this->suggestedTechnologyStack = $data['suggestedTechnologyStack'] ?? [];
        $this->implementationPhases = $data['implementationPhases'] ?? [];
        $this->aiNotes = $data['aiNotes'] ?? [];
    }

    /**
     * Convert properties to structured JSON layout arrays.
     */
    public function toArray(): array
    {
        return [
            'projectSummary' => $this->projectSummary,
            'entities' => $this->entities,
            'modules' => $this->modules,
            'roles' => $this->roles,
            'businessRules' => $this->businessRules,
            'functionalRequirements' => $this->functionalRequirements,
            'nonFunctionalRequirements' => $this->nonFunctionalRequirements,
            'assumptions' => $this->assumptions,
            'risks' => $this->risks,
            'userStories' => $this->userStories,
            'suggestedFolderStructure' => $this->suggestedFolderStructure,
            'suggestedTechnologyStack' => $this->suggestedTechnologyStack,
            'implementationPhases' => $this->implementationPhases,
            'aiNotes' => $this->aiNotes,
        ];
    }
}
