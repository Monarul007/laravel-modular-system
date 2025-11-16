<?php

namespace Monarul007\LaravelModularSystem\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value, string $group = 'general')
 * @method static array getGroup(string $group)
 * @method static void forget(string $key)
 *
 * @see \Monarul007\LaravelModularSystem\Core\SettingsManager
 */
class Settings extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Monarul007\LaravelModularSystem\Core\SettingsManager::class;
    }
}
