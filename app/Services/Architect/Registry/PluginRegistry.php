<?php

namespace App\Services\Architect\Registry;

use App\Services\Architect\Contracts\PluginInterface;
use Illuminate\Support\Facades\Log;

class PluginRegistry
{
    protected static array $plugins = [];
    protected static array $activePlugins = [];
    protected static string $pluginsPath = '';

    /**
     * Set up the plugin scanning path and create directory if missing.
     */
    public static function init(): void
    {
        self::$pluginsPath = base_path('plugins');
        if (!is_dir(self::$pluginsPath)) {
            mkdir(self::$pluginsPath, 0755, true);
        }
    }

    /**
     * Scan the plugins directory, find plugin.json files, and register manifest objects.
     */
    public static function scan(): array
    {
        self::init();
        self::$plugins = [];

        $dirs = glob(self::$pluginsPath . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $manifestPath = $dir . '/plugin.json';
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                if ($manifest && isset($manifest['name'], $manifest['entrypoint'])) {
                    $pluginKey = basename($dir);
                    $manifest['path'] = $dir;
                    $manifest['enabled'] = self::isPluginEnabled($pluginKey);
                    self::$plugins[$pluginKey] = $manifest;
                }
            }
        }

        return self::$plugins;
    }

    /**
     * Get all scanned plugins manifest maps.
     */
    public static function getPlugins(): array
    {
        return self::$plugins;
    }

    /**
     * Check if a specific plugin key is enabled.
     */
    public static function isPluginEnabled(string $key): bool
    {
        // For local simulation, we can store active state in a local file inside config/plugins.json
        $configPath = storage_path('app/plugins_config.json');
        if (!file_exists($configPath)) {
            // Default: all scanned plugins are enabled!
            return true;
        }

        $config = json_decode(file_get_contents($configPath), true) ?? [];
        return $config[$key] ?? true;
    }

    /**
     * Set plugin active/disabled state.
     */
    public static function setPluginStatus(string $key, bool $enabled): void
    {
        $configPath = storage_path('app/plugins_config.json');
        $config = [];
        if (file_exists($configPath)) {
            $config = json_decode(file_get_contents($configPath), true) ?? [];
        }

        $config[$key] = $enabled;
        file_put_contents($configPath, json_encode($config, JSON_PRETTY_PRINT));
    }

    /**
     * Register a booted plugin instance.
     */
    public static function registerInstance(string $key, PluginInterface $instance): void
    {
        self::$activePlugins[$key] = $instance;
    }

    /**
     * Get active plugin instances.
     */
    public static function getActivePlugins(): array
    {
        return self::$activePlugins;
    }
}
