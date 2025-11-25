<?php

namespace Monarul007\LaravelModularSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class ModuleEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $moduleName,
        public ?array $moduleConfig = null
    ) {}
}
