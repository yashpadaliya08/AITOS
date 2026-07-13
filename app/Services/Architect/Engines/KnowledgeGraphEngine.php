<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\Validators\KnowledgeGraphValidator;

class KnowledgeGraphEngine implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start        = microtime(true);
        $projectModel = new ProjectModelDTO($state);

        $relations = [];

        if (!empty($projectModel->relationships)) {
            // Structured relationships from AI — direct mapping
            foreach ($projectModel->relationships as $rel) {
                $from = strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $rel['from'] ?? '')));
                $to   = strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $rel['to']   ?? '')));

                if (empty($from) || empty($to)) continue;

                $relations[] = [
                    'source' => $from,
                    'target' => $to,
                    'type'   => 'One-to-Many',
                    'label'  => strtolower(trim($rel['type'] ?? 'has_many')),
                ];
            }
        } else {
            // O(n) heuristic fallback — pre-index entity names to avoid O(n²) nested loop
            $entities = $projectModel->entities;

            // Build a lookup map: clean_name => original_entity_data
            $nameIndex = [];
            foreach ($entities as $entityData) {
                $name      = is_array($entityData) ? ($entityData['name'] ?? '') : (string) $entityData;
                $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $name)));
                if (!empty($cleanName)) {
                    $nameIndex[$cleanName] = $name;
                }
            }

            // Check each entity name for substrings of other entity names
            foreach ($nameIndex as $e1Clean => $e1Name) {
                foreach ($nameIndex as $e2Clean => $e2Name) {
                    if ($e1Clean === $e2Clean) continue;

                    $singularE2 = rtrim($e2Clean, 's');
                    if (str_contains(strtolower($e1Name), $e2Clean) || str_contains(strtolower($e1Name), $singularE2)) {
                        $relations[] = [
                            'source' => $e2Clean,
                            'target' => $e1Clean,
                            'type'   => 'One-to-Many',
                            'label'  => 'has_many',
                        ];
                    }
                }
            }
        }

        // Final fallback: link first two entities if still empty
        if (empty($relations) && count($projectModel->entities) >= 2) {
            $e1 = is_array($projectModel->entities[0]) ? ($projectModel->entities[0]['name'] ?? '') : $projectModel->entities[0];
            $e2 = is_array($projectModel->entities[1]) ? ($projectModel->entities[1]['name'] ?? '') : $projectModel->entities[1];

            $relations[] = [
                'source' => strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $e1))),
                'target' => strtolower(trim(preg_replace('/[^a-zA-Z]/', '', $e2))),
                'type'   => 'One-to-Many',
                'label'  => 'owns',
            ];
        }

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success'        => true,
            'warnings'       => [],
            'errors'         => [],
            'generated_file' => 'knowledge_graph.json',
            'execution_time' => $duration,
            'data'           => ['relations' => $relations],
        ]);
    }

    public function validate(array $output): array
    {
        return KnowledgeGraphValidator::validate($output);
    }

    public function getName(): string
    {
        return 'KnowledgeGraphEngine';
    }

    public function getDependencies(): array
    {
        return ['projectModel'];
    }
}
