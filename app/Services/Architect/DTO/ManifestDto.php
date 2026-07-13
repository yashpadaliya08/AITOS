<?php

namespace App\Services\Architect\DTO;

class ManifestDto
{
    public string $projectName;
    public string $framework;
    public string $aitosVersion = '1.5.0';
    public string $blueprintVersion = '1.0.0';
    public string $generatedTime;
    public string $repositoryVersion = '1.0.0';

    public function __construct(array $data = [])
    {
        $this->projectName = $data['projectName'] ?? 'AITOS_Project';
        $this->framework = $data['framework'] ?? 'unknown';
        $this->aitosVersion = $data['aitosVersion'] ?? '1.5.0';
        $this->blueprintVersion = $data['blueprintVersion'] ?? '1.0.0';
        $this->generatedTime = $data['generatedTime'] ?? date('c');
        $this->repositoryVersion = $data['repositoryVersion'] ?? '1.0.0';
    }

    public function toArray(): array
    {
        return [
            'project_name' => $this->projectName,
            'framework' => $this->framework,
            'aitos_version' => $this->aitosVersion,
            'blueprint_version' => $this->blueprintVersion,
            'generated_time' => $this->generatedTime,
            'repository_version' => $this->repositoryVersion,
        ];
    }

    public function toJson(int $options = JSON_PRETTY_PRINT): string
    {
        return json_encode($this->toArray(), $options);
    }

    public static function fromArray(array $data): self
    {
        return new self([
            'projectName' => $data['project_name'] ?? null,
            'framework' => $data['framework'] ?? null,
            'aitosVersion' => $data['aitos_version'] ?? null,
            'blueprintVersion' => $data['blueprint_version'] ?? null,
            'generatedTime' => $data['generated_time'] ?? null,
            'repositoryVersion' => $data['repository_version'] ?? null,
        ]);
    }
}
