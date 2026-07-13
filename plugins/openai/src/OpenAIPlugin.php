<?php

namespace Plugins\OpenAI;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\Providers\OpenAIProvider;

class OpenAIPlugin implements PluginInterface
{
    public function getName(): string { return "OpenAI Provider Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates OpenAI model configurations."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        AIProviderFactory::register('openai', new OpenAIProvider());
    }
    public function uninstall(): bool { return true; }
}
