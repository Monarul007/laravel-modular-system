# Complete Testing Guide

## Setup in Laravel Application

### 1. Install Package

```bash
# In your Laravel app
composer require monarul007/laravel-modular-system

# Publish configuration
php artisan vendor:publish --tag=modular-config

# Publish routes (optional)
php artisan vendor:publish --tag=modular-routes

# Publish views (optional - auto-detects your stack)
php artisan vendor:publish --tag=modular-views
```

### 2. Verify Installation

```bash
# Check if commands are available
php artisan list | grep module

# You should see:
# make:module
# module:enable
# module:disable
# module:list
# module:remove
# module:make-controller
# module:make-model
# module:make-migration
# module:make-middleware
# module:make-command
# module:publish
# module:set-alias
# modular:detect-engine
```

---

## Test 1: Basic Module Management

### Create a Test Module

```bash
php artisan make:module Blog
```

**Expected Output:**
```
Creating new module: Blog
Do you want to create this module? (yes/no) [yes]: yes

Route Alias Configuration
Module: Blog
Suggested alias: blog
Choose route alias option:
  [use_suggested] Use suggested (blog)
  [custom] Enter custom alias
  [skip] Skip (use default)
```

**Verify:**
```bash
# Check module was created
ls modules/Blog

# Should see:
# Console/
# Http/
# Models/
# Database/
# routes/
# resources/
# config/
# Providers/
# module.json
```

### List Modules

```bash
php artisan module:list
```

**Expected Output:**
```
+------+---------+----------+-------------+
| Name | Version | Status   | Description |
+------+---------+----------+-------------+
| Blog | 1.0.0   | Disabled | The Blog... |
+------+---------+----------+-------------+
```

### Enable Module

```bash
php artisan module:enable Blog
```

**Expected Output:**
```
Module: Blog
Description: The Blog module
Do you want to enable this module? (yes/no) [yes]: yes
Module 'Blog' enabled successfully.
```

**Verify:**
```bash
php artisan module:list

# Status should now be "Enabled"
```

### Disable Module

```bash
php artisan module:disable Blog
```

**Expected Output:**
```
Module 'Blog' disabled successfully.
```

---

## Test 2: Module Components Generation

### Create Controller

```bash
php artisan module:make-controller Blog PostController --resource
```

**Verify:**
```bash
cat modules/Blog/Http/Controllers/PostController.php

# Should see a resource controller with index, create, store, etc.
```

### Create Model

```bash
php artisan module:make-model Blog Post --migration
```

**Verify:**
```bash
# Check model
cat modules/Blog/Models/Post.php

# Check migration
ls modules/Blog/Database/migrations/
```

### Create Middleware

```bash
php artisan module:make-middleware Blog CheckBlogAccess
```

**Verify:**
```bash
cat modules/Blog/Http/Middleware/CheckBlogAccess.php
```

### Create Migration

```bash
php artisan module:make-migration Blog create_comments_table --create=comments
```

**Verify:**
```bash
ls modules/Blog/Database/migrations/
```

---

## Test 3: Dependency Management

### Create Modules with Dependencies

```bash
# Create base module
php artisan make:module Core --skip-confirmation

# Create dependent module
php artisan make:module Blog --skip-confirmation
```

### Edit Blog module.json to add dependency

```bash
# Edit modules/Blog/module.json
```

Add dependency:
```json
{
    "name": "Blog",
    "version": "1.0.0",
    "namespace": "Modules\\Blog",
    "providers": [
        "Modules\\Blog\\Providers\\BlogServiceProvider"
    ],
    "dependencies": {
        "Core": "^1.0"
    }
}
```

### Test Dependency Checking

```bash
# Try to enable Blog without Core
php artisan module:enable Blog
```

**Expected Error:**
```
Cannot enable module 'Blog'. Missing dependencies: Core
```

### Enable in Correct Order

```bash
# Enable Core first
php artisan module:enable Core

# Now enable Blog
php artisan module:enable Blog
# Should succeed
```

### Test Reverse Dependency

```bash
# Try to disable Core while Blog depends on it
php artisan module:disable Core
```

**Expected Error:**
```
Cannot disable module 'Core'. Required by: Blog
```

---

## Test 4: Version Constraints

### Create Modules with Version Constraints

Edit `modules/Blog/module.json`:
```json
{
    "dependencies": {
        "Core": "^1.0",
        "Auth": "~2.5",
        "Payment": ">=3.0"
    }
}
```

### Test Caret Constraint (^)

```bash
# Create Core with version 1.5.0
# Edit modules/Core/module.json
```

```json
{
    "name": "Core",
    "version": "1.5.0"
}
```

```bash
php artisan module:enable Core
php artisan module:enable Blog
# Should work (1.5.0 satisfies ^1.0)
```

### Test Major Version Mismatch

Edit `modules/Core/module.json`:
```json
{
    "version": "2.0.0"
}
```

```bash
php artisan module:disable Blog
php artisan module:disable Core
php artisan module:enable Core
php artisan module:enable Blog
```

**Expected Error:**
```
Cannot enable module 'Blog'. Missing dependencies: Core (^1.0)
```

---

## Test 5: Circular Dependency Detection

### Create Circular Dependencies

```bash
php artisan make:module ModuleA --skip-confirmation
php artisan make:module ModuleB --skip-confirmation
```

Edit `modules/ModuleA/module.json`:
```json
{
    "dependencies": {
        "ModuleB": "*"
    }
}
```

Edit `modules/ModuleB/module.json`:
```json
{
    "dependencies": {
        "ModuleA": "*"
    }
}
```

### Test Detection

```bash
php artisan module:enable ModuleA
```

**Expected Error:**
```
Cannot enable module 'ModuleA'. Circular dependency detected: ModuleA -> ModuleB -> ModuleA
```

---

## Test 6: Event System

### Create Event Listener

```bash
php artisan make:listener LogModuleEnabled
```

Edit `app/Listeners/LogModuleEnabled.php`:
```php
<?php

namespace App\Listeners;

use Monarul007\LaravelModularSystem\Events\ModuleEnabled;
use Illuminate\Support\Facades\Log;

class LogModuleEnabled
{
    public function handle(ModuleEnabled $event): void
    {
        Log::info("Module enabled: {$event->moduleName}", [
            'config' => $event->moduleConfig
        ]);
    }
}
```

### Register Listener

Edit `app/Providers/EventServiceProvider.php`:
```php
use Monarul007\LaravelModularSystem\Events\ModuleEnabled;
use App\Listeners\LogModuleEnabled;

protected $listen = [
    ModuleEnabled::class => [
        LogModuleEnabled::class,
    ],
];
```

### Test Event

```bash
php artisan module:enable Blog

# Check log
tail -f storage/logs/laravel.log

# Should see: "Module enabled: Blog"
```

### Test All Events

Create listeners for:
- `ModuleEnabling` - Before enable
- `ModuleEnabled` - After enable
- `ModuleDisabling` - Before disable
- `ModuleDisabled` - After disable
- `ModuleInstalling` - Before install
- `ModuleInstalled` - After install
- `ModuleUninstalling` - Before uninstall
- `ModuleUninstalled` - After uninstall

---

## Test 7: Feature Registry

### Register Features in Module Service Provider

Edit `modules/Blog/Providers/BlogServiceProvider.php`:
```php
use Monarul007\LaravelModularSystem\Facades\FeatureRegistry;

public function boot(): void
{
    // Register features
    FeatureRegistry::register('blog.comments', [
        'enabled' => true,
        'module' => 'Blog',
        'permissions' => ['user'],
    ]);

    FeatureRegistry::register('blog.likes', [
        'enabled' => true,
        'module' => 'Blog',
        'dependencies' => ['blog.comments'],
    ]);

    FeatureRegistry::register('blog.advanced-editor', [
        'enabled' => false,
        'module' => 'Blog',
        'permissions' => ['admin'],
    ]);
}
```

### Test Feature Checking

Create a test route in `modules/Blog/routes/web.php`:
```php
use Illuminate\Support\Facades\Route;
use Monarul007\LaravelModularSystem\Facades\FeatureRegistry;

Route::get('/blog/test-features', function () {
    return [
        'comments_enabled' => FeatureRegistry::isEnabled('blog.comments'),
        'likes_enabled' => FeatureRegistry::isEnabled('blog.likes'),
        'editor_enabled' => FeatureRegistry::isEnabled('blog.advanced-editor'),
        'all_features' => FeatureRegistry::getByModule('Blog'),
    ];
});
```

### Test Feature Dependencies

```bash
# Enable module
php artisan module:enable Blog

# Visit route
curl http://your-app.test/blog/test-features
```

**Expected Response:**
```json
{
    "comments_enabled": true,
    "likes_enabled": true,
    "editor_enabled": false,
    "all_features": {
        "blog.comments": {...},
        "blog.likes": {...},
        "blog.advanced-editor": {...}
    }
}
```

### Test Feature Dependency Chain

```php
// In tinker or a test route
php artisan tinker

>>> use Monarul007\LaravelModularSystem\Facades\FeatureRegistry;
>>> FeatureRegistry::disable('blog.comments');
>>> FeatureRegistry::isEnabled('blog.likes');
// Should return false (because it depends on comments)
```

---

## Test 8: Module Upload/Install

### Create a Module ZIP

```bash
# Create and enable a module
php artisan make:module Shop --skip-confirmation
php artisan module:enable Shop

# Add some content
php artisan module:make-controller Shop ProductController
php artisan module:make-model Shop Product

# Create ZIP (manually or via download)
cd modules
zip -r Shop.zip Shop/
```

### Test Upload via API

```bash
curl -X POST http://your-app.test/api/v1/admin/modules/upload \
  -H "Content-Type: multipart/form-data" \
  -F "module_zip=@Shop.zip"
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Module 'Shop' installed successfully",
    "module": {
        "name": "Shop",
        "version": "1.0.0",
        ...
    }
}
```

### Test Upload via Admin Panel

1. Visit `http://your-app.test/admin/modules`
2. Click "Upload Module"
3. Select ZIP file
4. Click "Upload"
5. Verify module appears in list

---

## Test 9: Module Routes and Views

### Add Routes to Module

Edit `modules/Blog/routes/web.php`:
```php
use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/posts/{id}', [PostController::class, 'show'])->name('show');
});
```

### Create View

Create `modules/Blog/resources/views/index.blade.php`:
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Blog</title>
</head>
<body>
    <h1>Blog Posts</h1>
    <p>This is from the Blog module!</p>
</body>
</html>
```

### Update Controller

Edit `modules/Blog/Http/Controllers/PostController.php`:
```php
public function index()
{
    return view('blog::index');
}
```

### Test Routes

```bash
# Enable module
php artisan module:enable Blog

# Check routes
php artisan route:list | grep blog

# Visit in browser
curl http://your-app.test/blog
```

**Expected:** HTML page with "Blog Posts" heading

---

## Test 10: Module Assets

### Create Assets

```bash
# Create JS file
mkdir -p modules/Blog/resources/js
echo "console.log('Blog module loaded');" > modules/Blog/resources/js/app.js

# Create CSS file
mkdir -p modules/Blog/resources/css
echo "body { background: #f0f0f0; }" > modules/Blog/resources/css/style.css
```

### Publish Assets

```bash
php artisan module:publish Blog
```

**Verify:**
```bash
ls public/modules/blog/js/
ls public/modules/blog/css/

# Should see app.js and style.css
```

### Use in Views

```blade
<link rel="stylesheet" href="{{ asset('modules/blog/css/style.css') }}">
<script src="{{ asset('modules/blog/js/app.js') }}"></script>
```

---

## Test 11: Module Configuration

### Create Config File

Create `modules/Blog/config/Blog.php`:
```php
<?php

return [
    'posts_per_page' => 10,
    'allow_comments' => true,
    'cache_ttl' => 3600,
];
```

### Use Configuration

```php
// In controller or anywhere
$postsPerPage = config('blog.posts_per_page');
$allowComments = config('blog.allow_comments');
```

### Test Config Merging

```bash
php artisan tinker

>>> config('blog.posts_per_page');
// Should return 10
```

---

## Test 12: Admin Panel (Inertia)

### Access Admin Panel

```bash
# Visit admin panel
http://your-app.test/admin
```

**Features to Test:**

1. **Dashboard**
   - View module statistics
   - See enabled/disabled counts

2. **Module Management**
   - Enable/disable modules
   - Upload new modules
   - Download modules
   - Uninstall modules

3. **Module Actions**
   - Click "Enable" on disabled module
   - Click "Disable" on enabled module
   - Click "Download" to export module
   - Click "Uninstall" (with confirmation)

---

## Test 13: API Endpoints

### Get All Modules

```bash
curl http://your-app.test/api/v1/admin/modules
```

**Expected Response:**
```json
{
    "success": true,
    "data": {
        "Blog": {
            "name": "Blog",
            "version": "1.0.0",
            "enabled": true,
            ...
        }
    }
}
```

### Enable Module

```bash
curl -X POST http://your-app.test/api/v1/admin/modules/enable \
  -H "Content-Type: application/json" \
  -d '{"name": "Blog"}'
```

### Disable Module

```bash
curl -X POST http://your-app.test/api/v1/admin/modules/disable \
  -H "Content-Type: application/json" \
  -d '{"name": "Blog"}'
```

### Download Module

```bash
curl http://your-app.test/api/v1/admin/modules/download/Blog \
  --output Blog.zip
```

---

## Test 14: Package Unit Tests

### Run Package Tests

```bash
# In package directory
cd packages/laravel-modular-system

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit tests/Unit/ModuleManagerTest.php

# Run with coverage (if xdebug installed)
vendor/bin/phpunit --coverage-html coverage
```

**Expected Output:**
```
PHPUnit 11.0.0

.........................                                         24 / 24 (100%)

Time: 00:01.234, Memory: 20.00 MB

OK (24 tests, 50 assertions)
```

---

## Test 15: Real-World Scenario

### Build a Complete Blog Module

```bash
# 1. Create module
php artisan make:module Blog --skip-confirmation

# 2. Create components
php artisan module:make-model Blog Post --migration
php artisan module:make-model Blog Comment --migration
php artisan module:make-controller Blog PostController --resource
php artisan module:make-controller Blog CommentController

# 3. Edit migrations
# Add fields to posts and comments tables

# 4. Create routes
# Edit modules/Blog/routes/web.php

# 5. Create views
# Create blade files in modules/Blog/resources/views/

# 6. Register features
# Edit BlogServiceProvider to register features

# 7. Enable module
php artisan module:enable Blog

# 8. Test everything
# Visit routes, test CRUD operations
```

---

## Troubleshooting

### Module Not Loading

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check if module is enabled
php artisan module:list

# Check enabled.json
cat modules/enabled.json
```

### Routes Not Working

```bash
# Check if module is enabled
php artisan module:list

# List all routes
php artisan route:list | grep your-module

# Check route file exists
cat modules/YourModule/routes/web.php
```

### Views Not Found

```bash
# Check view namespace
# Use: view('modulename::viewname')

# Check view file exists
ls modules/YourModule/resources/views/

# Clear view cache
php artisan view:clear
```

### Events Not Firing

```bash
# Check listener is registered
cat app/Providers/EventServiceProvider.php

# Clear cache
php artisan event:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log
```

---

## Performance Testing

### Test with Many Modules

```bash
# Create 50 modules
for i in {1..50}; do
    php artisan make:module "TestModule$i" --skip-confirmation
done

# Enable all
for i in {1..50}; do
    php artisan module:enable "TestModule$i"
done

# Check performance
time php artisan route:list
```

### Test Caching

```bash
# Enable cache
# Edit config/modular-system.php
'cache_enabled' => true,

# Test with cache
time php artisan module:list

# Disable cache
'cache_enabled' => false,

# Test without cache
time php artisan module:list
```

---

## Summary Checklist

- [ ] Module creation works
- [ ] Module enable/disable works
- [ ] Component generation works
- [ ] Dependencies are checked
- [ ] Version constraints work
- [ ] Circular dependencies detected
- [ ] Events are dispatched
- [ ] Feature registry works
- [ ] Module upload works
- [ ] Routes load correctly
- [ ] Views render correctly
- [ ] Assets publish correctly
- [ ] Config merges correctly
- [ ] Admin panel works
- [ ] API endpoints work
- [ ] Unit tests pass

---

**All features tested successfully? You're ready to use the package in production!**
