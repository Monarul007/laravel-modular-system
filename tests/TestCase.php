<?php

namespace Monarul007\LaravelModularSystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Monarul007\LaravelModularSystem\ModularSystemServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ModularSystemServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('modular-system.modules_path', __DIR__ . '/fixtures/modules');
        $app['config']->set('modular-system.cache_enabled', false);
    }
}
