<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class NodeProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'node';
    }

    public function getFolderStructure(): array
    {
        return [
            'src/controllers',
            'src/models',
            'src/routes',
            'src/middleware',
            'src/config'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];

        $ctrlTpl = $this->loadTemplate('node', 'controller.js.tpl', "// Controller\nexports.getAll = async (req, res) => { res.json([]); };\n");
        $routeTpl = $this->loadTemplate('node', 'route.js.tpl', "const express = require('express');\nconst router = express.Router();\nmodule.exports = router;\n");

        foreach ($mappings as $mapping) {
            $modelName = $mapping['model'];
            $routeName = $mapping['route'];
            $modelFile = strtolower($mapping['entity']);

            $ctrlContent = str_replace(
                ['{{MODEL_NAME}}', '{{TABLE_NAME}}'],
                [$modelName, $routeName],
                $ctrlTpl
            );
            $files["src/controllers/{$modelFile}.js"] = $ctrlContent;

            $routeContent = str_replace(
                ['{{MODEL_FILE_NAME}}'],
                [$modelFile],
                $routeTpl
            );
            $files["src/routes/{$modelFile}.js"] = $routeContent;
        }

        // Add src/index.js entry point containing express app server bootstrap and dynamic router loops
        $indexJs = "const express = require('express');\nconst app = express();\napp.use(express.json());\n\n";
        foreach ($mappings as $mapping) {
            $modelFile = strtolower($mapping['entity']);
            $indexJs .= "const {$modelFile}Router = require('./routes/{$modelFile}');\napp.use('/api/{$mapping['route']}', {$modelFile}Router);\n";
        }
        $indexJs .= "\napp.get('/', (req, res) => res.json({ status: 'running' }));\n";
        $indexJs .= "const PORT = process.env.PORT || 3000;\napp.listen(PORT, () => console.log('Node Server active on port ' + PORT));\n";
        $files["src/index.js"] = $indexJs;

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'node-starter',
                'version' => '1.0.0',
                'description' => 'Express Node API starter scaffolding.',
                'main' => 'src/index.js',
                'scripts' => [
                    'start' => 'node src/index.js',
                    'dev' => 'nodemon src/index.js'
                ],
                'dependencies' => [
                    'express' => '^4.18.2',
                    'cors' => '^2.8.5',
                    'dotenv' => '^16.3.1'
                ]
            ], JSON_PRETTY_PRINT)
        ];
    }

    private function loadTemplate(string $framework, string $filename, string $default): string
    {
        $path = storage_path("app/templates/{$framework}/skeletons/{$filename}");
        if (file_exists($path)) {
            return file_get_contents($path);
        }
        return $default;
    }
}
