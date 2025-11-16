<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Monarul007\LaravelModularSystem\Core\ModuleViewHelper;

class ModulePublishCommand extends Command
{
    protected $signature = 'module:publish {module? : The module name} {--all : Publish all enabled modules}';
    protected $description = 'Publish module assets to public directory';

    public function handle(ModuleManager $moduleManager)
    {
        if ($this->option('all')) {
            return $this->publishAllModules($moduleManager);
        }

        $moduleName = $this->argument('module');

        if (!$moduleName) {
            $this->error('Please specify a module name or use --all flag.');
            return 1;
        }

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        return $this->publishModule($moduleName);
    }

    protected function publishModule(string $moduleName): int
    {
        $this->info("Publishing assets for module '{$moduleName}'...");

        if (ModuleViewHelper::publishAssets($moduleName)) {
            $this->info("Assets published successfully for module '{$moduleName}'.");
            return 0;
        }

        $this->warn("No assets found or failed to publish for module '{$moduleName}'.");
        return 0;
    }

    protected function publishAllModules(ModuleManager $moduleManager): int
    {
        $enabledModules = $moduleManager->getEnabledModules();

        if (empty($enabledModules)) {
            $this->warn('No enabled modules found.');
            return 0;
        }

        foreach ($enabledModules as $moduleName) {
            $this->publishModule($moduleName);
        }

        $this->info('All module assets published successfully.');
        return 0;
    }
}
