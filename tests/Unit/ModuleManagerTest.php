<?php

namespace Monarul007\LaravelModularSystem\Tests\Unit;

use Monarul007\LaravelModularSystem\Tests\TestCase;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Illuminate\Support\Facades\File;

class ModuleManagerTest extends TestCase
{
    protected ModuleManager $moduleManager;
    protected string $modulesPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->moduleManager = app(ModuleManager::class);
        $this->modulesPath = config('modular-system.modules_path');
        
        File::ensureDirectoryExists($this->modulesPath);
        $this->createTestModule('TestModule', '1.0.0');
    }

    protected function tearDown(): void
    {
        if (File::exists($this->modulesPath)) {
            File::deleteDirectory($this->modulesPath);
        }
        
        parent::tearDown();
    }

    protected function createTestModule(string $name, string $version = '1.0.0', array $dependencies = []): void
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

    public function test_module_exists_returns_true_for_existing_module(): void
    {
        $this->assertTrue($this->moduleManager->moduleExists('TestModule'));
    }

    public function test_module_exists_returns_false_for_non_existing_module(): void
    {
        $this->assertFalse($this->moduleManager->moduleExists('NonExistent'));
    }

    public function test_can_enable_module(): void
    {
        $result = $this->moduleManager->enableModule('TestModule');
        
        $this->assertTrue($result);
        $this->assertTrue($this->moduleManager->isModuleEnabled('TestModule'));
    }

    public function test_can_disable_module(): void
    {
        $this->moduleManager->enableModule('TestModule');
        $result = $this->moduleManager->disableModule('TestModule');
        
        $this->assertTrue($result);
        $this->assertFalse($this->moduleManager->isModuleEnabled('TestModule'));
    }

    public function test_get_module_config_returns_config(): void
    {
        $config = $this->moduleManager->getModuleConfig('TestModule');
        
        $this->assertIsArray($config);
        $this->assertEquals('TestModule', $config['name']);
        $this->assertEquals('1.0.0', $config['version']);
    }

    public function test_get_all_modules_returns_array(): void
    {
        $modules = $this->moduleManager->getAllModules();
        
        $this->assertIsArray($modules);
        $this->assertArrayHasKey('TestModule', $modules);
    }

    public function test_enabled_modules_persists(): void
    {
        $this->moduleManager->enableModule('TestModule');
        
        $newManager = new ModuleManager();
        
        $this->assertTrue($newManager->isModuleEnabled('TestModule'));
    }
}
