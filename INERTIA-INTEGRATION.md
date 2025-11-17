# Inertia.js Integration for Laravel Modular System

## Overview

This package provides full Inertia.js compatibility for modules, solving two main problems:
1. **Undefined `$page` variable** when using `@inertiaHead` in module Blade views
2. **Easy Inertia responses** from module controllers with proper component namespacing

## Quick Start

### Return Inertia Response

```php
// Option 1: Helper function (Recommended)
return module_inertia('Blog', 'Posts/Index', ['posts' => $posts]);

// Option 2: Facade
use Monarul007\LaravelModularSystem\Facades\ModuleInertia;
return ModuleInertia::render('Blog', 'Posts/Index', ['posts' => $posts]);

// Option 3: Base controller
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class PostController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function index()
    {
        return $this->inertia('Posts/Index', ['posts' => $posts]);
    }
}
```

### Return Blade View with Inertia Support

```php
// Option 1: Helper function (Recommended)
return module_view('blog', 'welcome', ['title' => 'Welcome']);

// Option 2: Base controller
class PageController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function about()
    {
        return $this->moduleView('about', ['title' => 'About']);
    }
}
```

## Setup

### 1. Update Vite Configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                // Include module components
                ...glob.sync('modules/*/resources/js/Pages/**/*.vue'),
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

### 2. Install Dependencies

```bash
npm install glob
npm run build
```

### 3. Optional: Add Middleware

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Monarul007\LaravelModularSystem\Http\Middleware\ShareInertiaDataWithModules::class,
    ]);
})
```

## Module Structure

```
modules/YourModule/
├── Http/
│   └── Controllers/
│       └── MyController.php
├── resources/
│   ├── js/
│   │   └── Pages/
│   │       ├── Index.vue
│   │       └── Show.vue
│   └── views/
│       └── welcome.blade.php
└── routes/
    └── web.php
```

## Complete Example

### Controller

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class PostController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';

    // Inertia response
    public function index()
    {
        return $this->inertia('Posts/Index', [
            'posts' => Post::all()
        ]);
    }

    // Blade view with Inertia support
    public function about()
    {
        return $this->moduleView('about', [
            'title' => 'About Us'
        ]);
    }
}
```

### Vue Component

```vue
<!-- modules/Blog/resources/js/Pages/Posts/Index.vue -->
<template>
    <div class="container">
        <h1>Blog Posts</h1>
        <div v-for="post in posts" :key="post.id">
            <h2>{{ post.title }}</h2>
            <p>{{ post.excerpt }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    posts: Array
});
</script>
```

### Blade View with Inertia

```blade
<!-- modules/Blog/resources/views/about.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @inertiaHead
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>This view uses @inertiaHead without errors!</p>
</body>
</html>
```

### Routes

```php
// modules/Blog/routes/web.php
use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('blog')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/about', [PostController::class, 'about']);
});
```

## Migration Guide

### Fix Undefined $page Error

**Before (Broken):**
```php
return view('blog::welcome', ['title' => 'Welcome']);
```

**After (Fixed):**
```php
return module_view('blog', 'welcome', ['title' => 'Welcome']);
```

### Add Inertia to Existing Controller

**Before:**
```php
class PostController extends Controller
{
    public function index()
    {
        return view('blog::posts.index', ['posts' => Post::all()]);
    }
}
```

**After:**
```php
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class PostController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function index()
    {
        return $this->inertia('Posts/Index', ['posts' => Post::all()]);
    }
}
```

## Troubleshooting

### Issue: Undefined variable $page

**Error:**
```
Undefined variable $page in welcome.blade.php
```

**Solution:**
Use `module_view()` helper instead of `view()`:

```php
// ❌ Wrong
return view('blog::welcome');

// ✅ Correct
return module_view('blog', 'welcome');
```

### Issue: Component Not Found

**Error:**
```
Inertia page component not found: Blog/Welcome
```

**Solutions:**

1. Check component exists at: `modules/Blog/resources/js/Pages/Welcome.vue`

2. Update Vite config to include module components:
```javascript
...glob.sync('modules/*/resources/js/Pages/**/*.vue')
```

3. Rebuild assets:
```bash
npm run build
```

### Issue: Props Not Passed

**Problem:** Props are not available in Vue component.

**Solution:** Use `defineProps`:

```vue
<script setup>
const props = defineProps({
    posts: Array,
    title: String
});
</script>
```

### Issue: Routes Not Working

**Solution:**
1. Check module is enabled: `php artisan module:list`
2. Check routes file exists: `modules/YourModule/routes/web.php`
3. Clear route cache: `php artisan route:clear`

## API Reference

### Helper Functions

#### `module_inertia(string $moduleName, string $component, array $props = [])`

Returns an Inertia response with proper component namespacing.

```php
return module_inertia('Blog', 'Posts/Index', [
    'posts' => Post::all()
]);
```

#### `module_view(string $moduleName, string $view, array $data = [])`

Returns a Blade view with automatic `$page` variable for Inertia support.

```php
return module_view('blog', 'welcome', [
    'title' => 'Welcome'
]);
```

### ModuleInertiaController

Base controller providing convenient methods for Inertia responses.

```php
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class MyController extends ModuleInertiaController
{
    protected string $moduleName = 'MyModule';
    
    // Return Inertia response
    protected function inertia(string $component, array $props = [])
    
    // Return Blade view with Inertia support
    protected function moduleView(string $view, array $data = [])
}
```

### ModuleInertia Facade

```php
use Monarul007\LaravelModularSystem\Facades\ModuleInertia;

// Render Inertia response
ModuleInertia::render(string $moduleName, string $component, array $props = [])

// Check if current request is Inertia
ModuleInertia::isInertiaRequest()

// Share data with all Inertia responses
ModuleInertia::share($key, $value = null)

// Get root view for module
ModuleInertia::getRootView(string $moduleName)

// Set root view
ModuleInertia::setRootView(string $view)
```

## Best Practices

1. **Use Helper Functions**: Prefer `module_inertia()` and `module_view()` for consistency

2. **Extend ModuleInertiaController**: For controllers primarily using Inertia

3. **Organize Components**: Keep module components in `modules/{Module}/resources/js/Pages/`

4. **Type Props**: Always define prop types in Vue components

5. **Share Common Data**: Use middleware to share data needed on all pages

6. **Handle Errors**: Provide fallbacks for missing components or data

## Testing

### Manual Testing

```bash
# 1. Create test module
php artisan make:module TestInertia

# 2. Enable module
php artisan module:enable TestInertia

# 3. Build assets
npm run build

# 4. Start server
php artisan serve

# 5. Visit routes and verify:
# - No $page errors
# - Inertia pages render
# - Props are passed correctly
```

### Automated Testing

```php
// tests/Feature/ModuleInertiaTest.php
public function test_inertia_page_renders()
{
    $response = $this->get('/blog/posts');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('Blog/Posts/Index')
            ->has('posts')
    );
}

public function test_blade_view_with_inertia_head()
{
    $response = $this->get('/blog/about');
    
    $response->assertStatus(200);
    $response->assertViewIs('blog::about');
    $response->assertViewHas('page');
}
```

## Common Patterns

### Pattern 1: Mixed Blade and Inertia

```php
class BlogController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    // Inertia SPA page
    public function dashboard()
    {
        return $this->inertia('Dashboard', ['stats' => $stats]);
    }
    
    // Blade page with Inertia support
    public function about()
    {
        return $this->moduleView('about', ['content' => $content]);
    }
    
    // Traditional Blade (no Inertia directives)
    public function legacy()
    {
        return view('blog::legacy');
    }
}
```

### Pattern 2: API + Inertia

```php
public function show($id)
{
    $post = Post::findOrFail($id);
    
    // Return JSON for API, Inertia for web
    if (request()->wantsJson()) {
        return response()->json($post);
    }
    
    return $this->inertia('Posts/Show', ['post' => $post]);
}
```

### Pattern 3: Shared Data

```php
class BlogController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            \Inertia\Inertia::share([
                'categories' => Category::all(),
                'user' => auth()->user(),
            ]);
            
            return $next($request);
        });
    }
}
```

## Backward Compatibility

✅ Existing Blade views still work  
✅ Existing controllers still work  
✅ No breaking changes  
✅ Migrate at your own pace  

You can migrate one controller at a time, or even one method at a time.

## Performance

- **Faster navigation**: SPA experience with Inertia
- **Reduced server load**: Only data is transferred, not full HTML
- **Better UX**: No full page refreshes
- **Optimized builds**: Use code splitting for large apps

## Requirements

- Laravel 11+
- Inertia.js 1.0+
- Vue 3 / React
- PHP 8.2+
- Node.js & npm

## Summary

The Inertia integration provides:

✅ Fixed `$page` undefined errors in module Blade views  
✅ Easy Inertia response helpers  
✅ Base controller for clean code  
✅ Proper component namespacing  
✅ Backward compatible  
✅ Production ready  

Use `module_inertia()` for Inertia responses and `module_view()` for Blade views with Inertia support.
