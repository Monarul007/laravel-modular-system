# Smart View Publishing Guide

The Laravel Modular System now includes **smart template engine detection** that automatically publishes the appropriate view files based on your application's setup.

## Overview

When you publish views, the package automatically detects whether your application uses:
- **Blade Templates** (traditional Laravel views)
- **Inertia.js with Vue** (Vue 3 components)
- **Inertia.js with React** (React/JSX components)

## Detection Logic

The package detects your templating engine by:

1. **Checking for Inertia.js** in `composer.json`
2. **Detecting frontend framework** in `package.json`:
   - Looks for `react` or `@inertiajs/react` → React
   - Looks for `vue` or `@inertiajs/vue3` → Vue
3. **Falling back to Blade** if no Inertia is found

## Publishing Views

### Automatic Detection (Recommended)

```bash
# Detect what will be published
php artisan modular:detect-engine

# Publish views based on auto-detection
php artisan vendor:publish --tag=modular-views
```

This will publish:
- **Blade templates** to `resources/views/vendor/modular-system/` if Blade is detected
- **Vue components** to `resources/js/Pages/` if Inertia + Vue is detected
- **React components** to `resources/js/Pages/` if Inertia + React is detected

### Manual Selection

You can force publish specific view types:

```bash
# Force publish Blade templates only
php artisan vendor:publish --tag=modular-views-blade

# Force publish Inertia components only (Vue or React)
php artisan vendor:publish --tag=modular-views-inertia

# Publish routes
php artisan vendor:publish --tag=modular-routes

# Publish everything
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"
```

## Available View Files

### Blade Templates

Located in `resources/views/admin/`:

```
admin/
├── layout.blade.php           # Main admin layout
├── dashboard.blade.php         # Dashboard page
├── modules/
│   └── index.blade.php        # Module management

```

**Features:**
- Traditional Laravel Blade syntax
- Tailwind CSS styling
- Form handling with CSRF protection
- Flash message support
- Modal dialogs

### Inertia Vue Components

Located in `resources/js/Pages/Admin/`:

```
Admin/
├── Layout.vue                 # Main admin layout
├── Dashboard.vue              # Dashboard page
├── Modules/
│   └── Index.vue             # Module management

```

**Features:**
- Vue 3 Composition API
- Inertia.js integration
- Reactive state management
- Component-based architecture

### Inertia React Components

Located in `resources/js/Pages/Admin/`:

```
Admin/
├── Layout.jsx                 # Main admin layout
├── Dashboard.jsx              # Dashboard page
├── Modules/
│   └── Index.jsx             # Module management

```

**Features:**
- React functional components
- Hooks (useState, useForm)
- Inertia.js React adapter
- JSX syntax

## Using Published Views

### With Blade

After publishing, the views are automatically available via the `modular-system` namespace:

```php
// In your routes or controllers
Route::get('/admin', function () {
    return view('modular-system::admin.dashboard', [
        'stats' => [...],
        'recent_modules' => [...]
    ]);
});
```

Or extend the layout in your own views:

```blade
@extends('modular-system::admin.layout')

@section('content')
    <h1>My Custom Admin Page</h1>
@endsection
```

### With Inertia (Vue or React)

The controllers already use Inertia responses:

```php
use Inertia\Inertia;

public function dashboard()
{
    return Inertia::render('Admin/Dashboard', [
        'stats' => [...],
        'recent_modules' => [...]
    ]);
}
```

The published components will be automatically used by Inertia.

## Customization

### Customizing Blade Views

After publishing, edit the files in `resources/views/vendor/modular-system/`:

```blade
{{-- resources/views/vendor/modular-system/admin/layout.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>My Custom Admin</title>
    {{-- Add your custom styles --}}
</head>
<body>
    @yield('content')
</body>
</html>
```

### Customizing Inertia Components

After publishing, edit the files in `resources/js/Pages/Admin/`:

```vue
<!-- resources/js/Pages/Admin/Dashboard.vue -->
<template>
    <AdminLayout>
        <h1>My Custom Dashboard</h1>
        <!-- Your custom content -->
    </AdminLayout>
</template>
```

Or for React:

```jsx
// resources/js/Pages/Admin/Dashboard.jsx
export default function Dashboard({ stats }) {
    return (
        <AdminLayout>
            <h1>My Custom Dashboard</h1>
            {/* Your custom content */}
        </AdminLayout>
    );
}
```

## Routes Integration

The package routes work with both Blade and Inertia:

```php
// routes/web.php (published as routes/modular-web.php)
Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/modules', [AdminModuleController::class, 'index']);
});
```

The controllers automatically return the appropriate response type based on your setup.

## Switching Between Engines

If you switch from Blade to Inertia (or vice versa):

1. **Remove old views:**
   ```bash
   # Remove Blade views
   rm -rf resources/views/vendor/modular-system
   
   # Remove Inertia components
   rm -rf resources/js/Pages/Admin
   ```

2. **Republish with new detection:**
   ```bash
   php artisan modular:detect-engine
   php artisan vendor:publish --tag=modular-views --force
   ```

## Troubleshooting

### Views Not Found

If views aren't loading:

```bash
# Clear view cache
php artisan view:clear

# Verify detection
php artisan modular:detect-engine

# Republish
php artisan vendor:publish --tag=modular-views --force
```

### Wrong Engine Detected

Force publish the correct views:

```bash
# For Blade
php artisan vendor:publish --tag=modular-views-blade --force

# For Inertia
php artisan vendor:publish --tag=modular-views-inertia --force
```

### Inertia Flash Messages Error

**Problem:** `Cannot read properties of undefined (reading 'success')`

**Solution:** Configure Inertia middleware to share flash messages. See [INERTIA-SETUP.md](INERTIA-SETUP.md) for detailed instructions.

Quick fix - add to your `app/Http/Middleware/HandleInertiaRequests.php`:

```php
public function share(Request $request): array
{
    return array_merge(parent::share($request), [
        'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error' => fn () => $request->session()->get('error'),
        ],
    ]);
}
```

### Inertia Components Not Rendering

Ensure you have Inertia properly configured:

```bash
# Install Inertia
composer require inertiajs/inertia-laravel

# For Vue
npm install @inertiajs/vue3

# For React
npm install @inertiajs/react
```

See [INERTIA-SETUP.md](INERTIA-SETUP.md) for complete setup guide.

## Best Practices

1. **Always detect first:**
   ```bash
   php artisan modular:detect-engine
   ```

2. **Publish routes with views:**
   ```bash
   php artisan vendor:publish --tag=modular-routes
   php artisan vendor:publish --tag=modular-views
   ```

3. **Customize after publishing:**
   - Don't modify package files directly
   - Always work with published copies

4. **Version control:**
   - Commit published views to your repository
   - Document any customizations

## Complete Setup Example

### For Blade Applications

```bash
# 1. Install package
composer require monarul007/laravel-modular-system

# 2. Detect engine
php artisan modular:detect-engine

# 3. Publish everything
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
php artisan vendor:publish --tag=modular-views

# 4. Run migrations
php artisan migrate

# 5. Access admin panel
# Visit: http://your-app.test/admin
```

### For Inertia + Vue Applications

```bash
# 1. Install package
composer require monarul007/laravel-modular-system

# 2. Detect engine (should show "Inertia.js with Vue")
php artisan modular:detect-engine

# 3. Publish everything
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
php artisan vendor:publish --tag=modular-views

# 4. Run migrations
php artisan migrate

# 5. Build assets
npm run dev

# 6. Access admin panel
# Visit: http://your-app.test/admin
```

### For Inertia + React Applications

```bash
# 1. Install package
composer require monarul007/laravel-modular-system

# 2. Detect engine (should show "Inertia.js with React")
php artisan modular:detect-engine

# 3. Publish everything
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
php artisan vendor:publish --tag=modular-views

# 4. Run migrations
php artisan migrate

# 5. Build assets
npm run dev

# 6. Access admin panel
# Visit: http://your-app.test/admin
```

## Summary

The smart view publishing system ensures that:
- ✅ You get the right views for your stack
- ✅ No manual configuration needed
- ✅ Easy to override and customize
- ✅ Supports all major Laravel templating approaches
- ✅ Routes work seamlessly with published views

For more information, see the main [README.md](README.md) or [ARCHITECTURE.md](ARCHITECTURE.md).
