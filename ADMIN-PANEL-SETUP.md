# Admin Panel Setup Guide

The Laravel Modular System includes a complete admin panel built with Inertia.js and Vue 3 for managing modules and settings.

## Features

- **Dashboard**: Overview of modules with statistics
- **Module Management**: Enable, disable, upload, download, and uninstall modules
- **Settings Management**: Configure system settings by groups
- **Authentication**: Protected routes requiring authentication

## Installation

### 1. Publish Package Assets

```bash
# Publish all assets
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
php artisan vendor:publish --tag=modular-views
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Update Vite Configuration

The admin panel Vue components need to be included in your Vite build. Update `vite.config.js`:

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
                // Include admin panel components
                ...glob.sync('resources/js/Pages/Admin/**/*.vue'),
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

### 4. Install Dependencies

```bash
npm install glob
npm run build
```

### 5. Set Up Authentication

The admin routes require authentication. Make sure you have Laravel's authentication set up:

```bash
# If using Laravel Breeze
composer require laravel/breeze --dev
php artisan breeze:install vue
npm install && npm run build
php artisan migrate
```

## Routes

The package provides these admin routes (all require authentication):

```
GET  /admin                      - Dashboard
GET  /admin/modules              - Module list
POST /admin/modules/enable       - Enable module
POST /admin/modules/disable      - Disable module
POST /admin/modules/upload       - Upload module ZIP
POST /admin/modules/uninstall    - Uninstall module
GET  /admin/modules/download/{name} - Download module
GET  /admin/settings             - Settings page
POST /admin/settings/{group}     - Update settings
```

## Usage

### Access the Admin Panel

1. Make sure you're logged in
2. Visit `/admin` in your browser
3. You'll see the dashboard with module statistics

### Managing Modules

**Enable/Disable Modules:**
1. Go to `/admin/modules`
2. Click "Enable" or "Disable" next to any module

**Upload New Module:**
1. Go to `/admin/modules`
2. Click "Upload Module"
3. Select a ZIP file containing your module
4. Optionally specify a custom module name
5. Click "Upload Module"

**Download Module:**
1. Go to `/admin/modules`
2. Click "Download" next to the module
3. A ZIP file will be downloaded

**Uninstall Module:**
1. Go to `/admin/modules`
2. Click "Uninstall" next to the module
3. Confirm the action

### Managing Settings

1. Go to `/admin/settings`
2. Select a settings group (General, OTP, System)
3. Modify settings as needed
4. Click "Save Settings"

## Customization

### Customize Routes

If you want to change the route prefix or add middleware, edit the published `routes/modular-web.php`:

```php
Route::middleware(['web', 'auth', 'your-middleware'])->prefix('your-prefix')->name('admin.')->group(function () {
    // Routes...
});
```

### Customize Views

The Vue components are published to `resources/js/Pages/Admin/`. You can modify them:

- `Admin/Dashboard.vue` - Dashboard page
- `Admin/Modules/Index.vue` - Module management
- `Admin/Settings/Index.vue` - Settings management
- `Admin/Layout.vue` - Admin layout wrapper

### Add Custom Middleware

To add authorization or custom middleware:

```php
// In routes/modular-web.php
Route::middleware(['web', 'auth', 'can:manage-modules'])->prefix('admin')->name('admin.')->group(function () {
    // Routes...
});
```

### Customize Layout

Edit `resources/js/Pages/Admin/Layout.vue` to:
- Change navigation
- Add user menu
- Modify styling
- Add breadcrumbs

## Module ZIP Structure

When uploading modules, the ZIP should contain:

```
YourModule/
├── module.json          # Required
├── Http/
│   └── Controllers/
├── resources/
│   ├── js/
│   │   └── Pages/
│   └── views/
└── routes/
    └── web.php
```

**module.json example:**
```json
{
    "name": "YourModule",
    "version": "1.0.0",
    "description": "Module description",
    "author": "Your Name",
    "enabled": false,
    "providers": [
        "Modules\\YourModule\\Providers\\YourModuleServiceProvider"
    ],
    "dependencies": []
}
```

## Troubleshooting

### Issue: 404 on Admin Routes

**Solution:** Make sure routes are loaded. Check `bootstrap/app.php` or run:
```bash
php artisan route:list | grep admin
```

### Issue: Vue Components Not Found

**Solution:**
1. Ensure components are published: `php artisan vendor:publish --tag=modular-views`
2. Rebuild assets: `npm run build`
3. Check Vite config includes admin components

### Issue: Authentication Required

**Solution:** The admin panel requires authentication. Make sure:
1. You're logged in
2. Authentication is set up (Breeze, Jetstream, etc.)
3. User model exists

### Issue: Module Upload Fails

**Solution:**
1. Check PHP upload limits in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
2. Ensure `storage/app/temp_modules` is writable
3. Check ZIP file structure includes `module.json`

### Issue: Settings Not Saving

**Solution:**
1. Ensure migrations are run: `php artisan migrate`
2. Check `settings` table exists
3. Verify database connection

## Security

### Authentication

All admin routes require authentication by default:

```php
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    // Routes...
});
```

### Authorization

Add authorization policies for fine-grained control:

```php
// In AuthServiceProvider
Gate::define('manage-modules', function ($user) {
    return $user->is_admin;
});

// In routes
Route::middleware(['web', 'auth', 'can:manage-modules'])->prefix('admin')->name('admin.')->group(function () {
    // Routes...
});
```

### File Upload Security

The package validates:
- File type (ZIP only)
- File size (configurable in `config/modular-system.php`)
- ZIP structure (must contain `module.json`)
- Module name conflicts

## Configuration

Edit `config/modular-system.php`:

```php
return [
    'modules_path' => base_path('modules'),
    'cache_enabled' => true,
    'cache_ttl' => 3600,
    'upload_max_size' => 2048, // KB
    'allowed_extensions' => ['zip'],
    'api_prefix' => 'v1/admin',
    'web_prefix' => 'admin',
];
```

## API Alternative

If you prefer API endpoints over Inertia, the package also provides REST API routes. See `routes/modular-api.php`.

## Support

For issues or questions:
1. Check this documentation
2. Review `INERTIA-INTEGRATION.md` for Inertia-specific issues
3. Check Laravel logs: `storage/logs/laravel.log`
4. Enable debug mode in `.env`: `APP_DEBUG=true`
