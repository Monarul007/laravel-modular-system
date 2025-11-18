<?php

namespace Monarul007\LaravelModularSystem\Support;

use Illuminate\Support\Facades\File;

class TemplateEngineDetector
{
    /**
     * Detect the templating engine used in the application
     * 
     * @return string 'blade', 'inertia-vue', 'inertia-react', or 'unknown'
     */
    public static function detect(): string
    {
        // Check for Inertia.js
        if (self::hasInertia()) {
            return self::detectInertiaFramework();
        }
        
        // Default to Blade if no Inertia detected
        if (self::hasBladeViews()) {
            return 'blade';
        }
        
        return 'unknown';
    }

    /**
     * Check if Inertia.js is installed
     */
    protected static function hasInertia(): bool
    {
        $composerPath = base_path('composer.json');
        
        if (!File::exists($composerPath)) {
            return false;
        }
        
        $composer = json_decode(File::get($composerPath), true);
        
        return isset($composer['require']['inertiajs/inertia-laravel']);
    }

    /**
     * Detect which frontend framework is used with Inertia
     */
    protected static function detectInertiaFramework(): string
    {
        $packageJsonPath = base_path('package.json');
        
        if (!File::exists($packageJsonPath)) {
            return 'inertia-vue'; // Default to Vue
        }
        
        $packageJson = json_decode(File::get($packageJsonPath), true);
        $dependencies = array_merge(
            $packageJson['dependencies'] ?? [],
            $packageJson['devDependencies'] ?? []
        );
        
        // Check for React
        if (isset($dependencies['react']) || isset($dependencies['@inertiajs/react'])) {
            return 'inertia-react';
        }
        
        // Check for Vue (default)
        if (isset($dependencies['vue']) || isset($dependencies['@inertiajs/vue3']) || isset($dependencies['@inertiajs/vue2'])) {
            return 'inertia-vue';
        }
        
        // Default to Vue if Inertia is installed but framework is unclear
        return 'inertia-vue';
    }

    /**
     * Check if Blade views exist in the application
     */
    protected static function hasBladeViews(): bool
    {
        $viewsPath = resource_path('views');
        
        if (!File::exists($viewsPath)) {
            return false;
        }
        
        // Check for any .blade.php files
        $bladeFiles = File::glob($viewsPath . '/**/*.blade.php');
        
        return count($bladeFiles) > 0;
    }

    /**
     * Get the appropriate view files to publish based on detected engine
     */
    public static function getViewFilesToPublish(): array
    {
        $engine = self::detect();
        $packagePath = __DIR__ . '/../../resources';
        
        switch ($engine) {
            case 'blade':
                return [
                    $packagePath . '/views' => resource_path('views/vendor/modular-system'),
                ];
                
            case 'inertia-vue':
                return [
                    $packagePath . '/js/Pages' => resource_path('js/Pages'),
                ];
                
            case 'inertia-react':
                // Publish JSX files for React
                $reactFiles = [];
                $sourcePath = $packagePath . '/js/Pages';
                $destPath = resource_path('js/Pages');
                
                // We'll copy .jsx files instead of .vue files
                return [
                    $sourcePath => $destPath,
                ];
                
            default:
                // Publish both Blade and Inertia Vue as fallback
                return [
                    $packagePath . '/views' => resource_path('views/vendor/modular-system'),
                    $packagePath . '/js/Pages' => resource_path('js/Pages'),
                ];
        }
    }

    /**
     * Get human-readable name of detected engine
     */
    public static function getEngineName(): string
    {
        $engine = self::detect();
        
        return match($engine) {
            'blade' => 'Blade Templates',
            'inertia-vue' => 'Inertia.js with Vue',
            'inertia-react' => 'Inertia.js with React',
            default => 'Unknown',
        };
    }

    /**
     * Check if specific engine is detected
     */
    public static function isEngine(string $engine): bool
    {
        return self::detect() === $engine;
    }
}
