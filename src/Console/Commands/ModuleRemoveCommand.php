<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModuleRemoveCommand extends Command
{
    protected $signature = 'module:remove {name : The module name} {--force : Force removal without confirmation}';
    protected $description = 'Remove/uninstall a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        // Ask for confirmation unless --force is used
        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to remove module '{$moduleName}'? This will delete all module files.", false)) {
                $this->info('Module removal cancelled.');
                return 0;
            }
        }

        $this->info("Removing module '{$moduleName}'...");

        // Check if published assets exist
        $publicPath = public_path("modules/" . strtolower($moduleName));
        $hasPublishedAssets = \Illuminate\Support\Facades\File::exists($publicPath);

        // Ask about asset deletion
        $deleteAssets = false;
        if ($hasPublishedAssets) {
            if ($this->option('force')) {
                $deleteAssets = true;
                $this->info("Published assets will also be removed.");
            } else {
                $deleteAssets = $this->confirm("Do you want to delete published assets for this module?", true);
            }
        }

        // Uninstall module
        if ($moduleManager->uninstallModule($moduleName)) {
            $this->info("Module '{$moduleName}' removed successfully.");

            // Delete published assets if requested
            if ($deleteAssets && $hasPublishedAssets) {
                if (\Illuminate\Support\Facades\File::deleteDirectory($publicPath)) {
                    $this->info("Published assets removed successfully.");
                } else {
                    $this->warn("Failed to remove published assets.");
                }
            }

            return 0;
        }

        $this->error("Failed to remove module '{$moduleName}'.");
        return 1;
    }
}
