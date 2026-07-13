<?php

namespace App\Services\Architect\Events;

class DocumentationGenerated
{
    public array $state;

    public function __construct(array $state)
    {
        $this->state = $state;
    }
}
