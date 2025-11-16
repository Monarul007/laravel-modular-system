<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class MakeModuleCommandCommand extends Command
{
    protected $signature = 'module:make-command {module : The module name} {name : The command name}';
    protected $description = 'Create a new artisan command for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $commandName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $commandsPath = "{$modulesPath}/{$moduleName}/Console/Commands";
        $commandPath = "{$commandsPath}/{$commandName}.php";

        if (File::exists($commandPath)) {
            $this->error("Command '{$commandName}' already exists in module '{$moduleName}'.");
            return 1;
        }

        File::ensureDirectoryExists($commandsPath);
        File::put($commandPath, $this->getCommandStub($moduleName, $commandName));

        $this->info("Command '{$commandName}' created successfully in module '{$moduleName}'.");
        $this->warn("Don't forget to register this command in your module's service provider.");
        
        return 0;
    }

    protected function getCommandStub(string $moduleName, string $commandName): string
    {
        $signature = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', str_replace('Command', '', $commandName)));
        
        return "<?php

namespace Modules\\{$moduleName}\\Console\\Commands;

use Illuminate\Console\Command;

class {$commandName} extends Command
{
    protected \$signature = '{$moduleName}:{$signature}';
    protected \$description = 'Command description';

    public function handle()
    {
        \$this->info('Command executed successfully!');
        return 0;
    }
}
";
    }
}
