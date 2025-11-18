# Inertia.js Setup Guide

## Quick Fix for Flash Messages

If you're seeing errors like `Cannot read properties of undefined (reading 'success')`, you need to ensure Inertia is properly sharing flash messages.

### Option 1: Use Your Existing Inertia Middleware (Recommended)

If you already have `app/Http/Middleware/HandleInertiaRequests.php`, update the `share()` method:

```php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error' => fn () => $request->session()->get('error'),
        ],
        'errors' => fn () => $request->session()->get('errors') 
            ? $request->session()->get('errors')->getBag('default')->getMessages()
            : (object) [],
    ]);
}
```

### Option 2: Use Package Middleware

The package includes a middleware you can use. Add it to your `bootstrap/app.php` or `app/Http/Kernel.php`:

```php
// For Laravel 11+ (bootstrap/app.php)
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \Monarul007\LaravelModularSystem\Http\Middleware\HandleInertiaRequests::class,
    ]);
})

// For Laravel 10 and below (app/Http/Kernel.php)
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \Monarul007\LaravelModularSystem\Http\Middleware\HandleInertiaRequests::class,
    ],
];
```

### Option 3: Quick Fix in Vue Components

If you can't modify middleware, the Vue components now use optional chaining (`?.`) to safely access flash messages. Make sure you're using the latest version:

```bash
php artisan vendor:publish --tag=modular-views --force
```

## Complete Inertia Setup

### 1. Install Inertia

```bash
# Laravel package
composer require inertiajs/inertia-laravel

# Vue 3
npm install @inertiajs/vue3

# OR React
npm install @inertiajs/react
```

### 2. Root Template

Create `resources/views/app.blade.php`:

```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

### 3. Setup Vue 3

Create/update `resources/js/app.js`:

```javascript
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
```

### 4. Setup React

Create/update `resources/js/app.jsx`:

```javascript
import { createRoot } from 'react-dom/client'
import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />)
    },
})
```

### 5. Vite Configuration

Update `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
// OR for React
// import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
        // OR for React
        // react(),
    ],
});
```

### 6. Install Ziggy (for route() helper)

```bash
composer require tightenco/ziggy
```

Add to your root template:

```blade
@routes
```

### 7. Build Assets

```bash
npm install
npm run dev
```

## Troubleshooting

### Flash Messages Not Showing

**Problem:** Flash messages don't appear after actions.

**Solution:** Ensure your Inertia middleware shares flash messages (see Option 1 above).

### Cannot Read Properties of Undefined

**Problem:** `Cannot read properties of undefined (reading 'success')`

**Solution:** 
1. Update Vue components to use optional chaining (republish views)
2. Configure Inertia middleware to share flash data

### Routes Not Found

**Problem:** `route is not defined`

**Solution:** Install and configure Ziggy:

```bash
composer require tightenco/ziggy
```

Add `@routes` to your app.blade.php template.

### Components Not Loading

**Problem:** Inertia pages show blank screen.

**Solution:**
1. Check browser console for errors
2. Verify Vite is running: `npm run dev`
3. Check component paths match Inertia render calls

### Authentication Required

The admin routes use `auth` middleware. Make sure you're logged in:

```php
// In your routes or controller
Route::middleware(['web', 'auth'])->group(function () {
    // Admin routes
});
```

## Testing Your Setup

1. **Visit the admin panel:**
   ```
   http://your-app.test/admin
   ```

2. **Check for errors in browser console** (F12)

3. **Test flash messages:**
   - Try enabling/disabling a module
   - You should see success/error messages

4. **Verify Inertia is working:**
   - Page transitions should be smooth (no full reload)
   - Browser back button should work

## Complete Example

Here's a complete working setup:

### app/Http/Middleware/HandleInertiaRequests.php

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
```

### resources/views/app.blade.php

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>
```

### resources/js/app.js (Vue)

```javascript
import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
```

## Need More Help?

- **Inertia Docs:** https://inertiajs.com/
- **Laravel Docs:** https://laravel.com/docs
- **Package Docs:** [README.md](README.md)
- **View Publishing:** [VIEW-PUBLISHING.md](VIEW-PUBLISHING.md)
