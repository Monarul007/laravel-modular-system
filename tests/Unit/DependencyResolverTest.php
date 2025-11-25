<?php

namespace Monarul007\LaravelModularSystem\Tests\Unit;

use Monarul007\LaravelModularSystem\Tests\TestCase;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Illuminate\Support\Facades\File;

class DependencyResolverTest extends TestCase
{
    protected ModuleManager $moduleManager;
    protected string $modulesPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->moduleManager = app(ModuleManager::class);
        $this->modulesPath = config('modular-system.modules_path');
        
        File::ensureDirectoryExists($this->modulesPath);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->modulesPath)) {
            File::deleteDirectory($this->modulesPath);
        }
        
        parent::tearDown();
    }

    protected function createModule(string $name, string $version = '1.0.0', array $dependencies = []): void
    {
        $modulePath = "{$this->modulesPath}/{$name}";
        File::ensureDirectoryExists($modulePath);
        
        $config = [
            'name' => $name,
            'version' => $version,
            'namespace' => "Modules\\{$name}",
            'providers' => [],
            'dependencies' => $dependencies,
        ];
        
        File::put("{$modulePath}/module.json", json_encode($config, JSON_PRETTY_PRINT));
    }

    public function test_detects_missing_dependencies(): void
    {
        $this->createModule('ModuleA', '1.0.0');
        $this->createModule('ModuleB', '1.0.0', ['ModuleA']);
        
        $missing = $this->moduleManager->checkDependencies('ModuleB');
        
        $this->assertContains('ModuleA', $missing);
    }

    public function test_no_missing_dependencies_when_enabled(): void
    {
        $this->createModule('ModuleA', '1.0.0');
        $this->createModule('ModuleB', '1.0.0', ['ModuleA']);
        
        $this->moduleManager->enableModule('ModuleA');
        $missing = $this->moduleManager->checkDependencies('ModuleB');
        
        $this->assertEmpty($missing);
    }

    public function test_detects_circular_dependencies(): void
    {
        $this->createModule('ModuleA', '1.0.0', ['ModuleB']);
        $this->createModule('ModuleB', '1.0.0', ['ModuleA']);
        
        $circular = $this->moduleManager->detectCircularDependencies('ModuleA');
        
        $this->assertNotNull($circular);
        $this->assertContains('ModuleA', $circular);
        $this->assertContains('ModuleB', $circular);
    }

    public function test_version_constraint_caret(): void
    {
        $this->createModule('ModuleA', '1.5.0');
        $this->createModule('ModuleB', '1.0.0', ['ModuleA' => '^1.0']);
        
        $this->moduleManager->enableModule('ModuleA');
        $missing = $this->moduleManager->checkDependencies('ModuleB');
        
        $this->assertEmpty($missing);
    }

    public function test_version_constraint_fails_major_version(): void
    {
        $this->createModule('ModuleA', '2.0.0');
        $this->createModule('ModuleB', '1.0.0', ['ModuleA' => '^1.0']);
        
        $this->moduleManager->enableModule('ModuleA');
        $missing = $this->moduleManager->checkDependencies('ModuleB');
        
        $this->assertNotEmpty($missing);
    }

    public function test_finds_dependent_modules(): void
    {
        $this->createModule('ModuleA', '1.0.0');
        $this->createModule('ModuleB', '1.0.0', ['ModuleA']);
        
        $this->moduleManager->enableModule('ModuleA');
        $this->moduleManager->enableModule('ModuleB');
        
        $dependents = $this->moduleManager->getDependentModules('ModuleA');
        
        $this->assertContains('ModuleB', $dependents);
    }
}
