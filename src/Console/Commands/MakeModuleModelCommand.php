<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class MakeModuleModelCommand extends Command
{
    protected $signature = 'module:make-model {module : The module name} {name : The model name} {--migration : Create a migration file}';
    protected $description = 'Create a new model for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $modelName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $modelsPath = "{$modulesPath}/{$moduleName}/Models";
        $modelPath = "{$modelsPath}/{$modelName}.php";

        if (File::exists($modelPath)) {
            $this->error("Model '{$modelName}' already exists in module '{$moduleName}'.");
            return 1;
        }

        File::ensureDirectoryExists($modelsPath);
        File::put($modelPath, $this->getModelStub($moduleName, $modelName));

        $this->info("Model '{$modelName}' created successfully in module '{$moduleName}'.");

        if ($this->option('migration')) {
            $tableName = strtolower(str_replace('\\', '_', preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName))) . 's';
            $this->call('module:make-migration', [
                'module' => $moduleName,
                'name' => "create_{$tableName}_table",
                '--create' => $tableName
            ]);
        }

        return 0;
    }

    protected function getModelStub(string $moduleName, string $modelName): string
    {
        return "<?php

namespace Modules\\{$moduleName}\\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class {$modelName} extends Model
{
    use HasFactory;

    protected \$fillable = [];

    protected \$casts = [];
}
";
    }
}
