<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Support\TemplateEngineDetector;

class DetectTemplateEngineCommand extends Command
{
    protected $signature = 'modular:detect-engine';
    
    protected $description = 'Detect the templating engine used in your application';

    public function handle(): int
    {
        $this->info('Detecting templating engine...');
        $this->newLine();

        $engine = TemplateEngineDetector::detect();
        $engineName = TemplateEngineDetector::getEngineName();

        $this->line("Detected Engine: <fg=green>{$engineName}</>");
        $this->newLine();

        // Show what will be published
        $this->info('When you run "php artisan vendor:publish --tag=modular-views", the following will be published:');
        $this->newLine();

        $viewFiles = TemplateEngineDetector::getViewFilesToPublish();
        
        foreach ($viewFiles as $source => $destination) {
            $this->line("  <fg=yellow>From:</> {$source}");
            $this->line("  <fg=cyan>To:</> {$destination}");
            $this->newLine();
        }

        // Show recommendations
        $this->info('Publishing Options:');
        $this->line('  • <fg=green>php artisan vendor:publish --tag=modular-views</> - Auto-detect and publish appropriate views');
        $this->line('  • <fg=green>php artisan vendor:publish --tag=modular-views-blade</> - Force publish Blade templates');
        $this->line('  • <fg=green>php artisan vendor:publish --tag=modular-views-inertia</> - Force publish Inertia components');
        $this->line('  • <fg=green>php artisan vendor:publish --tag=modular-routes</> - Publish route files');
        $this->newLine();

        return self::SUCCESS;
    }
}
