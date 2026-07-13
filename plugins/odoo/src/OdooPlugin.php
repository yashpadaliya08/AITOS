<?php

namespace Plugins\Odoo;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\Architect\Registry\FrameworkRegistry;
use App\Services\Architect\Providers\OdooProvider;

class OdooPlugin implements PluginInterface
{
    public function getName(): string { return "Odoo Framework Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates Odoo framework code generation maps."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        FrameworkRegistry::register('odoo', new OdooProvider());
    }
    public function uninstall(): bool { return true; }
}
