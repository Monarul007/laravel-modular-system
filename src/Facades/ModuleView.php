<?php

namespace Monarul007\LaravelModularSystem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string view(string $moduleName, string $view)
 * @method static string asset(string $moduleName, string $asset)
 * @method static bool viewExists(string $moduleName, string $view)
 * @method static array getModuleViews(string $moduleName)
 * @method static string publicPath(string $moduleName)
 * @method static bool publishAssets(string $moduleName)
 *
 * @see \Monarul007\LaravelModularSystem\Core\ModuleViewHelper
 */
class ModuleView extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'module-view-helper';
    }
}
