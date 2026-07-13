<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class LaravelProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'laravel';
    }

    public function getFolderStructure(): array
    {
        return [
            'app/Http/Controllers',
            'app/Models',
            'app/Services',
            'app/Repositories',
            'database/migrations',
            'routes',
            'config',
            'resources/views'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];
        $tables = $schema['tables'] ?? [];

        // Load template files or use fallbacks if not readable
        $modelTpl = $this->loadTemplate('laravel', 'Model.php.tpl', "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass {{MODEL_NAME}} extends Model\n{\n    protected \$guarded = [];\n}\n");
        $controllerTpl = $this->loadTemplate('laravel', 'Controller.php.tpl', "<?php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Models\\{{MODEL_NAME}};\nuse Illuminate\\Http\\Request;\n\nclass {{CONTROLLER_NAME}} extends Controller\n{\n    public function index() { return response()->json({{MODEL_NAME}}::all()); }\n}\n");
        $migrationTpl = $this->loadTemplate('laravel', 'Migration.php.tpl', "<?php\n\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\n\nreturn new class extends Migration {\n    public function up() {\n        Schema::create('{{TABLE_NAME}}', function (Blueprint \$table) {\n            \$table->id();\n{{COLUMNS_SCHEMA}}            \$table->timestamps();\n        });\n    }\n    public function down() { Schema::dropIfExists('{{TABLE_NAME}}'); }\n};\n");
        $serviceTpl = $this->loadTemplate('laravel', 'Service.php.tpl', "<?php\n\nnamespace App\\Services;\n\nclass {{SERVICE_NAME}} {\n    public function handle() {}\n}\n");
        $repositoryTpl = $this->loadTemplate('laravel', 'Repository.php.tpl', "<?php\n\nnamespace App\\Repositories;\n\nclass {{REPOSITORY_NAME}} {\n    public function all() {}\n}\n");

        foreach ($mappings as $mapping) {
            $entity = $mapping['entity'];
            $modelName = $mapping['model'];
            $controllerName = $mapping['controller'];
            $routeName = $mapping['route'];
            $migrationName = date('Y_m_d_His') . '_' . $mapping['migration'] . '.php';
            $serviceName = $mapping['service'];
            $repositoryName = $mapping['repository'];

            // Match against table in schema to get columns
            $tableColumns = [];
            foreach ($tables as $t) {
                if ($t['name'] === $routeName || rtrim($t['name'], 's') === rtrim($routeName, 's')) {
                    $tableColumns = $t['columns'] ?? [];
                    break;
                }
            }

            // Generate Model
            $files["app/Models/{$modelName}.php"] = str_replace('{{MODEL_NAME}}', $modelName, $modelTpl);

            // Generate Controller
            $controllerContent = str_replace(
                ['{{MODEL_NAME}}', '{{CONTROLLER_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $controllerName, $routeName],
                $controllerTpl
            );
            $files["app/Http/Controllers/{$controllerName}.php"] = $controllerContent;

            // Generate Service
            $files["app/Services/{$serviceName}.php"] = str_replace(
                ['{{MODEL_NAME}}', '{{SERVICE_NAME}}'],
                [$modelName, $serviceName],
                $serviceTpl
            );

            // Generate Repository
            $files["app/Repositories/{$repositoryName}.php"] = str_replace(
                ['{{MODEL_NAME}}', '{{REPOSITORY_NAME}}'],
                [$modelName, $repositoryName],
                $repositoryTpl
            );

            // Generate Migration
            $columnsStr = '';
            foreach ($tableColumns as $col) {
                $name = $col['name'];
                if ($name === 'id') continue;
                
                $type = strtolower($col['type']);
                if (str_contains($type, 'varchar')) {
                    $columnsStr .= "            \$table->string('{$name}');\n";
                } elseif (str_contains($type, 'text')) {
                    $columnsStr .= "            \$table->text('{$name}');\n";
                } elseif (str_contains($type, 'integer')) {
                    $columnsStr .= "            \$table->integer('{$name}');\n";
                } else {
                    $columnsStr .= "            \$table->string('{$name}');\n";
                }
            }

            $migrationContent = str_replace(
                ['{{TABLE_NAME}}', '{{COLUMNS_SCHEMA}}'],
                [$routeName, $columnsStr],
                $migrationTpl
            );
            $files["database/migrations/{$migrationName}"] = $migrationContent;
        }

        // Generate api.php routes config using model mappings
        $routesCode = "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::middleware('api')->group(function () {\n";
        foreach ($mappings as $mapping) {
            $routeName = $mapping['route'];
            $controllerName = $mapping['controller'];
            $routesCode .= "    Route::apiResource('{$routeName}', \\App\\Http\\Controllers\\{$controllerName}::class);\n";
        }
        $routesCode .= "});\n";
        $files["routes/api.php"] = $routesCode;

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            'composer.json' => json_encode([
                'name' => 'laravel/laravel',
                'description' => 'AITOS generated Laravel scaffolding.',
                'type' => 'project',
                'require' => [
                    'php' => '^8.2',
                    'laravel/framework' => '^11.0'
                ],
                'autoload' => [
                    'psr-4' => [
                        'App\\' => 'app/'
                    ]
                ],
                'config' => [
                    'optimize-autoloader' => true,
                    'sort-packages' => true
                ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
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
