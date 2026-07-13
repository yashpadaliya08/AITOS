<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class FastAPIProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'fastapi';
    }

    public function getFolderStructure(): array
    {
        return [
            'app/api',
            'app/core',
            'app/models',
            'app/schemas',
            'app/crud'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];
        $tables = $schema['tables'] ?? [];

        $modelTpl = $this->loadTemplate('fastapi', 'model.py.tpl', "from sqlalchemy import Column, Integer, String\nfrom app.database import Base\n\nclass {{MODEL_NAME}}(Base):\n    __tablename__ = \"{{TABLE_NAME}}\"\n{{COLUMNS_SCHEMA}}\n");
        $schemaTpl = $this->loadTemplate('fastapi', 'schema.py.tpl', "from pydantic import BaseModel\n\nclass {{MODEL_NAME}}Base(BaseModel):\n{{COLUMNS_SCHEMA}}\nclass {{MODEL_NAME}}Create({{MODEL_NAME}}Base):\n    pass\nclass {{MODEL_NAME}}({{MODEL_NAME}}Base):\n    id: int\n    class Config:\n        orm_mode = True\n");
        $routerTpl = $this->loadTemplate('fastapi', 'router.py.tpl', "from fastapi import APIRouter\nfrom typing import List\nfrom app.schemas.{{MODEL_FILE_NAME}} import {{MODEL_NAME}}, {{MODEL_NAME}}Create\nrouter = APIRouter(prefix=\"/{{ROUTE_NAME}}\")\n");

        foreach ($mappings as $mapping) {
            $entityName = $mapping['entity'];
            $modelName = $mapping['model'];
            $routeName = $mapping['route'];
            $modelFile = strtolower($entityName);

            // Fetch columns from table schema
            $tableColumns = [];
            foreach ($tables as $t) {
                if ($t['name'] === $routeName || rtrim($t['name'], 's') === rtrim($routeName, 's')) {
                    $tableColumns = $t['columns'] ?? [];
                    break;
                }
            }

            // Build sqlalchemy column schema code block
            $dbColumnsCode = "";
            foreach ($tableColumns as $col) {
                $name = $col['name'];
                $type = strtolower($col['type']);
                if ($name === 'id') {
                    $dbColumnsCode .= "    id = Column(Integer, primary_key=True, index=True)\n";
                } elseif (str_contains($type, 'integer')) {
                    $dbColumnsCode .= "    {$name} = Column(Integer)\n";
                } elseif (str_contains($type, 'text')) {
                    $dbColumnsCode .= "    {$name} = Column(Text)\n";
                } else {
                    $dbColumnsCode .= "    {$name} = Column(String(255))\n";
                }
            }
            if (empty($dbColumnsCode)) {
                $dbColumnsCode = "    id = Column(Integer, primary_key=True, index=True)\n";
            }

            // Build Pydantic columns code block
            $pyColumnsCode = "";
            foreach ($tableColumns as $col) {
                $name = $col['name'];
                if ($name === 'id') continue;
                $type = strtolower($col['type']);
                if (str_contains($type, 'integer')) {
                    $pyColumnsCode .= "    {$name}: int\n";
                } else {
                    $pyColumnsCode .= "    {$name}: str\n";
                }
            }
            if (empty($pyColumnsCode)) {
                $pyColumnsCode = "    name: str\n";
            }

            // Generate SQLAlchemy model file
            $modelContent = str_replace(
                ['{{MODEL_NAME}}', '{{TABLE_NAME}}', '{{COLUMNS_SCHEMA}}'],
                [$modelName, $routeName, $dbColumnsCode],
                $modelTpl
            );
            $files["app/models/{$modelFile}.py"] = $modelContent;

            // Generate Pydantic schemas file
            $schemaContent = str_replace(
                ['{{MODEL_NAME}}', '{{COLUMNS_SCHEMA}}'],
                [$modelName, $pyColumnsCode],
                $schemaTpl
            );
            $files["app/schemas/{$modelFile}.py"] = $schemaContent;

            // Generate Router API Endpoint file
            $routerContent = str_replace(
                ['{{MODEL_NAME}}', '{{MODEL_FILE_NAME}}', '{{ROUTE_NAME}}'],
                [$modelName, $modelFile, $routeName],
                $routerTpl
            );
            $files["app/api/{$modelFile}.py"] = $routerContent;
        }

        // Generate main.py containing dynamic includes
        $mainPyCode = "import uvicorn\nfrom fastapi import FastAPI\n";
        foreach ($mappings as $mapping) {
            $entityName = $mapping['entity'];
            $modelFile = strtolower($entityName);
            $mainPyCode .= "from app.api.{$modelFile} import router as {$modelFile}_router\n";
        }
        $mainPyCode .= "\napp = FastAPI(title=\"AITOS FastAPI Starter\")\n\n";
        foreach ($mappings as $mapping) {
            $modelFile = strtolower($mapping['entity']);
            $mainPyCode .= "app.include_router({$modelFile}_router)\n";
        }
        $mainPyCode .= "\n@app.get('/')\ndef index():\n    return {'status': 'running'}\n";
        $files["main.py"] = $mainPyCode;

        // Generate dummy app/database.py to prevent import failures
        $files["app/database.py"] = "from sqlalchemy import create_engine\nfrom sqlalchemy.ext.declarative import declarative_base\nfrom sqlalchemy.orm import sessionmaker\n\nengine = create_engine('sqlite:///./test.db')\nSessionLocal = sessionmaker(bind=engine)\nBase = declarative_base()\n";

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            'requirements.txt' => "fastapi>=0.100.0\nuvicorn>=0.22.0\nsqlalchemy>=2.0.0\npydantic>=2.0.0\npython-dotenv>=1.0.0"
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
