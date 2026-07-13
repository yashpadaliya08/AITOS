<?php

namespace App\Services\Architect\Events;

class DatabaseGenerated
{
    public array $state;

    public function __construct(array $state)
    {
        $this->state = $state;
    }
}
