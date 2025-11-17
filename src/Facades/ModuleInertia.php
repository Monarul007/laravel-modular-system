<?php

namespace Monarul007\LaravelModularSystem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Inertia\Response render(string $moduleName, string $component, array $props = [])
 * @method static bool isInertiaRequest()
 * @method static void share(array|string $key, mixed $value = null)
 * @method static string getRootView(string $moduleName)
 * @method static void setRootView(string $view)
 *
 * @see \Monarul007\LaravelModularSystem\Core\InertiaHelper
 */
class ModuleInertia extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'module-inertia-helper';
    }
}
