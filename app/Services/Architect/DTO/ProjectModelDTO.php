<?php

namespace App\Services\Architect\DTO;

class ProjectModelDTO
{
    public string $projectName;
    public string $projectDescription;
    public string $projectGoal;
    public array  $entities                 = [];
    public array  $relationships            = [];
    public array  $modules                  = [];
    public array  $roles                    = [];
    public array  $businessRules            = [];
    public array  $functionalRequirements   = [];
    public array  $nonFunctionalRequirements = [];
    public array  $assumptions              = [];
    public array  $risks                    = [];
    public array  $userStories              = [];
    public array  $suggestedFolderStructure = [];   // was incorrectly typed as string
    public array  $suggestedTechnologyStack = [];
    public array  $implementationPhases     = [];
    public array  $aiNotes                  = [];

    public function __construct(array $data = [])
    {
        $this->projectName        = $data['projectName']        ?? 'AITOS Project';
        $this->projectDescription = $data['projectDescription'] ?? '';
        $this->projectGoal        = $data['projectGoal']        ?? '';

        $req = $data['requirements'] ?? [];

        $this->entities                  = $this->parseEntities($req['entities']                   ?? []);
        $this->relationships             = $this->parseRelationships($req['relationships']          ?? []);
        $this->modules                   = $this->parseLines($req['modules']                       ?? '');
        $this->roles                     = $this->parseLines($req['roles']                         ?? '');
        $this->businessRules             = $this->parseLines($req['businessRules']                 ?? '');
        $this->functionalRequirements    = $this->parseLines($req['functionalRequirements']        ?? '');
        $this->nonFunctionalRequirements = $this->parseLines($req['nonFunctionalRequirements']     ?? '');
        $this->assumptions               = $this->parseLines($req['assumptions']                   ?? '');
        $this->risks                     = $this->parseLines($req['risks']                         ?? '');
        $this->userStories               = $this->parseLines($req['userStories']                   ?? '');
        $this->suggestedFolderStructure  = $this->parseFolder($req['suggestedFolderStructure']     ?? []);
        $this->suggestedTechnologyStack  = is_array($req['suggestedTechnologyStack'] ?? [])
                                            ? ($req['suggestedTechnologyStack'] ?? [])
                                            : [];
        $this->implementationPhases      = $this->parseLines($req['implementationPhases']          ?? '');
        $this->aiNotes                   = $this->parseLines($req['aiNotes']                       ?? '');
    }

    // -------------------------------------------------------------------------
    // Parsing Helpers
    // -------------------------------------------------------------------------

    /**
     * Parse entity data which may arrive as an array of objects or as plain text.
     */
    protected function parseEntities(mixed $input): array
    {
        if (is_array($input)) {
            // Already structured — validate each item has at least a 'name' key
            return array_values(array_filter(array_map(function ($item) {
                if (is_array($item) && isset($item['name'])) {
                    return [
                        'name'       => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $item['name'])),
                        'attributes' => array_values(array_filter(
                            array_map('trim', $item['attributes'] ?? [])
                        )),
                    ];
                }
                if (is_string($item) && !empty(trim($item))) {
                    return ['name' => trim($item), 'attributes' => []];
                }
                return null;
            }, $input), fn($v) => $v !== null && !empty($v['name'])));
        }

        if (!is_string($input) || empty(trim($input))) {
            return [];
        }

        // Legacy plain-text fallback
        $entities = [];
        $lines    = array_filter(array_map('trim', explode("\n", $input)));
        foreach ($lines as $line) {
            $parts = preg_split('/[:\-]/', $line, 2);
            $name  = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $parts[0] ?? ''));
            if (empty($name)) continue;

            $attrs = [];
            if (isset($parts[1])) {
                $rawAttrs      = explode(',', $parts[1]);
                $isDescription = false;
                foreach ($rawAttrs as $attr) {
                    $trimmed = trim($attr);
                    if (strlen($trimmed) > 25 || str_contains($trimmed, ' ')) {
                        $isDescription = true;
                        break;
                    }
                }
                if (!$isDescription) {
                    foreach ($rawAttrs as $attr) {
                        $attrName = trim(preg_replace('/[^a-zA-Z0-9_]/', '', $attr));
                        if (!empty($attrName)) {
                            $attrs[] = $attrName;
                        }
                    }
                }
            }

            $entities[] = ['name' => $name, 'attributes' => $attrs];
        }

        return $entities;
    }

    /**
     * Parse relationship data which may arrive as a structured array or plain text.
     */
    protected function parseRelationships(mixed $input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map(function ($item) {
                if (is_array($item) && isset($item['from'], $item['to'])) {
                    return [
                        'from' => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $item['from'])),
                        'to'   => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $item['to'])),
                        'type' => $item['type'] ?? 'has_many',
                    ];
                }
                return null;
            }, $input), fn($v) => $v !== null));
        }

        if (!is_string($input) || empty(trim($input))) {
            return [];
        }

        // Legacy plain-text fallback: "Entity1 has_many Entity2"
        $relations = [];
        $lines     = array_filter(array_map('trim', explode("\n", $input)));
        foreach ($lines as $line) {
            $parts = preg_split('/\s+/', $line, 3);
            if (count($parts) >= 3) {
                $relations[] = [
                    'from' => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $parts[0])),
                    'type' => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $parts[1])),
                    'to'   => trim(preg_replace('/[^a-zA-Z0-9_]/', '', $parts[2])),
                ];
            }
        }

        return $relations;
    }

    /**
     * Parse generic line-delimited data (array or string) into a clean string array.
     */
    protected function parseLines(mixed $input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map(
                fn($item) => is_string($item) ? trim($item) : (string) $item,
                $input
            ), fn($v) => $v !== ''));
        }

        if (!is_string($input)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", $input))));
    }

    /**
     * Parse folder structure (array or string) into a string array.
     */
    protected function parseFolder(mixed $input): array
    {
        return $this->parseLines($input);
    }

    // -------------------------------------------------------------------------
    // Serialisation
    // -------------------------------------------------------------------------

    public function toArray(): array
    {
        return [
            'project_name'                => $this->projectName,
            'description'                 => $this->projectDescription,
            'goal'                        => $this->projectGoal,
            'entities'                    => $this->entities,
            'relationships'               => $this->relationships,
            'modules'                     => $this->modules,
            'roles'                       => $this->roles,
            'business_rules'              => $this->businessRules,
            'functional_requirements'     => $this->functionalRequirements,
            'non_functional_requirements' => $this->nonFunctionalRequirements,
            'assumptions'                 => $this->assumptions,
            'risks'                       => $this->risks,
            'user_stories'                => $this->userStories,
            'folder_structure'            => $this->suggestedFolderStructure,
            'tech_stack'                  => $this->suggestedTechnologyStack,
            'phases'                      => $this->implementationPhases,
            'ai_notes'                    => $this->aiNotes,
        ];
    }
}
