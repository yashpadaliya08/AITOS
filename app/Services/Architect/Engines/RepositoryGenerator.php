<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\Registry\FrameworkRegistry;
use App\Services\Architect\Validators\RepositoryValidator;

class RepositoryGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        
        $framework = $state['techStack']['framework'] ?? 'laravel';
        $provider = FrameworkRegistry::get($framework);
        
        $files = [];

        if ($provider) {
            // Get configuration configs
            $configs = $provider->getConfigFileTemplates();
            foreach ($configs as $path => $content) {
                $files[$path] = $content;
            }

            // Get generated schema placeholders using code generation model
            $dbSchema = $state['databaseSchema'] ?? ['tables' => []];
            $codeGenModel = $state['codeGenerationModel'] ?? [];
            $placeholders = $provider->getPlaceholderFiles([
                'schema' => $dbSchema,
                'code_gen_model' => $codeGenModel
            ]);
            foreach ($placeholders as $path => $content) {
                $files[$path] = $content;
            }
        }

        // Add standard root gitignore file
        $files['.gitignore'] = "/node_modules\n/vendor\n.env\n.DS_Store\n";

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'repository.json',
            'execution_time' => $duration,
            'data' => [
                'files' => $files,
                'framework' => $framework
            ]
        ]);
    }

    public function validate(array $output): array
    {
        return RepositoryValidator::validate($output);
    }

    public function getName(): string
    {
        return 'RepositoryGenerator';
    }

    public function getDependencies(): array
    {
        return ['requirements', 'databaseSchema', 'apiDesign', 'uiBlueprint', 'codeGenerationModel', 'workspaceState'];
    }
}
