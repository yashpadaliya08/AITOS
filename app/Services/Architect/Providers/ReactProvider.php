<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class ReactProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'react';
    }

    public function getFolderStructure(): array
    {
        return [
            'src/components',
            'src/hooks',
            'src/pages',
            'src/context',
            'src/utils',
            'public'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];

        $pageTpl = $this->loadTemplate('react', 'Page.jsx.tpl', "import React from 'react';\nconst {{MODEL_NAME}}Page = () => { return <div>{{MODEL_NAME}}</div>; };\nexport default {{MODEL_NAME}}Page;\n");
        $compTpl = $this->loadTemplate('react', 'Component.jsx.tpl', "import React from 'react';\nexport default function {{MODEL_NAME}}Component() { return <div>{{MODEL_NAME}} Component</div>; }\n");

        foreach ($mappings as $mapping) {
            $modelName = $mapping['model'];
            $routeName = $mapping['route'];

            $pageContent = str_replace(
                ['{{MODEL_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $routeName],
                $pageTpl
            );
            $files["src/pages/{$modelName}Page.jsx"] = $pageContent;

            $compContent = str_replace(
                ['{{MODEL_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $routeName],
                $compTpl
            );
            $files["src/components/{$modelName}Component.jsx"] = $compContent;
        }

        // Add index.js entry point to make react buildable
        $files["src/index.js"] = "import React from 'react';\nimport ReactDOM from 'react-dom/client';\nimport App from './App';\nconst root = ReactDOM.createRoot(document.getElementById('root'));\nroot.render(<React.StrictMode><App /></React.StrictMode>);\n";
        
        // Add App.js mapping routes dynamically
        $appJs = "import React from 'react';\n";
        foreach ($mappings as $mapping) {
            $appJs .= "import " . $mapping['model'] . "Page from './pages/" . $mapping['model'] . "Page';\n";
        }
        $appJs .= "\nexport default function App() {\n    return (\n        <div>\n            <h1>AITOS React Starter</h1>\n";
        foreach ($mappings as $mapping) {
            $appJs .= "            <" . $mapping['model'] . "Page />\n";
        }
        $appJs .= "        </div>\n    );\n}\n";
        $files["src/App.js"] = $appJs;

        // Add index.html skeleton inside public directory
        $files["public/index.html"] = "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>AITOS React App</title>\n</head>\n<body>\n    <div id=\"root\"></div>\n</body>\n</html>\n";

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'react-starter',
                'version' => '1.0.0',
                'private' => true,
                'dependencies' => [
                    'react' => '^18.2.0',
                    'react-dom' => '^18.2.0',
                    'react-scripts' => '^5.0.1'
                ],
                'scripts' => [
                    'start' => 'react-scripts start',
                    'build' => 'react-scripts build'
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
