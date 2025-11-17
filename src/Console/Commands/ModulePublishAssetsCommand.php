<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModulePublishAssetsCommand extends Command
{
    protected $signature = 'module:publish-assets {module? : The module name}';
    protected $description = 'Publish module Vue components to resources directory';

    public function handle(ModuleManager $moduleManager): int
    {
        $moduleName = $this->argument('module');

        if ($moduleName) {
            return $this->publishModuleAssets($moduleName);
        }

        // Publish all enabled modules
        $enabledModules = $moduleManager->getEnabledModules();
        
        if (empty($enabledModules)) {
            $this->info('No enabled modules found.');
            return 0;
        }

        foreach ($enabledModules as $module) {
            $this->publishModuleAssets($module);
        }

        $this->info('All module assets published successfully.');
        $this->warn('Run "npm run build" to compile the assets.');

        return 0;
    }

    protected function publishModuleAssets(string $moduleName): int
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $sourceDir = "{$modulesPath}/{$moduleName}/resources/js/Pages";
        $targetDir = resource_path("js/Pages/{$moduleName}");

        if (!File::exists($sourceDir)) {
            $this->warn("No Vue components found for module '{$moduleName}'.");
            return 0;
        }

        // Create target directory
        File::ensureDirectoryExists($targetDir);

        // Copy all Vue files
        File::copyDirectory($sourceDir, $targetDir);

        $this->info("✓ Published assets for module '{$moduleName}'");

        return 0;
    }
}
