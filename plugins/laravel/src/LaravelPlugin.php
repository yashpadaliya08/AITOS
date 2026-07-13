<?php

namespace Plugins\Laravel;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\Architect\Registry\FrameworkRegistry;
use App\Services\Architect\Providers\LaravelProvider;

class LaravelPlugin implements PluginInterface
{
    public function getName(): string { return "Laravel Framework Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates Laravel framework code generation maps."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        FrameworkRegistry::register('laravel', new LaravelProvider());
    }
    public function uninstall(): bool { return true; }
}
