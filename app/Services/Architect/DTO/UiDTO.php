<?php

namespace App\Services\Architect\DTO;

class UiDTO
{
    public array $pages = [];
    public array $navigation = [];

    public function __construct(array $data = [])
    {
        $this->pages = $data['pages'] ?? [];
        $this->navigation = $data['navigation'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'pages' => $this->pages,
            'navigation' => $this->navigation,
        ];
    }
}
