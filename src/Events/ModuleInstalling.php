<?php

namespace Monarul007\LaravelModularSystem\Events;

class ModuleInstalling extends ModuleEvent
{
    public function __construct(
        public string $moduleName,
        public string $zipPath,
        public ?array $moduleConfig = null
    ) {
        parent::__construct($moduleName, $moduleConfig);
    }
}
