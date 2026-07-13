<?php

namespace App\Services\Architect\Contracts;

interface PluginInterface
{
    public function getName(): string;

    public function getVersion(): string;

    public function getAuthor(): string;

    public function getDescription(): string;

    public function getRequiredCoreVersion(): string;

    public function install(): bool;

    public function boot(): void;

    public function register(): void;

    public function uninstall(): bool;
}
