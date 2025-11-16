<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';
    protected $description = 'List all available modules';

    public function handle(ModuleManager $moduleManager)
    {
        $modules = $moduleManager->getAllModules();

        if (empty($modules)) {
            $this->info('No modules found.');
            return 0;
        }

        $this->table(
            ['Name', 'Version', 'Status', 'Description'],
            collect($modules)->map(function ($config, $name) {
                return [
                    $name,
                    $config['version'] ?? 'N/A',
                    $config['enabled'] ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>',
                    $config['description'] ?? 'N/A'
                ];
            })->toArray()
        );

        return 0;
    }
}
