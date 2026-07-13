<?php

namespace Plugins\Next;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\Architect\Registry\FrameworkRegistry;
use App\Services\Architect\Providers\NextProvider;

class NextPlugin implements PluginInterface
{
    public function getName(): string { return "Next.js Framework Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates Next.js framework code generation maps."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        FrameworkRegistry::register('next', new NextProvider());
    }
    public function uninstall(): bool { return true; }
}
