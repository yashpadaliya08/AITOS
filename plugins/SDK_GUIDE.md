# AITOS Plugin SDK Guide (Version 2.0)

Welcome to the AITOS Platform SDK! This guide shows how to write custom plugins to extend the compiler core without modifying the codebase.

## Directory Structure
Every plugin is located under `plugins/<plugin-name>/` and has the following folder layout:
```text
plugins/my_plugin/
├── plugin.json
├── README.md
└── src/
    └── MyPlugin.php
```

## Creating `plugin.json` Manifest
The manifest describes the plugin, core compatibility boundaries, and entry point mappings:
```json
{
    "name": "My Custom Plugin",
    "version": "1.0.0",
    "author": "Developer Name",
    "description": "Short description of the custom extensions.",
    "required_core_version": "1.5.0",
    "entrypoint": "MyPlugin",
    "namespace": "Plugins\\MyPlugin"
}
```

## Implementing `PluginInterface`
All entry point classes must implement the `PluginInterface` contract:
```php
<?php

namespace Plugins\MyPlugin;

use App\Services\Architect\Contracts\PluginInterface;

class MyPlugin implements PluginInterface
{
    public function getName(): string { return "My Custom Plugin"; }
    public function getVersion(): string { return "1.0.0"; }
    public function getAuthor(): string { return "Developer Name"; }
    public function getDescription(): string { return "Extends the core."; }
    public function getRequiredCoreVersion(): string { return "1.5.0"; }
    public function install(): bool { return true; }
    public function boot(): void {}
    public function register(): void {}
    public function uninstall(): bool { return true; }
}
```
