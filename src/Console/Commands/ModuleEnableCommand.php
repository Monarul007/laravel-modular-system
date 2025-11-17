<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModuleEnableCommand extends Command
{
    protected $signature = 'module:enable {name : The module name} {--skip-confirmation : Skip confirmation prompt} {--alias= : Custom route alias}';
    protected $description = 'Enable a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        if ($moduleManager->isModuleEnabled($moduleName)) {
            $this->info("Module '{$moduleName}' is already enabled.");
            return 0;
        }

        // Ask for confirmation unless skipped
        if (!$this->option('skip-confirmation')) {
            $config = $moduleManager->getModuleConfig($moduleName);
            $description = $config['description'] ?? 'No description available';
            
            $this->info("Module: {$moduleName}");
            $this->info("Description: {$description}");
            $this->newLine();
            
            if (!$this->confirm("Do you want to enable this module?", true)) {
                $this->info('Module activation cancelled.');
                return 0;
            }
        }

        // Handle route alias
        $routeAlias = $this->handleRouteAlias($moduleName);

        // Enable the module
        if ($moduleManager->enableModule($moduleName)) {
            $this->info("Module '{$moduleName}' has been enabled.");
            
            if ($routeAlias) {
                $this->info("Route alias: {$routeAlias}");
                $this->saveRouteAlias($moduleName, $routeAlias);
            }
            
            $this->newLine();
            $this->warn("Note: You may need to clear cache for changes to take effect:");
            $this->line("  php artisan cache:clear");
            $this->line("  php artisan route:clear");
            
            return 0;
        }

        $this->error("Failed to enable module '{$moduleName}'.");
        return 1;
    }

    protected function handleRouteAlias(string $moduleName): ?string
    {
        // If alias provided via option, use it
        if ($this->option('alias')) {
            $alias = $this->option('alias');
            if ($this->checkRouteAliasAvailable($alias)) {
                return $alias;
            }
            $this->warn("Route alias '{$alias}' is already in use.");
        }

        // Suggest default alias
        $suggestedAlias = $this->generateSuggestedAlias($moduleName);
        
        $this->newLine();
        $this->info("Route Alias Configuration");
        $this->line("Suggested alias: {$suggestedAlias}");
        
        $useCustom = $this->confirm("Do you want to use a custom route alias?", false);
        
        if (!$useCustom) {
            if ($this->checkRouteAliasAvailable($suggestedAlias)) {
                return $suggestedAlias;
            }
            $this->warn("Suggested alias '{$suggestedAlias}' is already in use.");
        }

        // Ask for custom alias
        $attempts = 0;
        while ($attempts < 3) {
            $customAlias = $this->ask("Enter custom route alias (or press Enter to skip)", $suggestedAlias);
            
            if (empty($customAlias)) {
                return null;
            }

            if ($this->checkRouteAliasAvailable($customAlias)) {
                return $customAlias;
            }

            $this->error("Route alias '{$customAlias}' is already in use. Please try a different one.");
            $attempts++;
        }

        $this->warn("Maximum attempts reached. Module will be enabled without custom route alias.");
        return null;
    }

    protected function generateSuggestedAlias(string $moduleName): string
    {
        // Convert PascalCase to kebab-case
        // TestBlog -> test-blog
        // LoginWithPhone -> login-with-phone
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));
    }

    protected function checkRouteAliasAvailable(string $alias): bool
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        
        foreach ($routes as $route) {
            $prefix = $route->getPrefix();
            if ($prefix === $alias || str_starts_with($prefix, $alias . '/')) {
                return false;
            }
        }
        
        return true;
    }

    protected function saveRouteAlias(string $moduleName, string $alias): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $configPath = "{$modulesPath}/{$moduleName}/config/{$moduleName}.php";
        
        if (\Illuminate\Support\Facades\File::exists($configPath)) {
            $config = include $configPath;
            $config['route_alias'] = $alias;
            
            $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
            \Illuminate\Support\Facades\File::put($configPath, $content);
        }
    }
}
