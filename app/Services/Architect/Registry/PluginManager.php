<?php

namespace App\Services\Architect\Registry;

use App\Services\Architect\Contracts\PluginInterface;
use Illuminate\Support\Facades\Log;

class PluginManager
{
    /**
     * Cached scan results to avoid repeated directory scans.
     */
    private static ?array $cachedManifests = null;

    /**
     * Scan the plugins directory and boot all enabled plugin modules.
     */
    public static function boot(): void
    {
        $plugins = self::getManifests();

        foreach ($plugins as $key => $manifest) {
            if ($manifest['enabled'] === true) {
                self::loadPlugin($key, $manifest);
            }
        }
    }

    /**
     * Load, validate, and boot a single plugin from its manifest.
     */
    public static function loadPlugin(string $key, array $manifest): bool
    {
        $entrypointFile = $manifest['path'] . '/src/' . $manifest['entrypoint'] . '.php';

        // Security: ensure the resolved path is strictly within the plugins directory
        $realPath       = realpath($entrypointFile);
        $pluginsBasePath = realpath(base_path('plugins'));

        if ($realPath === false || $pluginsBasePath === false || !str_starts_with($realPath, $pluginsBasePath)) {
            Log::warning("PluginManager: Blocked path traversal attempt for plugin '{$key}': {$entrypointFile}");
            return false;
        }

        if (!file_exists($realPath)) {
            Log::warning("PluginManager: Entrypoint file missing for plugin '{$key}': {$realPath}");
            return false;
        }

        try {
            require_once $realPath;

            $namespace = $manifest['namespace'] ?? '';
            $className = $namespace ? $namespace . '\\' . $manifest['entrypoint'] : $manifest['entrypoint'];

            if (!class_exists($className)) {
                Log::warning("PluginManager: Class '{$className}' not found after loading plugin '{$key}'.");
                return false;
            }

            $instance = new $className();

            if (!($instance instanceof PluginInterface)) {
                Log::warning("PluginManager: Plugin '{$key}' class does not implement PluginInterface.");
                return false;
            }

            if (!self::validateCompatibility($instance)) {
                Log::error("PluginManager: Compatibility validation failed for plugin '{$key}'.");
                return false;
            }

            $instance->register();
            $instance->boot();
            PluginRegistry::registerInstance($key, $instance);
            HookSystem::doAction('PluginLoaded', $key, $instance);

            return true;

        } catch (\Throwable $e) {
            Log::error("PluginManager: Failed to load plugin '{$key}': " . $e->getMessage());
        }

        return false;
    }

    /**
     * Validate plugin compatibility against the current core engine version.
     */
    public static function validateCompatibility(PluginInterface $plugin): bool
    {
        $coreVersion    = '1.5.0';
        $requiredVersion = $plugin->getRequiredCoreVersion();

        return version_compare($coreVersion, $requiredVersion, '>=');
    }

    /**
     * Enable a specific plugin and boot it immediately.
     */
    public static function enablePlugin(string $key): bool
    {
        PluginRegistry::setPluginStatus($key, true);
        self::$cachedManifests = null; // Invalidate cache so next call rescans

        $manifests = self::getManifests();
        if (isset($manifests[$key])) {
            return self::loadPlugin($key, $manifests[$key]);
        }

        return false;
    }

    /**
     * Disable a specific plugin and call its uninstall lifecycle hook.
     */
    public static function disablePlugin(string $key): void
    {
        PluginRegistry::setPluginStatus($key, false);
        self::$cachedManifests = null; // Invalidate cache

        $activeInstances = PluginRegistry::getActivePlugins();
        if (isset($activeInstances[$key])) {
            $activeInstances[$key]->uninstall();
        }
    }

    /**
     * Return cached plugin manifests, scanning the directory only once per request.
     */
    private static function getManifests(): array
    {
        if (self::$cachedManifests === null) {
            self::$cachedManifests = PluginRegistry::scan();
        }

        return self::$cachedManifests;
    }
}
