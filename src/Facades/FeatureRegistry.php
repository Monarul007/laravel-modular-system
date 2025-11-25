<?php

namespace Monarul007\LaravelModularSystem\Facades;

use Illuminate\Support\Facades\Facade;

class FeatureRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Monarul007\LaravelModularSystem\Core\FeatureRegistry::class;
    }
}
