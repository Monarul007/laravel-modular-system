<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The module name} {--skip-confirmation : Skip confirmation prompt} {--alias= : Custom route alias}';
    protected $description = 'Create a new module';

    public function handle()
    {
        $moduleName = $this->argument('name');
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $modulePath = "{$modulesPath}/{$moduleName}";

        if (File::exists($modulePath)) {
            $this->error("Module '{$moduleName}' already exists.");
            return 1;
        }

        // Ask for confirmation unless skipped
        if (!$this->option('skip-confirmation')) {
            $this->info("Creating new module: {$moduleName}");
            $this->newLine();
            
            if (!$this->confirm("Do you want to create this module?", true)) {
                $this->info('Module creation cancelled.');
                return 0;
            }
        }

        // Get route alias
        $routeAlias = $this->getRouteAlias($moduleName);

        $this->createModuleStructure($moduleName, $modulePath, $routeAlias);
        
        $this->newLine();
        $this->info("Module '{$moduleName}' created successfully!");
        $this->line("Location: {$modulePath}");
        
        if ($routeAlias) {
            $this->line("Route alias: {$routeAlias}");
        }
        
        $this->newLine();
        $this->comment("Next steps:");
        $this->line("  1. php artisan module:enable {$moduleName}");
        $this->line("  2. Add your routes in modules/{$moduleName}/routes/");
        $this->line("  3. Create controllers, models, etc.");

        return 0;
    }

    protected function getRouteAlias(string $moduleName): ?string
    {
        // If alias provided via option, use it
        if ($this->option('alias')) {
            $alias = $this->option('alias');
            if ($this->checkRouteAliasAvailable($alias)) {
                return $alias;
            }
            $this->warn("Route alias '{$alias}' is already in use.");
        }

        // Generate suggested alias
        $suggestedAlias = $this->generateSuggestedAlias($moduleName);
        
        $this->newLine();
        $this->info("Route Alias Configuration");
        $this->line("Module: {$moduleName}");
        $this->line("Suggested alias: {$suggestedAlias}");
        
        $choice = $this->choice(
            'Choose route alias option',
            [
                'use_suggested' => "Use suggested ({$suggestedAlias})",
                'custom' => 'Enter custom alias',
                'skip' => 'Skip (use default)'
            ],
            'use_suggested'
        );

        if ($choice === 'skip') {
            return null;
        }

        if ($choice === 'use_suggested') {
            if ($this->checkRouteAliasAvailable($suggestedAlias)) {
                return $suggestedAlias;
            }
            $this->warn("Suggested alias '{$suggestedAlias}' is already in use.");
            $choice = 'custom';
        }

        if ($choice === 'custom') {
            $attempts = 0;
            while ($attempts < 3) {
                $customAlias = $this->ask("Enter custom route alias", $suggestedAlias);
                
                if (empty($customAlias)) {
                    return null;
                }

                if ($this->checkRouteAliasAvailable($customAlias)) {
                    return $customAlias;
                }

                $this->error("Route alias '{$customAlias}' is already in use. Please try a different one.");
                $attempts++;
            }

            $this->warn("Maximum attempts reached. Module will be created without custom route alias.");
        }

        return null;
    }

    protected function generateSuggestedAlias(string $moduleName): string
    {
        // Convert PascalCase to kebab-case
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

    protected function createModuleStructure(string $moduleName, string $modulePath, ?string $routeAlias = null): void
    {
        $directories = [
            'Providers',
            'Http/Controllers',
            'Http/Middleware',
            'Database/migrations',
            'routes',
            'config',
            'resources/views',
            'resources/js'
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }

        $moduleConfig = [
            'name' => $moduleName,
            'description' => "The {$moduleName} module",
            'version' => '1.0.0',
            'namespace' => "Modules\\{$moduleName}",
            'providers' => ["Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider"],
            'dependencies' => []
        ];

        File::put(
            "{$modulePath}/module.json",
            json_encode($moduleConfig, JSON_PRETTY_PRINT)
        );

        File::put(
            "{$modulePath}/Providers/{$moduleName}ServiceProvider.php",
            $this->getServiceProviderStub($moduleName)
        );

        $alias = $routeAlias ?: strtolower($moduleName);
        File::put("{$modulePath}/routes/api.php", $this->getApiRoutesStub($moduleName, $alias));
        File::put("{$modulePath}/routes/web.php", $this->getWebRoutesStub($moduleName, $alias));
        File::put("{$modulePath}/config/{$moduleName}.php", $this->getConfigStub($moduleName, $routeAlias));
        File::put("{$modulePath}/README.md", $this->getReadmeStub($moduleName));
    }

    protected function getServiceProviderStub(string $moduleName): string
    {
        return "<?php

namespace Modules\\{$moduleName}\\Providers;

use Illuminate\Support\ServiceProvider;

class {$moduleName}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        \$this->mergeConfigFrom(
            __DIR__ . '/../config/{$moduleName}.php',
            strtolower('{$moduleName}')
        );
    }

    public function boot(): void
    {
        \$this->loadViewsFrom(__DIR__ . '/../resources/views', '{$moduleName}');
        
        if (\$this->app->runningInConsole()) {
            \$this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        }
    }
}";
    }

    protected function getApiRoutesStub(string $moduleName, string $alias): string
    {
        return "<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/{$alias}')->group(function () {
    // Add your API routes here
    // Example:
    // Route::get('/', [YourController::class, 'index']);
});";
    }

    protected function getWebRoutesStub(string $moduleName, string $alias): string
    {
        return "<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{$alias}')->group(function () {
    // Add your web routes here
    // Example:
    // Route::get('/', [YourController::class, 'index']);
});";
    }

    protected function getConfigStub(string $moduleName, ?string $routeAlias = null): string
    {
        $aliasLine = $routeAlias ? "\n    'route_alias' => '{$routeAlias}'," : '';
        
        return "<?php

return [
    'name' => '{$moduleName}',
    'enabled' => true,{$aliasLine}
    
    // Add your module configuration here
];";
    }

    protected function getReadmeStub(string $moduleName): string
    {
        return "# {$moduleName} Module

## Description

The {$moduleName} module.

## Installation

This module is installed via the Laravel Modular System.

## Configuration

Edit `config/{$moduleName}.php` to configure the module.

## Usage

Add your usage instructions here.

## Version

1.0.0
";
    }
}
