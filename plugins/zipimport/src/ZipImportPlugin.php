<?php

namespace Plugins\ZipImport;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\Architect\Registry\HookSystem;

class ZipImportPlugin implements PluginInterface
{
    public function getName(): string { return "ZIP Import Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Enables importing pre-compiled .aitos ZIP packages."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void
    {
        HookSystem::addAction('PluginLoaded', function($key, $instance) {
            \Illuminate\Support\Facades\Log::info("ZipImportPlugin: Triggered confirmation for plugin loader {$key}.");
        });
    }
    public function register(): void {}
    public function uninstall(): bool { return true; }
}
