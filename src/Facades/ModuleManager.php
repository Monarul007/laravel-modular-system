<?php

namespace Monarul007\LaravelModularSystem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array getEnabledModules()
 * @method static bool isModuleEnabled(string $moduleName)
 * @method static bool enableModule(string $moduleName)
 * @method static bool disableModule(string $moduleName)
 * @method static array|null getModuleConfig(string $moduleName)
 * @method static array getAllModules()
 * @method static bool moduleExists(string $moduleName)
 * @method static array installModuleFromZip(string $zipPath, string $moduleName = null)
 * @method static bool uninstallModule(string $moduleName)
 * @method static string|null createModuleZip(string $moduleName)
 * @method static void bootModules()
 *
 * @see \Monarul007\LaravelModularSystem\Core\ModuleManager
 */
class ModuleManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Monarul007\LaravelModularSystem\Core\ModuleManager::class;
    }
}
