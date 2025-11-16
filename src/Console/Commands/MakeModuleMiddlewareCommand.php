<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class MakeModuleMiddlewareCommand extends Command
{
    protected $signature = 'module:make-middleware {module : The module name} {name : The middleware name}';
    protected $description = 'Create a new middleware for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $middlewareName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $middlewarePath = "{$modulesPath}/{$moduleName}/Http/Middleware/{$middlewareName}.php";

        if (File::exists($middlewarePath)) {
            $this->error("Middleware '{$middlewareName}' already exists in module '{$moduleName}'.");
            return 1;
        }

        File::ensureDirectoryExists(dirname($middlewarePath));
        File::put($middlewarePath, $this->getMiddlewareStub($moduleName, $middlewareName));

        $this->info("Middleware '{$middlewareName}' created successfully in module '{$moduleName}'.");
        return 0;
    }

    protected function getMiddlewareStub(string $moduleName, string $middlewareName): string
    {
        return "<?php

namespace Modules\\{$moduleName}\\Http\\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class {$middlewareName}
{
    public function handle(Request \$request, Closure \$next): Response
    {
        // Add your middleware logic here

        return \$next(\$request);
    }
}
";
    }
}
