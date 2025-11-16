# Quick Start Guide

Get up and running with Laravel Modular System in 5 minutes.

## Installation

```bash
composer require monarul007/laravel-modular-system
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"
php artisan migrate
```

## Create Your First Module

```bash
php artisan make:module Blog
```

This creates a complete module structure in `modules/Blog/`.

## Enable the Module

```bash
php artisan module:enable Blog
```

## Add a Controller

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
        $posts = [
            ['id' => 1, 'title' => 'First Post'],
            ['id' => 2, 'title' => 'Second Post'],
        ];
        
        return ApiResponse::success($posts, 'Posts retrieved successfully');
    }
}
```

## Add Routes

Edit `modules/Blog/routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('api/v1/blog')->group(function () {
    Route::get('posts', [PostController::class, 'index']);
});
```

## Test It

```bash
php artisan serve
```

Visit: `http://localhost:8000/api/v1/blog/posts`

Response:
```json
{
    "success": true,
    "message": "Posts retrieved successfully",
    "data": [
        {"id": 1, "title": "First Post"},
        {"id": 2, "title": "Second Post"}
    ]
}
```

## Using Facades

```php
use Monarul007\LaravelModularSystem\Facades\ModuleManager;
use Monarul007\LaravelModularSystem\Facades\Settings;

// Module Management
$modules = ModuleManager::getAllModules();
ModuleManager::enableModule('Blog');
ModuleManager::disableModule('Blog');

// Settings Management
Settings::set('site_name', 'My Blog');
$siteName = Settings::get('site_name');
```

## CLI Commands

```bash
# List all modules
php artisan module:list

# Create module
php artisan make:module Shop

# Enable module
php artisan module:enable Shop

# Disable module
php artisan module:disable Shop

# Test ZIP upload
php artisan test:module-upload module.zip
```

## API Endpoints

### List Modules
```bash
curl http://localhost:8000/api/v1/admin/modules
```

### Enable Module
```bash
curl -X POST http://localhost:8000/api/v1/admin/modules/enable \
  -H "Content-Type: application/json" \
  -d '{"name": "Blog"}'
```

### Upload Module
```bash
curl -X POST http://localhost:8000/api/v1/admin/modules/upload \
  -F "file=@module.zip"
```

## Next Steps

- Read [USAGE.md](USAGE.md) for detailed examples
- Check [API.md](API.md) for complete API documentation
- See [PACKAGE-SUMMARY.md](PACKAGE-SUMMARY.md) for architecture details

## Common Tasks

### Add Database Migration

Create `modules/Blog/Database/migrations/2024_01_01_000000_create_posts_table.php`:

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

Run migrations:
```bash
php artisan migrate
```

### Add Configuration

Edit `modules/Blog/config/Blog.php`:

```php
<?php

return [
    'posts_per_page' => 10,
    'allow_comments' => true,
];
```

Access in code:
```php
$perPage = config('Blog.posts_per_page');
```

### Package Module for Distribution

```bash
# Create ZIP
cd modules
zip -r Blog.zip Blog/

# Others can install via:
# - Upload through admin panel
# - php artisan test:module-upload Blog.zip
```

## Troubleshooting

### Module not loading?
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

### Routes not working?
```bash
php artisan module:list  # Check if enabled
php artisan route:list   # Verify routes registered
```

### Permission errors?
```bash
chmod -R 755 modules
chmod -R 755 storage
```

## Support

- Documentation: See all `.md` files in package
- Issues: GitHub repository
- Examples: Check `modules/` directory in your app

That's it! You now have a working modular system. 🎉
