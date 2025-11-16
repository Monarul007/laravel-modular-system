# Usage Guide

## Quick Start

### 1. Create Your First Module

```bash
php artisan make:module Blog
```

This creates:
```
modules/Blog/
├── module.json
├── Providers/BlogServiceProvider.php
├── Http/Controllers/
├── routes/api.php
├── routes/web.php
├── config/Blog.php
└── README.md
```

### 2. Enable the Module

```bash
php artisan module:enable Blog
```

### 3. Add Routes

Edit `modules/Blog/routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('api/v1/blog')->group(function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);
});
```

### 4. Create Controller

Create `modules/Blog/Http/Controllers/PostController.php`:

```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\ApiResponse;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(['posts' => []], 'Posts retrieved');
    }

    public function store(): JsonResponse
    {
        return ApiResponse::success(null, 'Post created');
    }
}
```

## Using Facades

### ModuleManager Facade

```php
use Monarul007\LaravelModularSystem\Facades\ModuleManager;

// Get all modules
$modules = ModuleManager::getAllModules();

// Check if module exists
if (ModuleManager::moduleExists('Blog')) {
    // Module exists
}

// Check if module is enabled
if (ModuleManager::isModuleEnabled('Blog')) {
    // Module is enabled
}

// Enable module
ModuleManager::enableModule('Blog');

// Disable module
ModuleManager::disableModule('Blog');

// Get module configuration
$config = ModuleManager::getModuleConfig('Blog');

// Install from ZIP
$result = ModuleManager::installModuleFromZip('/path/to/module.zip');

// Uninstall module
ModuleManager::uninstallModule('Blog');

// Create ZIP
$zipPath = ModuleManager::createModuleZip('Blog');
```

### Settings Facade

```php
use Monarul007\LaravelModularSystem\Facades\Settings;

// Get setting
$siteName = Settings::get('site_name', 'Default Name');

// Set setting
Settings::set('site_name', 'My Awesome Site', 'general');

// Get group
$generalSettings = Settings::getGroup('general');

// Delete setting
Settings::forget('old_setting');
```

## Module Structure

### module.json

```json
{
    "name": "Blog",
    "description": "A blog module",
    "version": "1.0.0",
    "namespace": "Modules\\Blog",
    "providers": [
        "Modules\\Blog\\Providers\\BlogServiceProvider"
    ],
    "dependencies": []
}
```

### Service Provider

```php
<?php

namespace Modules\Blog\Providers;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/Blog.php',
            'Blog'
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'Blog');
        
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');
        }
    }
}
```

## Advanced Usage

### Module Dependencies

In `module.json`:

```json
{
    "name": "BlogComments",
    "dependencies": ["Blog"]
}
```

### Module Configuration

Create `modules/Blog/config/Blog.php`:

```php
<?php

return [
    'posts_per_page' => 10,
    'allow_comments' => true,
    'cache_enabled' => true,
];
```

Access in code:

```php
$postsPerPage = config('Blog.posts_per_page');
```

### Module Migrations

Create migrations in `modules/Blog/Database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
```

### Module Views

Create views in `modules/Blog/resources/views/`:

```blade
<!-- modules/Blog/resources/views/post.blade.php -->
<div class="post">
    <h1>{{ $title }}</h1>
    <p>{{ $content }}</p>
</div>
```

Access in controller:

```php
return view('Blog::post', ['title' => 'Hello', 'content' => 'World']);
```

## CLI Commands

### List Modules

```bash
php artisan module:list
```

### Create Module

```bash
php artisan make:module YourModule
```

### Enable Module

```bash
php artisan module:enable YourModule
```

### Disable Module

```bash
php artisan module:disable YourModule
```

### Test Upload

```bash
php artisan test:module-upload /path/to/module.zip
```

## API Usage

### Using cURL

```bash
# List modules
curl http://localhost:8000/api/v1/admin/modules

# Enable module
curl -X POST http://localhost:8000/api/v1/admin/modules/enable \
  -H "Content-Type: application/json" \
  -d '{"name": "Blog"}'

# Upload module
curl -X POST http://localhost:8000/api/v1/admin/modules/upload \
  -F "file=@/path/to/module.zip"
```

### Using JavaScript

```javascript
// List modules
fetch('/api/v1/admin/modules')
  .then(res => res.json())
  .then(data => console.log(data));

// Enable module
fetch('/api/v1/admin/modules/enable', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ name: 'Blog' })
})
  .then(res => res.json())
  .then(data => console.log(data));
```

## Best Practices

1. **Namespace**: Always use proper namespacing for modules
2. **Dependencies**: Document module dependencies in module.json
3. **Versioning**: Use semantic versioning (1.0.0)
4. **Testing**: Test modules before distribution
5. **Documentation**: Include README.md in each module
6. **Security**: Validate inputs in controllers
7. **Performance**: Use caching for heavy operations
8. **Cleanup**: Implement proper uninstall procedures

## Troubleshooting

### Module not loading

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Routes not working

Check if module is enabled:
```bash
php artisan module:list
```

### Permission issues

```bash
chmod -R 755 modules
```
