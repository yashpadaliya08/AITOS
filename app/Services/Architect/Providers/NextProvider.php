<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class NextProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'next';
    }

    public function getFolderStructure(): array
    {
        return [
            'src/app',
            'src/components',
            'src/hooks',
            'public'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];

        $pageTpl = $this->loadTemplate('next', 'page.tsx.tpl', "import React from 'react';\nexport default function {{MODEL_NAME}}Page() { return <div>{{MODEL_NAME}}</div>; }\n");
        $compTpl = $this->loadTemplate('next', 'Component.tsx.tpl', "'use client';\nimport React from 'react';\nexport default function {{MODEL_NAME}}Component() { return <div>{{MODEL_NAME}} Component</div>; }\n");

        foreach ($mappings as $mapping) {
            $modelName = $mapping['model'];
            $routeName = $mapping['route'];

            $pageContent = str_replace(
                ['{{MODEL_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $routeName],
                $pageTpl
            );
            $files["src/app/{$routeName}/page.tsx"] = $pageContent;

            $compContent = str_replace(
                ['{{MODEL_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $routeName],
                $compTpl
            );
            $files["src/components/{$modelName}Component.tsx"] = $compContent;
        }

        // Add root layout.tsx and page.tsx to ensure next builds cleanly
        $files["src/app/layout.tsx"] = "import React from 'react';\nexport default function RootLayout({ children }: { children: React.ReactNode }) {\n    return (\n        <html lang=\"en\">\n            <body>{children}</body>\n        </html>\n    );\n}\n";
        $files["src/app/page.tsx"] = "import React from 'react';\nexport default function Home() { return <h1>Welcome to AITOS Next.js App!</h1>; }\n";

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'nextjs-starter',
                'version' => '1.0.0',
                'private' => true,
                'scripts' => [
                    'dev' => 'next dev',
                    'build' => 'next build',
                    'start' => 'next start'
                ],
                'dependencies' => [
                    'next' => '13.4.12',
                    'react' => '18.2.0',
                    'react-dom' => '18.2.0'
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
