# AITOS Plugin Code Examples

## Example: Custom Logging Plugin

```php
<?php

namespace Plugins\CustomLogger;

use App\Services\Architect\Contracts\PluginInterface;
use App\Services\Architect\Registry\HookSystem;

class CustomLoggerPlugin implements PluginInterface
{
    public function getName(): string { return "Custom Logger"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "Operator"; }
    public function getDescription(): string { return "Logs export operations to standard streams."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void
    {
        HookSystem::addAction('AfterExport', function($state) {
            file_put_contents(storage_path('logs/export_history.log'), "Exported: " . $state['projectName'] . "\n", FILE_APPEND);
        });
    }
    public function register(): void {}
    public function uninstall(): bool { return true; }
}
```
