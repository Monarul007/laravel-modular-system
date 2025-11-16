<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModuleSetAliasCommand extends Command
{
    protected $signature = 'module:set-alias {module : The module name} {alias? : The route alias} {--force : Skip confirmation}';
    protected $description = 'Set or change the route alias for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $newAlias = $this->argument('alias');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        // Get current configuration
        $config = $moduleManager->getModuleConfig($moduleName);
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configPath = "{$modulesPath}/{$moduleName}/config/{$moduleName}.php";

        // Get current alias
        $currentAlias = null;
        if (File::exists($configPath)) {
            $configArray = include $configPath;
            $currentAlias = $configArray['route_alias'] ?? null;
        }

        // Display current information
        $this->info("Module: {$moduleName}");
        if ($currentAlias) {
            $this->line("Current alias: {$currentAlias}");
        } else {
            $this->line("Current alias: Not set (using default: " . strtolower($moduleName) . ")");
        }
        $this->newLine();

        // If no alias provided, ask for it
        if (!$newAlias) {
            $suggestedAlias = $this->generateSuggestedAlias($moduleName);
            
            $this->info("Route Alias Configuration");
            $this->line("Suggested alias: {$suggestedAlias}");
            
            $choice = $this->choice(
                'Choose route alias option',
                [
                    'suggested' => "Use suggested ({$suggestedAlias})",
                    'custom' => 'Enter custom alias',
                    'remove' => 'Remove alias (use default)',
                    'cancel' => 'Cancel'
                ],
                'suggested'
            );

            if ($choice === 'cancel') {
                $this->info('Operation cancelled.');
                return 0;
            }

            if ($choice === 'remove') {
                return $this->removeAlias($moduleName, $configPath, $currentAlias);
            }

            if ($choice === 'suggested') {
                $newAlias = $suggestedAlias;
            } else {
                $newAlias = $this->askForCustomAlias($suggestedAlias);
                if (!$newAlias) {
                    $this->info('Operation cancelled.');
                    return 0;
                }
            }
        }

        // Validate the new alias
        if ($newAlias === $currentAlias) {
            $this->info("Alias is already set to '{$newAlias}'.");
            return 0;
        }

        // Check if alias is available
        if (!$this->checkRouteAliasAvailable($newAlias, $moduleName)) {
            $this->error("Route alias '{$newAlias}' is already in use by another module or route.");
            
            if (!$this->option('force')) {
                if ($this->confirm('Do you want to enter a different alias?', true)) {
                    $newAlias = $this->askForCustomAlias($newAlias);
                    if (!$newAlias) {
                        $this->info('Operation cancelled.');
                        return 0;
                    }
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        }

        // Confirm the change
        if (!$this->option('force')) {
            $this->newLine();
            $this->info("New alias will be: {$newAlias}");
            
            if (!$this->confirm('Do you want to proceed?', true)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Update the configuration
        if ($this->updateModuleConfig($moduleName, $configPath, $newAlias)) {
            $this->newLine();
            $this->info("Route alias updated successfully!");
            $this->line("Module: {$moduleName}");
            $this->line("New alias: {$newAlias}");
            
            // Update route files
            if ($this->confirm('Do you want to update route files with the new alias?', true)) {
                $this->updateRouteFiles($moduleName, $newAlias);
            }
            
            $this->newLine();
            $this->warn("Important: Clear cache for changes to take effect:");
            $this->line("  php artisan route:clear");
            $this->line("  php artisan cache:clear");
            
            if ($moduleManager->isModuleEnabled($moduleName)) {
                $this->newLine();
                $this->comment("Note: Module is currently enabled. You may need to disable and re-enable it:");
                $this->line("  php artisan module:disable {$moduleName}");
                $this->line("  php artisan module:enable {$moduleName}");
            }
            
            return 0;
        }

        $this->error("Failed to update route alias.");
        return 1;
    }

    protected function removeAlias(string $moduleName, string $configPath, ?string $currentAlias): int
    {
        if (!$currentAlias) {
            $this->info("Module doesn't have a custom alias set.");
            return 0;
        }

        if (!$this->option('force')) {
            if (!$this->confirm("Remove custom alias '{$currentAlias}' and use default?", true)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        if ($this->updateModuleConfig($moduleName, $configPath, null)) {
            $this->info("Custom alias removed. Module will use default alias: " . strtolower($moduleName));
            return 0;
        }

        $this->error("Failed to remove alias.");
        return 1;
    }

    protected function askForCustomAlias(string $default): ?string
    {
        $attempts = 0;
        while ($attempts < 3) {
            $customAlias = $this->ask("Enter custom route alias", $default);
            
            if (empty($customAlias)) {
                return null;
            }

            if ($this->checkRouteAliasAvailable($customAlias)) {
                return $customAlias;
            }

            $this->error("Route alias '{$customAlias}' is already in use. Please try a different one.");
            $attempts++;
        }

        $this->warn("Maximum attempts reached.");
        return null;
    }

    protected function generateSuggestedAlias(string $moduleName): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));
    }

    protected function checkRouteAliasAvailable(string $alias, ?string $excludeModule = null): bool
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        
        foreach ($routes as $route) {
            $prefix = $route->getPrefix();
            if ($prefix === $alias || str_starts_with($prefix, $alias . '/')) {
                // Check if this route belongs to the module we're updating
                if ($excludeModule) {
                    $action = $route->getAction();
                    $controller = $action['controller'] ?? '';
                    if (str_contains($controller, "Modules\\{$excludeModule}\\")) {
                        continue; // Skip routes from the same module
                    }
                }
                return false;
            }
        }
        
        return true;
    }

    protected function updateModuleConfig(string $moduleName, string $configPath, ?string $alias): bool
    {
        if (!File::exists($configPath)) {
            $this->error("Configuration file not found: {$configPath}");
            return false;
        }

        $configArray = include $configPath;
        
        if ($alias === null) {
            unset($configArray['route_alias']);
        } else {
            $configArray['route_alias'] = $alias;
        }

        $content = "<?php\n\nreturn " . var_export($configArray, true) . ";\n";
        
        return File::put($configPath, $content) !== false;
    }

    protected function updateRouteFiles(string $moduleName, string $newAlias): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $webRoutePath = "{$modulesPath}/{$moduleName}/routes/web.php";
        $apiRoutePath = "{$modulesPath}/{$moduleName}/routes/api.php";

        $updated = false;

        // Update web routes
        if (File::exists($webRoutePath)) {
            $content = File::get($webRoutePath);
            $oldPattern = "/Route::prefix\('([^']+)'\)/";
            $newContent = preg_replace($oldPattern, "Route::prefix('{$newAlias}')", $content, 1);
            
            if ($newContent !== $content) {
                File::put($webRoutePath, $newContent);
                $this->line("✓ Updated web routes");
                $updated = true;
            }
        }

        // Update API routes
        if (File::exists($apiRoutePath)) {
            $content = File::get($apiRoutePath);
            $oldPattern = "/Route::prefix\('api\/v1\/([^']+)'\)/";
            $newContent = preg_replace($oldPattern, "Route::prefix('api/v1/{$newAlias}')", $content, 1);
            
            if ($newContent !== $content) {
                File::put($apiRoutePath, $newContent);
                $this->line("✓ Updated API routes");
                $updated = true;
            }
        }

        if (!$updated) {
            $this->warn("No route files were updated. You may need to update them manually.");
        }
    }
}
