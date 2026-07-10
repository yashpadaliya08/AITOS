<?php

namespace App\Services\AI\DTO;

class ProjectContext
{
    public string $projectName;
    public string $projectDescription;
    public string $projectGoal;
    public string $problemStatement;
    public array $preferredStack;

    public function __construct(array $data)
    {
        $this->projectName = $data['project_name'] ?? '';
        $this->projectDescription = $data['project_description'] ?? '';
        $this->projectGoal = $data['project_goal'] ?? '';
        $this->problemStatement = $data['problem_statement'] ?? '';
        $this->preferredStack = $data['preferred_stack'] ?? [];
    }

    /**
     * Export the context as an associative array.
     */
    public function toArray(): array
    {
        return [
            'project_name' => $this->projectName,
            'project_description' => $this->projectDescription,
            'project_goal' => $this->projectGoal,
            'problem_statement' => $this->problemStatement,
            'preferred_stack' => $this->preferredStack,
        ];
    }
}
