<?php

namespace App\Services\Architect\Engines;

use App\Services\Architect\Contracts\EngineInterface;
use App\Services\Architect\DTO\EngineResultDto;
use App\Services\Architect\DTO\ProjectModelDTO;
use App\Services\Architect\DTO\UiDTO;
use App\Services\Architect\Validators\UiValidator;

class UiGenerator implements EngineInterface
{
    public function run(array $state): EngineResultDto
    {
        $start = microtime(true);
        $projectModel = new ProjectModelDTO($state);
        
        $pages = [
            [
                'name' => 'Dashboard Overview',
                'path' => '/dashboard',
                'components' => ['MetricsSummaryGrid', 'RecentActivityFeed', 'AnalyticsChartPanel'],
                'responsive_notes' => 'Reflow to single column on mobile, metrics cards stacked.'
            ]
        ];

        foreach ($projectModel->modules as $module) {
            $cleanName = trim(preg_replace('/[^a-zA-Z0-9 ]/', '', $module));
            if (empty($cleanName)) continue;
            
            $slug = strtolower(str_replace(' ', '-', $cleanName));
            $camel = str_replace(' ', '', $cleanName);

            $pages[] = [
                'name' => "{$cleanName} Management",
                'path' => "/{$slug}",
                'components' => [
                    "{$camel}Sidebar",
                    "{$camel}DataTableList",
                    "{$camel}FilterPanelBar",
                    "{$camel}CreateEditModalForm"
                ],
                'responsive_notes' => 'Table components will scroll horizontally, forms reflow stack.'
            ];
        }

        $links = array_map(function ($page) {
            return [
                'label' => $page['name'],
                'path' => $page['path']
            ];
        }, $pages);

        $uiDto = new UiDTO([
            'pages' => $pages,
            'navigation' => [
                'type' => 'sidebar',
                'links' => $links
            ]
        ]);

        $duration = (microtime(true) - $start) * 1000;

        return new EngineResultDto([
            'success' => true,
            'warnings' => [],
            'errors' => [],
            'generated_file' => 'ui_blueprint.json',
            'execution_time' => $duration,
            'data' => $uiDto->toArray()
        ]);
    }

    public function validate(array $output): array
    {
        return UiValidator::validate($output);
    }

    public function getName(): string
    {
        return 'UiGenerator';
    }

    public function getDependencies(): array
    {
        return ['projectModel'];
    }
}
