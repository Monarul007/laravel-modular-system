<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class MakeModuleMigrationCommand extends Command
{
    protected $signature = 'module:make-migration {module : The module name} {name : The migration name} {--create= : The table to be created} {--table= : The table to migrate}';
    protected $description = 'Create a new migration for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $migrationName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $migrationsPath = "{$modulesPath}/{$moduleName}/Database/migrations";
        
        File::ensureDirectoryExists($migrationsPath);

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$migrationName}.php";
        $migrationPath = "{$migrationsPath}/{$fileName}";

        $className = Str::studly($migrationName);
        $tableName = $this->option('create') ?: $this->option('table');

        $stub = $this->option('create')
            ? $this->getCreateMigrationStub($className, $this->option('create'))
            : ($this->option('table')
                ? $this->getUpdateMigrationStub($className, $this->option('table'))
                : $this->getBlankMigrationStub($className));

        File::put($migrationPath, $stub);

        $this->info("Migration '{$fileName}' created successfully in module '{$moduleName}'.");
        return 0;
    }

    protected function getCreateMigrationStub(string $className, string $tableName): string
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
";
    }

    protected function getUpdateMigrationStub(string $className, string $tableName): string
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
            //
        });
    }

    public function down(): void
    {
        Schema::table('{$tableName}', function (Blueprint \$table) {
            //
        });
    }
};
";
    }

    protected function getBlankMigrationStub(string $className): string
    {
        return "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
";
    }
}
