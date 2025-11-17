<?php

namespace Modules\TestModule\Http\Controllers;

use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class WelcomeController extends ModuleInertiaController
{
    protected string $moduleName = 'TestModule';

    /**
     * Show Inertia page
     */
    public function index()
    {
        return $this->inertia('Welcome', [
            'message' => 'Hello from TestModule!',
            'items' => ['Item 1', 'Item 2', 'Item 3']
        ]);
    }

    /**
     * Show Blade view with Inertia support
     */
    public function blade()
    {
        return $this->moduleView('welcome', [
            'title' => 'Blade View with Inertia',
            'content' => 'This is a Blade view that uses Inertia directives.'
        ]);
    }
}
