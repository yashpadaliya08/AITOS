# AITOS Plugin API Reference

## Hook System Hooks

Plugins can hook into various compiler events using the static `HookSystem` interface.

### Registering Action Callbacks
```php
use App\Services\Architect\Registry\HookSystem;

HookSystem::addAction('BeforeExport', function($state) {
    // Run action before package export zip generates
});
```

### Triggering Custom Filters
```php
$filteredValue = HookSystem::applyFilters('BeforeCompile', $initialValue);
```

### Available Core Tags:
- `BeforeExport`: Executed right before ZIP is compiled.
- `AfterExport`: Executed after ZIP compilation finishes.
- `BeforeCompile`: Fired prior to running compiler stages.
- `AfterCompile`: Fired once compiler stages complete.
- `PluginLoaded`: Triggered when a plugin boots.
- `RepositoryGenerated`: Triggered on skeletal file writes.
