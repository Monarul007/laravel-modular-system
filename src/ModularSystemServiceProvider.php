<?php

namespace Monarul007\LaravelModularSystem;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Monarul007\LaravelModularSystem\Core\SettingsManager;

class ModularSystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__.'/../config/modular-system.php', 'modular-system');

        // Register singletons
        $this->app->singleton(ModuleManager::class);
        $this->app->singleton(SettingsManager::class);
        $this->app->singleton('module-view-helper', function () {
            return new Core\ModuleViewHelper();
        });
<<<<<<< HEAD
        $this->app->singleton('module-inertia-helper', function () {
            return new Core\InertiaHelper();
        });
=======
>>>>>>> 7135bd4baa80123da48c1b13925a5a46c2b7c084

        // Register aliases
        $this->app->alias(ModuleManager::class, 'module-manager');
        $this->app->alias(SettingsManager::class, 'settings-manager');
<<<<<<< HEAD
        
        // Load helper functions
        if (file_exists(__DIR__.'/Support/helpers.php')) {
            require_once __DIR__.'/Support/helpers.php';
        }
=======
>>>>>>> 7135bd4baa80123da48c1b13925a5a46c2b7c084
    }

    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/modular-system.php' => config_path('modular-system.php'),
        ], 'modular-config');

        // Publish migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'modular-migrations');

        // Publish routes
        $this->publishes([
            __DIR__.'/../routes/api.php' => base_path('routes/modular-api.php'),
            __DIR__.'/../routes/web.php' => base_path('routes/modular-web.php'),
        ], 'modular-routes');

<<<<<<< HEAD
        // Publish Vue components
        $this->publishes([
            __DIR__.'/../resources/js/Pages' => resource_path('js/Pages'),
        ], 'modular-views');

=======
>>>>>>> 7135bd4baa80123da48c1b13925a5a46c2b7c084
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\MakeModuleCommand::class,
                Console\Commands\ModuleEnableCommand::class,
                Console\Commands\ModuleDisableCommand::class,
                Console\Commands\ModuleListCommand::class,
                Console\Commands\ModuleRemoveCommand::class,
                Console\Commands\TestModuleUploadCommand::class,
                Console\Commands\MakeModuleControllerCommand::class,
                Console\Commands\MakeModuleMiddlewareCommand::class,
                Console\Commands\MakeModuleMigrationCommand::class,
                Console\Commands\MakeModuleModelCommand::class,
                Console\Commands\MakeModuleCommandCommand::class,
                Console\Commands\ModulePublishCommand::class,
                Console\Commands\ModuleSetAliasCommand::class,
<<<<<<< HEAD
                Console\Commands\ModulePublishAssetsCommand::class,
=======
>>>>>>> 7135bd4baa80123da48c1b13925a5a46c2b7c084
            ]);
        }

        // Ensure modules directory exists
        $this->ensureModulesDirectory();

        // Boot modules
        $moduleManager = $this->app->make(ModuleManager::class);
        $moduleManager->bootModules();

        // Load module routes
        $this->loadModuleRoutes($moduleManager);

        // Load module views
        $this->loadModuleViews($moduleManager);

        // Load package routes
        // Always load from package to ensure routes are available
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    protected function ensureModulesDirectory(): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        
        if (!File::exists($modulesPath)) {
            File::makeDirectory($modulesPath, 0755, true);
        }
    }

    protected function loadModuleRoutes(ModuleManager $moduleManager): void
    {
        foreach ($moduleManager->getEnabledModules() as $moduleName) {
            $this->loadModuleApiRoutes($moduleName);
            $this->loadModuleWebRoutes($moduleName);
        }
    }

    protected function loadModuleApiRoutes(string $moduleName): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $routePath = "{$modulesPath}/{$moduleName}/routes/api.php";
        
        if (File::exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }
    }

    protected function loadModuleWebRoutes(string $moduleName): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $routePath = "{$modulesPath}/{$moduleName}/routes/web.php";
        
        if (File::exists($routePath)) {
            $this->loadRoutesFrom($routePath);
        }
    }

    protected function loadModuleViews(ModuleManager $moduleManager): void
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        
        foreach ($moduleManager->getEnabledModules() as $moduleName) {
            $viewsPath = "{$modulesPath}/{$moduleName}/resources/views";
            
            if (File::exists($viewsPath)) {
                $this->loadViewsFrom($viewsPath, strtolower($moduleName));
            }
        }
    }
}
