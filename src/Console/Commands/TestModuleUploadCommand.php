<?php

namespace Monarul007\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;
use Monarul007\LaravelModularSystem\Core\ModuleManager;

class TestModuleUploadCommand extends Command
{
    protected $signature = 'test:module-upload {zipPath : Path to the module ZIP file}';
    protected $description = 'Test module upload from ZIP file';

    public function handle(ModuleManager $moduleManager)
    {
        $zipPath = $this->argument('zipPath');

        if (!file_exists($zipPath)) {
            $this->error("ZIP file not found: {$zipPath}");
            return 1;
        }

        $this->info("Testing module upload from: {$zipPath}");

        $result = $moduleManager->installModuleFromZip($zipPath);

        if ($result['success']) {
            $this->info($result['message']);
            $this->table(
                ['Property', 'Value'],
                collect($result['module'])->map(fn($value, $key) => [
                    $key,
                    is_array($value) ? json_encode($value) : $value
                ])->toArray()
            );
            return 0;
        }

        $this->error($result['message']);
        return 1;
    }
}
