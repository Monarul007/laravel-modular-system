<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModuleEnableCommand extends Command
{
    protected $signature = 'module:enable {name : The module name}';
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

        if ($moduleManager->enableModule($moduleName)) {
            $this->info("Module '{$moduleName}' has been enabled.");
            $this->warn("Note: You may need to clear cache or restart the application for changes to take effect.");
            return 0;
        }

        $this->error("Failed to enable module '{$moduleName}'.");
        return 1;
    }
}
