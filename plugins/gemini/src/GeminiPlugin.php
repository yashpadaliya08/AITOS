<?php

namespace Plugins\Gemini;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\Providers\GeminiProvider;

class GeminiPlugin implements PluginInterface
{
    public function getName(): string { return "Gemini AI Provider Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates Google Gemini model configurations."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        AIProviderFactory::register('gemini', new GeminiProvider());
    }
    public function uninstall(): bool { return true; }
}
