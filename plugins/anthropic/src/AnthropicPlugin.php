<?php

namespace Plugins\Anthropic;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\AI\AIProviderFactory;
use App\Services\AI\Providers\AnthropicProvider;

class AnthropicPlugin implements PluginInterface
{
    public function getName(): string { return "Anthropic Provider Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "AITOS Platform"; }
    public function getDescription(): string { return "Integrates Anthropic Claude model configurations."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void
    {
        AIProviderFactory::register('anthropic', new AnthropicProvider());
    }
    public function uninstall(): bool { return true; }
}
