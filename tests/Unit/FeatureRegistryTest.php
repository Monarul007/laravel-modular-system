<?php

namespace Monarul007\LaravelModularSystem\Tests\Unit;

use Monarul007\LaravelModularSystem\Tests\TestCase;
use Monarul007\LaravelModularSystem\Core\FeatureRegistry;

class FeatureRegistryTest extends TestCase
{
    protected FeatureRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = app(FeatureRegistry::class);
    }

    public function test_can_register_feature(): void
    {
        $this->registry->register('test.feature', [
            'enabled' => true,
            'module' => 'TestModule',
        ]);
        
        $feature = $this->registry->get('test.feature');
        
        $this->assertNotNull($feature);
        $this->assertEquals('TestModule', $feature['module']);
    }

    public function test_feature_is_enabled_by_default(): void
    {
        $this->registry->register('test.feature', []);
        
        $this->assertTrue($this->registry->isEnabled('test.feature'));
    }

    public function test_can_disable_feature(): void
    {
        $this->registry->register('test.feature', []);
        $this->registry->disable('test.feature');
        
        $this->assertFalse($this->registry->isEnabled('test.feature'));
    }

    public function test_can_enable_feature(): void
    {
        $this->registry->register('test.feature', ['enabled' => false]);
        $this->registry->enable('test.feature');
        
        $this->assertTrue($this->registry->isEnabled('test.feature'));
    }

    public function test_feature_with_dependencies_checks_them(): void
    {
        $this->registry->register('feature.a', ['enabled' => true]);
        $this->registry->register('feature.b', [
            'enabled' => true,
            'dependencies' => ['feature.a'],
        ]);
        
        $this->assertTrue($this->registry->isEnabled('feature.b'));
        
        $this->registry->disable('feature.a');
        
        $this->assertFalse($this->registry->isEnabled('feature.b'));
    }

    public function test_can_get_features_by_module(): void
    {
        $this->registry->register('module1.feature1', ['module' => 'Module1']);
        $this->registry->register('module1.feature2', ['module' => 'Module1']);
        $this->registry->register('module2.feature1', ['module' => 'Module2']);
        
        $module1Features = $this->registry->getByModule('Module1');
        
        $this->assertCount(2, $module1Features);
    }

    public function test_can_unregister_feature(): void
    {
        $this->registry->register('test.feature', []);
        $this->registry->unregister('test.feature');
        
        $this->assertNull($this->registry->get('test.feature'));
    }
}
