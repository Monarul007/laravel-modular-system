<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Response as InertiaResponse;
use Monarul007\LaravelModularSystem\Core\InertiaHelper;

abstract class ModuleInertiaController extends Controller
{
    /**
     * The module name
     * 
     * @var string
     */
    protected string $moduleName;

    /**
     * Render an Inertia response for this module
     * 
     * @param string $component
     * @param array $props
     * @return InertiaResponse
     */
    protected function inertia(string $component, array $props = []): InertiaResponse
    {
        if (!isset($this->moduleName)) {
            throw new \RuntimeException('Module name must be set in the controller');
        }

        return InertiaHelper::render($this->moduleName, $component, $props);
    }

    /**
     * Return a module view with Inertia support
     * 
     * @param string $view
     * @param array $data
     * @return \Illuminate\Contracts\View\View
     */
    protected function moduleView(string $view, array $data = [])
    {
        if (!isset($this->moduleName)) {
            throw new \RuntimeException('Module name must be set in the controller');
        }

        return module_view($this->moduleName, $view, $data);
    }
}
