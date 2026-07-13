<?php

namespace App\Services\Architect\Events;

class ApiGenerated
{
    public array $state;

    public function __construct(array $state)
    {
        $this->state = $state;
    }
}
