<?php

namespace App\Services\Architect\Providers;

use App\Services\Architect\Contracts\FrameworkProviderInterface;

class OdooProvider implements FrameworkProviderInterface
{
    public function getName(): string
    {
        return 'odoo';
    }

    public function getFolderStructure(): array
    {
        return [
            'models',
            'views',
            'security',
            'data'
        ];
    }

    public function getPlaceholderFiles(array $data): array
    {
        $files = [];
        $schema = $data['schema'] ?? [];
        $codeGenModel = $data['code_gen_model'] ?? [];
        $mappings = $codeGenModel['mappings'] ?? [];
        $tables = $schema['tables'] ?? [];

        $modelTpl = $this->loadTemplate('odoo', 'model.py.tpl', "# -*- coding: utf-8 -*-\nfrom odoo import models, fields\n\nclass {{MODEL_NAME}}(models.Model):\n    _name = 'aitos.{{TABLE_NAME_SINGULAR}}'\n    _description = 'AITOS generated model for {{MODEL_NAME}}'\n{{COLUMNS_SCHEMA}}\n");
        $accessTpl = $this->loadTemplate('odoo', 'ir.model.access.csv.tpl', "id,name,model_id:id,group_id:id,perm_read,perm_write,perm_create,perm_unlink\naccess_aitos_{{TABLE_NAME_SINGULAR}},access_aitos_{{TABLE_NAME_SINGULAR}},model_aitos_{{TABLE_NAME_SINGULAR}},base.group_user,1,1,1,1\n");

        $accessRows = [];
        foreach ($mappings as $mapping) {
            $entityName = $mapping['entity'];
            $modelName = $mapping['model'];
            $routeName = $mapping['route'];
            $singularName = rtrim($routeName, 's');

            // Fetch columns from table schema
            $tableColumns = [];
            foreach ($tables as $t) {
                if ($t['name'] === $routeName || rtrim($t['name'], 's') === rtrim($routeName, 's')) {
                    $tableColumns = $t['columns'] ?? [];
                    break;
                }
            }

            // Build Odoo Char/Integer fields
            $fieldsCode = "";
            foreach ($tableColumns as $col) {
                $name = $col['name'];
                if ($name === 'id') continue;
                $type = strtolower($col['type']);
                if (str_contains($type, 'integer')) {
                    $fieldsCode .= "    {$name} = fields.Integer(string='{$name}')\n";
                } elseif (str_contains($type, 'text')) {
                    $fieldsCode .= "    {$name} = fields.Text(string='{$name}')\n";
                } else {
                    $fieldsCode .= "    {$name} = fields.Char(string='{$name}')\n";
                }
            }
            if (empty($fieldsCode)) {
                $fieldsCode = "    name = fields.Char(string='Name')\n";
            }

            // Generate Odoo Model
            $modelContent = str_replace(
                ['{{MODEL_NAME}}', '{{TABLE_NAME_SINGULAR}}', '{{COLUMNS_SCHEMA}}'],
                [$modelName, $singularName, $fieldsCode],
                $modelTpl
            );
            $files["models/{$singularName}.py"] = $modelContent;

            // Generate Odoo access mapping CSV line
            $accessRows[] = str_replace('{{TABLE_NAME_SINGULAR}}', $singularName, trim($accessTpl));
        }

        // Add access CSV file
        $files["security/ir.model.access.csv"] = implode("\n", array_unique($accessRows)) . "\n";

        // Generate dynamic views.xml
        $xmlViews = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<odoo>\n    <data>\n";
        foreach ($mappings as $mapping) {
            $singularName = rtrim($mapping['route'], 's');
            $xmlViews .= "        <!-- View for {$mapping['entity']} -->\n";
            $xmlViews .= "        <record id=\"view_aitos_{$singularName}_form\" model=\"ir.ui.view\">\n";
            $xmlViews .= "            <field name=\"name\">aitos.{$singularName}.form</field>\n";
            $xmlViews .= "            <field name=\"model\">aitos.{$singularName}</field>\n";
            $xmlViews .= "            <field name=\"arch\" type=\"xml\">\n";
            $xmlViews .= "                <form string=\"{$mapping['entity']}\">\n";
            $xmlViews .= "                    <sheet>\n";
            $xmlViews .= "                        <group>\n";
            $xmlViews .= "                            <field name=\"name\"/>\n";
            $xmlViews .= "                        </group>\n";
            $xmlViews .= "                    </sheet>\n";
            $xmlViews .= "                </form>\n";
            $xmlViews .= "            </field>\n";
            $xmlViews .= "        </record>\n";
        }
        $xmlViews .= "    </data>\n</odoo>\n";
        $files["views/views.xml"] = $xmlViews;

        return $files;
    }

    public function getConfigFileTemplates(): array
    {
        return [
            '__manifest__.py' => "# -*- coding: utf-8 -*-\n{\n    'name': 'AITOS Odoo Module',\n    'version': '1.0.0',\n    'summary': 'Scaffolded ERP module representing project requirements.',\n    'category': 'Extra Tools',\n    'depends': ['base'],\n    'data': [\n        'security/ir.model.access.csv',\n        'views/views.xml'\n    ],\n    'installable': True,\n    'application': True,\n    'auto_install': False,\n}\n"
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
