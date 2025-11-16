<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The module name}';
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

        $this->createModuleStructure($moduleName, $modulePath);
        $this->info("Module '{$moduleName}' created successfully at: {$modulePath}");

        return 0;
    }

    protected function createModuleStructure(string $moduleName, string $modulePath): void
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

        File::put("{$modulePath}/routes/api.php", $this->getApiRoutesStub($moduleName));
        File::put("{$modulePath}/routes/web.php", $this->getWebRoutesStub($moduleName));
        File::put("{$modulePath}/config/{$moduleName}.php", $this->getConfigStub($moduleName));
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
            '{$moduleName}'
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

    protected function getApiRoutesStub(string $moduleName): string
    {
        $lowerName = strtolower($moduleName);
        return "<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/{$lowerName}')->group(function () {
    // Add your API routes here
    // Example:
    // Route::get('/', [YourController::class, 'index']);
});";
    }

    protected function getWebRoutesStub(string $moduleName): string
    {
        $lowerName = strtolower($moduleName);
        return "<?php

use Illuminate\Support\Facades\Route;

Route::prefix('{$lowerName}')->group(function () {
    // Add your web routes here
    // Example:
    // Route::get('/', [YourController::class, 'index']);
});";
    }

    protected function getConfigStub(string $moduleName): string
    {
        return "<?php

return [
    'name' => '{$moduleName}',
    'enabled' => true,
    
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
