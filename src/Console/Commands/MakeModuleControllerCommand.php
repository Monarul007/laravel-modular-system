<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class MakeModuleControllerCommand extends Command
{
    protected $signature = 'module:make-controller {module : The module name} {name : The controller name} {--api : Create an API controller} {--resource : Create a resource controller}';
    protected $description = 'Create a new controller for a module';

    public function handle(ModuleManager $moduleManager)
    {
        $moduleName = $this->argument('module');
        $controllerName = $this->argument('name');

        if (!$moduleManager->moduleExists($moduleName)) {
            $this->error("Module '{$moduleName}' does not exist.");
            return 1;
        }

        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $controllerPath = "{$modulesPath}/{$moduleName}/Http/Controllers/{$controllerName}.php";

        if (File::exists($controllerPath)) {
            $this->error("Controller '{$controllerName}' already exists in module '{$moduleName}'.");
            return 1;
        }

        File::ensureDirectoryExists(dirname($controllerPath));

        $stub = $this->option('resource') 
            ? $this->getResourceControllerStub($moduleName, $controllerName)
            : ($this->option('api') 
                ? $this->getApiControllerStub($moduleName, $controllerName)
                : $this->getControllerStub($moduleName, $controllerName));

        File::put($controllerPath, $stub);

        $this->info("Controller '{$controllerName}' created successfully in module '{$moduleName}'.");
        return 0;
    }

    protected function getControllerStub(string $moduleName, string $controllerName): string
    {
        $namespace = $this->getNamespace($moduleName, $controllerName);
        $className = $this->getClassName($controllerName);
        
        return "<?php

namespace {$namespace};

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class {$className} extends Controller
{
    public function index()
    {
        //
    }
}
";
    }

    protected function getApiControllerStub(string $moduleName, string $controllerName): string
    {
        $namespace = $this->getNamespace($moduleName, $controllerName);
        $className = $this->getClassName($controllerName);
        
        return "<?php

namespace {$namespace};

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class {$className} extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }
}
";
    }

    protected function getResourceControllerStub(string $moduleName, string $controllerName): string
    {
        $namespace = $this->getNamespace($moduleName, $controllerName);
        $className = $this->getClassName($controllerName);
        
        return "<?php

namespace {$namespace};

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class {$className} extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request \$request)
    {
        //
    }

    public function show(\$id)
    {
        //
    }

    public function edit(\$id)
    {
        //
    }

    public function update(Request \$request, \$id)
    {
        //
    }

    public function destroy(\$id)
    {
        //
    }
}
";
    }
    
    protected function getNamespace(string $moduleName, string $controllerName): string
    {
        $namespace = "Modules\\{$moduleName}\\Http\\Controllers";
        
        if (str_contains($controllerName, '/')) {
            $parts = explode('/', $controllerName);
            array_pop($parts); // Remove the class name
            $namespace .= '\\' . implode('\\', $parts);
        }
        
        return $namespace;
    }
    
    protected function getClassName(string $controllerName): string
    {
        if (str_contains($controllerName, '/')) {
            $parts = explode('/', $controllerName);
            return end($parts);
        }
        
        return $controllerName;
    }
}
