<?php

namespace Modules\TestModule\Providers;

use Illuminate\Support\ServiceProvider;

class TestModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Module is automatically loaded by the modular system
        // This provider is optional but can be used for additional setup
    }

    public function register(): void
    {
        //
    }
}
