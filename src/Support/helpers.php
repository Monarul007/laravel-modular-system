<?php

use Monarul007\LaravelModularSystem\Core\InertiaHelper;
use Inertia\Response as InertiaResponse;

if (!function_exists('module_inertia')) {
    /**
     * Render an Inertia response from a module
     * 
     * @param string $moduleName
     * @param string $component
     * @param array $props
     * @return InertiaResponse
     */
    function module_inertia(string $moduleName, string $component, array $props = []): InertiaResponse
    {
        return InertiaHelper::render($moduleName, $component, $props);
    }
}

if (!function_exists('module_db_prefix')) {
    /**
     * Get the database table prefix for modules
     */
    function module_db_prefix(string $table = ''): string
    {
        $prefix = config('modular-system.database_prefix', 'module_');
        return $prefix . $table;
    }
}

if (!function_exists('module_view')) {
    /**
     * Get a module view with proper Inertia support
     * 
     * @param string $moduleName
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\View\View
     */
    function module_view(string $moduleName, string $view, array $data = [])
    {
        $viewName = strtolower($moduleName) . '::' . $view;
        
        // If the view uses Inertia directives, ensure $page is available
        if (!isset($data['page'])) {
            try {
                // Try to get Inertia shared data
                if (class_exists(\Inertia\Inertia::class)) {
                    $data['page'] = \Inertia\Inertia::getShared();
                }
            } catch (\Exception $e) {
                // If Inertia is not available, provide empty page data
                $data['page'] = ['component' => '', 'props' => [], 'url' => request()->url(), 'version' => null];
            }
        }
        
        return view($viewName, $data);
    }
}
