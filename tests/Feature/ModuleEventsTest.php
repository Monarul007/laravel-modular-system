<?php

namespace Monarul007\LaravelModularSystem\Tests\Feature;

use Monarul007\LaravelModularSystem\Tests\TestCase;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Monarul007\LaravelModularSystem\Events\ModuleEnabled;
use Monarul007\LaravelModularSystem\Events\ModuleEnabling;
use Monarul007\LaravelModularSystem\Events\ModuleDisabled;
use Monarul007\LaravelModularSystem\Events\ModuleDisabling;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;

class ModuleEventsTest extends TestCase
{
    protected ModuleManager $moduleManager;
    protected string $modulesPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        Event::fake();
        
        $this->moduleManager = app(ModuleManager::class);
        $this->modulesPath = config('modular-system.modules_path');
        
        File::ensureDirectoryExists($this->modulesPath);
        $this->createTestModule('TestModule');
    }

    protected function tearDown(): void
    {
        if (File::exists($this->modulesPath)) {
            File::deleteDirectory($this->modulesPath);
        }
        
        parent::tearDown();
    }

    protected function createTestModule(string $name): void
    {
        $modulePath = "{$this->modulesPath}/{$name}";
        File::ensureDirectoryExists($modulePath);
        
        $config = [
            'name' => $name,
            'version' => '1.0.0',
            'namespace' => "Modules\\{$name}",
            'providers' => [],
        ];
        
        File::put("{$modulePath}/module.json", json_encode($config));
    }

    public function test_dispatches_enabling_and_enabled_events(): void
    {
        $this->moduleManager->enableModule('TestModule');
        
        Event::assertDispatched(ModuleEnabling::class, function ($event) {
            return $event->moduleName === 'TestModule';
        });
        
        Event::assertDispatched(ModuleEnabled::class, function ($event) {
            return $event->moduleName === 'TestModule';
        });
    }

    public function test_dispatches_disabling_and_disabled_events(): void
    {
        $this->moduleManager->enableModule('TestModule');
        $this->moduleManager->disableModule('TestModule');
        
        Event::assertDispatched(ModuleDisabling::class, function ($event) {
            return $event->moduleName === 'TestModule';
        });
        
        Event::assertDispatched(ModuleDisabled::class, function ($event) {
            return $event->moduleName === 'TestModule';
        });
    }
}
