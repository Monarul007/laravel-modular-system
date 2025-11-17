<?php

namespace Monarul007\LaravelModularSystem\Core;

use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class InertiaHelper
{
    /**
     * Render an Inertia response from a module
     * 
     * @param string $moduleName The module name
     * @param string $component The component path (e.g., 'Welcome' or 'Admin/Dashboard')
     * @param array $props Props to pass to the component
     * @return InertiaResponse
     */
    public static function render(string $moduleName, string $component, array $props = []): InertiaResponse
    {
        // Build the component path with module namespace
        $componentPath = ucfirst($moduleName) . '/' . $component;
        
        return Inertia::render($componentPath, $props);
    }

    /**
     * Check if the current request wants an Inertia response
     * 
     * @return bool
     */
    public static function isInertiaRequest(): bool
    {
        return request()->header('X-Inertia') === 'true';
    }

    /**
     * Share data with all Inertia responses
     * 
     * @param array|string $key
     * @param mixed $value
     * @return void
     */
    public static function share($key, $value = null): void
    {
        Inertia::share($key, $value);
    }

    /**
     * Get the root view for module Inertia pages
     * This allows modules to have their own root template if needed
     * 
     * @param string $moduleName
     * @return string
     */
    public static function getRootView(string $moduleName): string
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $moduleRootView = "{$modulesPath}/{$moduleName}/resources/views/app.blade.php";
        
        if (file_exists($moduleRootView)) {
            return strtolower($moduleName) . '::app';
        }
        
        // Fall back to main app root view
        return 'app';
    }

    /**
     * Set the root view for Inertia
     * 
     * @param string $view
     * @return void
     */
    public static function setRootView(string $view): void
    {
        Inertia::setRootView($view);
    }
}
