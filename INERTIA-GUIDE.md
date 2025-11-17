# Inertia.js Integration Guide

This guide explains how to use Inertia.js with the Laravel Modular System.

## Table of Contents

1. [Overview](#overview)
2. [Setup](#setup)
3. [Returning Inertia Responses](#returning-inertia-responses)
4. [Using Blade Views with Inertia](#using-blade-views-with-inertia)
5. [Module Structure](#module-structure)
6. [Examples](#examples)
7. [Troubleshooting](#troubleshooting)

## Overview

The Laravel Modular System provides full Inertia.js compatibility, allowing modules to:
- Return Inertia responses
- Use Blade views with Inertia directives
- Share data with Inertia components
- Have their own Vue/React components

## Setup

### 1. Ensure Inertia is Installed

Your main Laravel app should have Inertia installed:

```bash
composer require inertiajs/inertia-laravel
```

### 2. Module Structure for Inertia

```
modules/YourModule/
├── Http/
│   └── Controllers/
│       └── WelcomeController.php
├── resources/
│   ├── js/
│   │   └── Pages/
│   │       └── Welcome.vue
│   └── views/
│       ├── app.blade.php (optional custom root)
│       └── welcome.blade.php
└── routes/
    └── web.php
```

## Returning Inertia Responses

### Method 1: Using the Helper Function (Recommended)

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function index()
    {
        return module_inertia('Blog', 'Posts/Index', [
            'posts' => Post::all()
        ]);
    }
    
    public function show($id)
    {
        return module_inertia('Blog', 'Posts/Show', [
            'post' => Post::findOrFail($id)
        ]);
    }
}
```

### Method 2: Using the Facade

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Facades\ModuleInertia;

class PostController extends Controller
{
    public function index()
    {
        return ModuleInertia::render('Blog', 'Posts/Index', [
            'posts' => Post::all()
        ]);
    }
}
```

### Method 3: Extending ModuleInertiaController

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class PostController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function index()
    {
        return $this->inertia('Posts/Index', [
            'posts' => Post::all()
        ]);
    }
}
```

### Method 4: Using Inertia Directly

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class PostController extends Controller
{
    public function index()
    {
        // Component path: Blog/Posts/Index
        return Inertia::render('Blog/Posts/Index', [
            'posts' => Post::all()
        ]);
    }
}
```

## Using Blade Views with Inertia

### Problem: Undefined $page Variable

When using `@inertiaHead` in module Blade views, you might get an "undefined variable $page" error.

### Solution 1: Use the Helper Function

```php
// In your controller
public function welcome()
{
    return module_view('blog', 'welcome', [
        'title' => 'Welcome to Blog'
    ]);
}
```

The `module_view()` helper automatically provides the `$page` variable.

### Solution 2: Manually Pass $page

```php
use Inertia\Inertia;

public function welcome()
{
    return view('blog::welcome', [
        'page' => Inertia::getShared(),
        'title' => 'Welcome'
    ]);
}
```

### Solution 3: Use ModuleInertiaController

```php
class WelcomeController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function index()
    {
        return $this->moduleView('welcome', [
            'title' => 'Welcome'
        ]);
    }
}
```

## Module Structure

### Vue Component Location

Place your Vue components in the module's resources directory:

```
modules/Blog/resources/js/Pages/
├── Posts/
│   ├── Index.vue
│   ├── Show.vue
│   └── Edit.vue
└── Welcome.vue
```

### Vite Configuration

Update your `vite.config.js` to include module components:

```javascript
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
                // Include all module pages
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
    resolve: {
        alias: {
            '@': '/resources/js',
            '@modules': '/modules',
        },
    },
});
```

### Custom Root Template (Optional)

Modules can have their own Inertia root template:

```blade
<!-- modules/Blog/resources/views/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Blog Module</title>
    
    @routes
    @vite(['resources/js/app.js', "modules/Blog/resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

Then use it:

```php
use Monarul007\LaravelModularSystem\Facades\ModuleInertia;

ModuleInertia::setRootView(ModuleInertia::getRootView('Blog'));
```

## Examples

### Example 1: Simple Inertia Page

**Controller:**
```php
<?php

namespace Modules\Dashboard\Http\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return module_inertia('Dashboard', 'Index', [
            'stats' => [
                'users' => 100,
                'posts' => 500,
            ]
        ]);
    }
}
```

**Vue Component (modules/Dashboard/resources/js/Pages/Index.vue):**
```vue
<template>
    <div>
        <h1>Dashboard</h1>
        <p>Users: {{ stats.users }}</p>
        <p>Posts: {{ stats.posts }}</p>
    </div>
</template>

<script setup>
defineProps({
    stats: Object
});
</script>
```

**Route:**
```php
// modules/Dashboard/routes/web.php
use Modules\Dashboard\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index']);
```

### Example 2: Blade View with Inertia Directives

**Controller:**
```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;

class PageController extends Controller
{
    public function about()
    {
        return module_view('blog', 'about', [
            'title' => 'About Us',
            'content' => 'Welcome to our blog!'
        ]);
    }
}
```

**Blade View (modules/Blog/resources/views/about.blade.php):**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @inertiaHead
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $content }}</p>
</body>
</html>
```

### Example 3: Mixed Approach

**Controller:**
```php
<?php

namespace Modules\Shop\Http\Controllers;

use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class ProductController extends ModuleInertiaController
{
    protected string $moduleName = 'Shop';
    
    // Inertia response
    public function index()
    {
        return $this->inertia('Products/Index', [
            'products' => Product::all()
        ]);
    }
    
    // Blade view with Inertia support
    public function landing()
    {
        return $this->moduleView('landing', [
            'featured' => Product::featured()->get()
        ]);
    }
}
```

## Troubleshooting

### Issue 1: Undefined Variable $page

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

### Issue 2: Component Not Found

**Error:**
```
Inertia page component not found: Blog/Welcome
```

**Solutions:**

1. Check component path:
```
modules/Blog/resources/js/Pages/Welcome.vue
```

2. Update Vite config to include module components

3. Rebuild assets:
```bash
npm run build
```

### Issue 3: Props Not Passed to Component

**Problem:**
Props are not available in Vue component.

**Solution:**
Ensure you're using `defineProps`:

```vue
<script setup>
const props = defineProps({
    posts: Array,
    title: String
});
</script>
```

### Issue 4: Inertia Middleware Not Applied

**Problem:**
Module routes don't have Inertia middleware.

**Solution:**
Apply middleware in module routes:

```php
// modules/Blog/routes/web.php
Route::middleware(['web', 'inertia'])->group(function () {
    Route::get('/blog', [BlogController::class, 'index']);
});
```

Or add to bootstrap/app.php:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Monarul007\LaravelModularSystem\Http\Middleware\ShareInertiaDataWithModules::class,
    ]);
})
```

## Best Practices

1. **Use Helper Functions**: Prefer `module_inertia()` and `module_view()` for consistency

2. **Extend ModuleInertiaController**: For controllers that primarily use Inertia

3. **Organize Components**: Keep module components in `modules/{Module}/resources/js/Pages/`

4. **Share Module Data**: Use the middleware to share module info with all Inertia pages

5. **Type Props**: Always define prop types in Vue components

6. **Handle Errors**: Provide fallbacks for missing components or data

## Additional Resources

- [Inertia.js Documentation](https://inertiajs.com/)
- [Laravel Inertia Adapter](https://github.com/inertiajs/inertia-laravel)
- [Vue 3 Documentation](https://vuejs.org/)

## Support

If you encounter issues:
1. Check this guide's troubleshooting section
2. Verify Inertia is properly installed in your main app
3. Ensure module structure follows the conventions
4. Check browser console for JavaScript errors
