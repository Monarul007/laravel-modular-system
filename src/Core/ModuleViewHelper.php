<?php

namespace Monarul007\LaravelModularSystem\Core;

use Illuminate\Support\Facades\File;

class ModuleViewHelper
{
    /**
     * Get the view path for a module
     */
    public static function view(string $moduleName, string $view): string
    {
        return strtolower($moduleName) . '::' . $view;
    }

    /**
     * Get the asset path for a module
     */
    public static function asset(string $moduleName, string $asset): string
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $assetPath = "{$modulesPath}/{$moduleName}/resources/js/{$asset}";
        
        if (File::exists($assetPath)) {
            return "/modules/{$moduleName}/js/{$asset}";
        }
        
        return '';
    }

    /**
     * Check if a module view exists
     */
    public static function viewExists(string $moduleName, string $view): bool
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $viewPath = "{$modulesPath}/{$moduleName}/resources/views/{$view}.blade.php";
        
        return File::exists($viewPath);
    }

    /**
     * Get all views for a module
     */
    public static function getModuleViews(string $moduleName): array
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $viewsPath = "{$modulesPath}/{$moduleName}/resources/views";
        
        if (!File::exists($viewsPath)) {
            return [];
        }
        
        $views = [];
        $files = File::allFiles($viewsPath);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                $viewName = str_replace(['/', '\\', '.blade.php'], ['.', '.', ''], $relativePath);
                $views[] = $viewName;
            }
        }
        
        return $views;
    }

    /**
     * Get the public path for module assets
     */
    public static function publicPath(string $moduleName): string
    {
        return public_path("modules/{$moduleName}");
    }

    /**
     * Publish module assets to public directory
     */
    public static function publishAssets(string $moduleName): bool
    {
        $modulesPath = config('modular-system.modules_path', base_path('modules'));
        $published = false;
        
        // Publish JS assets
        $jsSource = "{$modulesPath}/{$moduleName}/resources/js";
        $jsTarget = public_path("modules/{$moduleName}/js");
        
        if (File::exists($jsSource)) {
            File::ensureDirectoryExists($jsTarget);
            File::copyDirectory($jsSource, $jsTarget);
            $published = true;
        }
        
        // Publish CSS assets
        $cssSource = "{$modulesPath}/{$moduleName}/resources/css";
        $cssTarget = public_path("modules/{$moduleName}/css");
        
        if (File::exists($cssSource)) {
            File::ensureDirectoryExists($cssTarget);
            File::copyDirectory($cssSource, $cssTarget);
            $published = true;
        }
        
        return $published;
    }
}
