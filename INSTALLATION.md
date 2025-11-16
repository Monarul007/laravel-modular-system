# Installation Guide

## Step 1: Install via Composer

### Option A: From Packagist (Recommended)

```bash
composer require monarul007/laravel-modular-system
```

### Option B: From Local Path (Development)

Add to your Laravel app's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../packages/laravel-modular-system"
        }
    ],
    "require": {
        "monarul007/laravel-modular-system": "*"
    }
}
```

Then run:

```bash
composer update monarul007/laravel-modular-system
```

### Option C: From GitHub

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/monarul007/laravel-modular-system"
        }
    ]
}
```

```bash
composer require monarul007/laravel-modular-system
```

## Step 2: Publish Assets

```bash
# Publish everything
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
```

## Step 3: Run Migrations

```bash
php artisan migrate
```

## Step 4: Configure (Optional)

Edit `config/modular-system.php`:

```php
return [
    'modules_path' => base_path('modules'),
    'cache_enabled' => true,
    'cache_ttl' => 3600,
    'upload_max_size' => 2048,
];
```

## Step 5: Load Routes (If Published)

If you published routes, add them to your `bootstrap/app.php` or route service provider:

```php
// In bootstrap/app.php (Laravel 11+)
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/modular-api.php'));
    }
)
```

## Step 6: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Verification

Test the installation:

```bash
# List modules
php artisan module:list

# Create a test module
php artisan make:module TestModule

# Check if it appears
php artisan module:list
```

## Troubleshooting

### Issue: Commands not found

```bash
composer dump-autoload
php artisan config:clear
```

### Issue: Routes not loading

Make sure routes are published or loaded in your application.

### Issue: Modules directory not created

```bash
mkdir modules
chmod 755 modules
```

### Issue: Permission denied

```bash
chmod -R 755 modules
chmod -R 755 storage
```

## Next Steps

- Create your first module: `php artisan make:module YourModule`
- Read the [README.md](README.md) for usage examples
- Check the [API documentation](API.md) for endpoint details
