<?php

namespace Monarul007\LaravelModularSystem\Core;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class ModuleManager
{
    protected array $enabledModules = [];
    protected array $moduleConfigs = [];

    public function __construct()
    {
        $this->loadEnabledModules();
    }

    public function loadEnabledModules(): void
    {
        $cacheKey = 'modular_system.enabled_modules';
        $cacheTtl = config('modular-system.cache_ttl', 3600);

        $enabledModules = config('modular-system.cache_enabled', true)
            ? Cache::remember($cacheKey, $cacheTtl, fn() => $this->readEnabledModulesFile())
            : $this->readEnabledModulesFile();

        $this->enabledModules = $enabledModules;
        
        // Clean up stale entries (modules that no longer exist)
        $this->cleanupStaleModules();
    }
    
    protected function cleanupStaleModules(): void
    {
        $originalCount = count($this->enabledModules);
        $this->enabledModules = array_filter($this->enabledModules, function($moduleName) {
            return $this->moduleExists($moduleName);
        });
        
        // If any modules were removed, save the updated list
        if (count($this->enabledModules) !== $originalCount) {
            $this->enabledModules = array_values($this->enabledModules);
            $this->saveEnabledModules();
            $this->clearCache();
        }
    }

    protected function readEnabledModulesFile(): array
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configFile = config('modular-system.enabled_modules_file', 'enabled.json');
        $configPath = "{$modulesPath}/{$configFile}";

        if (!File::exists($configPath)) {
            File::put($configPath, json_encode([]));
            return [];
        }

        return json_decode(File::get($configPath), true) ?? [];
    }

    public function getEnabledModules(): array
    {
        return $this->enabledModules;
    }

    public function isModuleEnabled(string $moduleName): bool
    {
        return in_array($moduleName, $this->enabledModules);
    }

    public function enableModule(string $moduleName): bool
    {
        if (!$this->moduleExists($moduleName)) {
            return false;
        }

        if (!in_array($moduleName, $this->enabledModules)) {
            $this->enabledModules[] = $moduleName;
            $this->saveEnabledModules();
            $this->clearCache();
        }

        return true;
    }

    public function disableModule(string $moduleName): bool
    {
        $key = array_search($moduleName, $this->enabledModules);

        if ($key !== false) {
            unset($this->enabledModules[$key]);
            $this->enabledModules = array_values($this->enabledModules);
            $this->saveEnabledModules();
            $this->clearCache();
        }

        return true;
    }

    public function getModuleConfig(string $moduleName): ?array
    {
        if (isset($this->moduleConfigs[$moduleName])) {
            return $this->moduleConfigs[$moduleName];
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configFile = config('modular-system.module_config_file', 'module.json');
        $configPath = "{$modulesPath}/{$moduleName}/{$configFile}";

        if (!File::exists($configPath)) {
            return null;
        }

        $config = json_decode(File::get($configPath), true);
        $this->moduleConfigs[$moduleName] = $config;

        return $config;
    }

    public function getAllModules(): array
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));

        if (!File::exists($modulesPath)) {
            return [];
        }

        $modules = [];
        $directories = File::directories($modulesPath);

        foreach ($directories as $directory) {
            $moduleName = basename($directory);
            $config = $this->getModuleConfig($moduleName);

            if ($config) {
                $modules[$moduleName] = array_merge($config, [
                    'enabled' => $this->isModuleEnabled($moduleName),
                    'path' => $directory
                ]);
            }
        }

        return $modules;
    }

    public function moduleExists(string $moduleName): bool
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configFile = config('modular-system.module_config_file', 'module.json');
        
        return File::exists("{$modulesPath}/{$moduleName}/{$configFile}");
    }

    protected function saveEnabledModules(): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configFile = config('modular-system.enabled_modules_file', 'enabled.json');
        $configPath = "{$modulesPath}/{$configFile}";
        
        File::put($configPath, json_encode($this->enabledModules, JSON_PRETTY_PRINT));
    }

    protected function clearCache(): void
    {
        if (config('modular-system.cache_enabled', true)) {
            Cache::forget('modular_system.enabled_modules');
        }
    }

    public function bootModules(): void
    {
        foreach ($this->enabledModules as $moduleName) {
            $this->bootModule($moduleName);
        }
    }

    protected function bootModule(string $moduleName): void
    {
        $config = $this->getModuleConfig($moduleName);

        if (!$config || !isset($config['providers'])) {
            return;
        }

        foreach ($config['providers'] as $provider) {
            if (class_exists($provider)) {
                app()->register($provider);
            }
        }
    }

    public function installModuleFromZip(string $zipPath, string $moduleName = null): array
    {
        $zip = new \ZipArchive();

        if ($zip->open($zipPath) !== TRUE) {
            return ['success' => false, 'message' => 'Could not open ZIP file'];
        }

        $tempDir = sys_get_temp_dir() . '/module_' . uniqid();

        if (!$zip->extractTo($tempDir)) {
            $zip->close();
            return ['success' => false, 'message' => 'Could not extract ZIP file'];
        }

        $zip->close();

        $moduleJsonPath = $this->findModuleJson($tempDir);

        if (!$moduleJsonPath) {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => 'No module.json found in ZIP file'];
        }

        $moduleConfig = json_decode(File::get($moduleJsonPath), true);

        if (!$moduleConfig || !isset($moduleConfig['name'])) {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => 'Invalid module.json format'];
        }

        $detectedModuleName = $moduleConfig['name'];
        $finalModuleName = $moduleName ?: $detectedModuleName;

        if ($this->moduleExists($finalModuleName)) {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => "Module '{$finalModuleName}' already exists"];
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $moduleDir = "{$modulesPath}/{$finalModuleName}";
        $extractedModuleDir = dirname($moduleJsonPath);

        if (File::exists($moduleDir)) {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => "Target directory already exists: {$moduleDir}"];
        }

        File::ensureDirectoryExists(dirname($moduleDir));

        if (!File::copyDirectory($extractedModuleDir, $moduleDir)) {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => "Could not copy module files"];
        }

        File::deleteDirectory($tempDir);

        if ($finalModuleName !== $detectedModuleName) {
            $moduleConfig['name'] = $finalModuleName;
            File::put("{$moduleDir}/module.json", json_encode($moduleConfig, JSON_PRETTY_PRINT));
        }

        return [
            'success' => true,
            'message' => "Module '{$finalModuleName}' installed successfully",
            'module' => $moduleConfig
        ];
    }

    protected function findModuleJson(string $directory): ?string
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->getFilename() === 'module.json') {
                return $file->getPathname();
            }
        }

        return null;
    }

    public function uninstallModule(string $moduleName): bool
    {
        if (!$this->moduleExists($moduleName)) {
            return false;
        }

        $this->disableModule($moduleName);

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $moduleDir = "{$modulesPath}/{$moduleName}";

        return File::deleteDirectory($moduleDir);
    }

    public function createModuleZip(string $moduleName): ?string
    {
        if (!$this->moduleExists($moduleName)) {
            return null;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $moduleDir = "{$modulesPath}/{$moduleName}";
        $zipPath = storage_path("app/modules/{$moduleName}.zip");

        File::ensureDirectoryExists(dirname($zipPath));

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return null;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($moduleDir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($moduleDir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return $zipPath;
    }
}
