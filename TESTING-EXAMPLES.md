# Testing Examples & Workflows

This document provides real-world testing examples and workflows for the Laravel Modular System package.

## Table of Contents

- [Basic Module Creation Workflow](#basic-module-creation-workflow)
- [Blog Module Example](#blog-module-example)
- [E-commerce Shop Module](#e-commerce-shop-module)
- [API-Only Module](#api-only-module)
- [Testing View Resolution](#testing-view-resolution)
- [Testing Asset Publishing](#testing-asset-publishing)
- [Testing Module Removal](#testing-module-removal)

---

## Basic Module Creation Workflow

### Step 1: Create Module

```bash
php artisan make:module TestBlog
```

**Expected Output:**
```
Module 'TestBlog' created successfully.
```

**Verify Structure:**
```bash
ls modules/TestBlog
```

### Step 2: Generate Components

```bash
# Create model with migration
php artisan module:make-model TestBlog Post --migration

# Create resource controller
php artisan module:make-controller TestBlog PostController --resource

# Create middleware
php artisan module:make-middleware TestBlog CheckPostAccess

# Create command
php artisan module:make-command TestBlog PublishPostsCommand
```

### Step 3: Enable Module

```bash
php artisan module:enable TestBlog
```

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Publish Assets

```bash
php artisan module:publish TestBlog
```

### Step 6: Test Routes

```bash
php artisan route:list --name=testblog
```

---

## Blog Module Example

### Complete Blog Module Setup

```bash
# 1. Create module
php artisan make:module Blog

# 2. Create models
php artisan module:make-model Blog Post --migration
php artisan module:make-model Blog Category --migration
php artisan module:make-model Blog Comment --migration

# 3. Create controllers
php artisan module:make-controller Blog PostController --resource
php artisan module:make-controller Blog CategoryController --resource
php artisan module:make-controller Blog Api/PostController --api
php artisan module:make-controller Blog Api/CategoryController --api

# 4. Create middleware
php artisan module:make-middleware Blog CheckPostOwnership
php artisan module:make-middleware Blog CheckPublished

# 5. Create migrations
php artisan module:make-migration Blog add_featured_to_posts --table=posts
php artisan module:make-migration Blog add_slug_to_categories --table=categories

# 6. Create commands
php artisan module:make-command Blog PublishScheduledPostsCommand
php artisan module:make-command Blog GenerateSitemapCommand
```

### Create Views

```bash
# Create view structure
mkdir -p modules/Blog/resources/views/{layouts,pages,components,partials}
```

**modules/Blog/resources/views/layouts/app.blade.php:**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') - Blog</title>
    <link rel="stylesheet" href="{{ asset('modules/blog/js/styles/blog.css') }}">
</head>
<body>
    @include('blog::partials.navigation')
    
    <main>
        @yield('content')
    </main>
    
    @include('blog::partials.footer')
    <script src="{{ asset('modules/blog/js/app.js') }}"></script>
</body>
</html>
```

**modules/Blog/resources/views/pages/index.blade.php:**
```blade
@extends('blog::layouts.app')

@section('title', 'All Posts')

@section('content')
    <h1>Blog Posts</h1>
    @foreach($posts as $post)
        @include('blog::components.post-card', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
@endsection
```

### Update Controller

**modules/Blog/Http/Controllers/PostController.php:**
```php
<?php

namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Blog\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('category', 'author')
                    ->published()
                    ->latest()
                    ->paginate(10);
        
        return view('blog::pages.index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
                   ->published()
                   ->firstOrFail();
        
        return view('blog::pages.show', compact('post'));
    }
}
```

### Define Routes

**modules/Blog/routes/web.php:**
```php
<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;
use Modules\Blog\Http\Controllers\CategoryController;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
});
```

### Enable and Test

```bash
# Enable module
php artisan module:enable Blog

# Run migrations
php artisan migrate

# Publish assets
php artisan module:publish Blog

# Test routes
php artisan route:list --name=blog

# Visit in browser
# http://your-app.test/blog
```

---

## E-commerce Shop Module

### Setup Shop Module

```bash
# Create module
php artisan make:module Shop

# Create models
php artisan module:make-model Shop Product --migration
php artisan module:make-model Shop Category --migration
php artisan module:make-model Shop Order --migration
php artisan module:make-model Shop OrderItem --migration
php artisan module:make-model Shop Cart --migration

# Create controllers
php artisan module:make-controller Shop ProductController --resource
php artisan module:make-controller Shop CartController
php artisan module:make-controller Shop CheckoutController
php artisan module:make-controller Shop OrderController --resource

# Create API controllers
php artisan module:make-controller Shop Api/ProductController --api
php artisan module:make-controller Shop Api/CartController --api
php artisan module:make-controller Shop Api/OrderController --api

# Create middleware
php artisan module:make-middleware Shop CheckCartNotEmpty
php artisan module:make-middleware Shop CheckProductAvailability

# Create commands
php artisan module:make-command Shop ProcessPendingOrdersCommand
php artisan module:make-command Shop UpdateInventoryCommand
```

### Test Product Model

**modules/Shop/Models/Product.php:**
```php
<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'category_id',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
```

### Enable and Test

```bash
php artisan module:enable Shop
php artisan migrate
php artisan module:publish Shop
```

---

## API-Only Module

### Create API Module

```bash
# Create module
php artisan make:module ApiService

# Create API controllers only
php artisan module:make-controller ApiService Api/UserController --api
php artisan module:make-controller ApiService Api/AuthController --api
php artisan module:make-controller ApiService Api/DataController --api

# Create models
php artisan module:make-model ApiService ApiKey --migration
php artisan module:make-model ApiService ApiLog --migration

# Create middleware
php artisan module:make-middleware ApiService ValidateApiKey
php artisan module:make-middleware ApiService RateLimitApi
```

### API Controller Example

**modules/ApiService/Http/Controllers/Api/UserController.php:**
```php
<?php

namespace Modules\ApiService\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
```

---

## Testing View Resolution

### Create Test Views

```bash
# Create view files
mkdir -p modules/TestBlog/resources/views/{layouts,pages,components,partials}
```

**modules/TestBlog/resources/views/pages/index.blade.php:**
```blade
<!DOCTYPE html>
<html>
<head>
    <title>Test Blog</title>
</head>
<body>
    <h1>Blog Posts</h1>
    <p>View namespace: testblog::pages.index</p>
</body>
</html>
```

### Test in Controller

**modules/TestBlog/Http/Controllers/PostController.php:**
```php
public function index()
{
    // Test view resolution
    return view('testblog::pages.index');
}

public function nested()
{
    // Test nested views
    return view('testblog::components.post-card');
}
```

### Test View Helpers

```php
use Monarul007\LaravelModularSystem\Facades\ModuleView;

// Check if view exists
if (ModuleView::viewExists('TestBlog', 'pages.index')) {
    echo "View exists!";
}

// Get all module views
$views = ModuleView::getModuleViews('TestBlog');
print_r($views);

// Get view path
$viewPath = ModuleView::view('TestBlog', 'pages.index');
echo $viewPath; // Output: testblog::pages.index
```

---

## Testing Asset Publishing

### Create Assets

**modules/TestBlog/resources/js/app.js:**
```javascript
console.log('TestBlog module loaded!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('TestBlog initialized');
});
```

**modules/TestBlog/resources/js/styles/blog.css:**
```css
.blog-post {
    padding: 20px;
    margin: 10px 0;
    border: 1px solid #ddd;
}

.blog-title {
    font-size: 24px;
    font-weight: bold;
}
```

### Publish Assets

```bash
# Publish specific module
php artisan module:publish TestBlog

# Verify published files
ls public/modules/testblog/js/
```

### Use Published Assets

```blade
{{-- In your view --}}
<link rel="stylesheet" href="{{ asset('modules/testblog/js/styles/blog.css') }}">
<script src="{{ asset('modules/testblog/js/app.js') }}"></script>

{{-- Or using helper --}}
<script src="{{ ModuleView::asset('TestBlog', 'app.js') }}"></script>
```

### Publish All Modules

```bash
php artisan module:publish --all
```

---

## Testing Module Removal

### Remove Module with Confirmation

```bash
# Will ask for confirmation
php artisan module:remove TestBlog
```

**Expected Prompt:**
```
Are you sure you want to remove module 'TestBlog'? This will delete all module files. (yes/no) [no]:
```

### Force Remove (Skip Confirmation)

```bash
php artisan module:remove TestBlog --force
```

### Verify Removal

```bash
# Check module list
php artisan module:list

# Verify directory is gone
ls modules/TestBlog  # Should not exist

# Check routes are removed
php artisan route:list --name=testblog  # Should show no routes
```

---

## Complete Testing Workflow

### Full Test Cycle

```bash
# 1. Create module
php artisan make:module TestBlog

# 2. Generate all components
php artisan module:make-model TestBlog Post --migration
php artisan module:make-controller TestBlog PostController --resource
php artisan module:make-controller TestBlog Api/PostController --api
php artisan module:make-middleware TestBlog CheckPostAccess
php artisan module:make-command TestBlog PublishPostsCommand
php artisan module:make-migration TestBlog add_status_to_posts --table=posts

# 3. Create views
echo '<!DOCTYPE html><html><body><h1>Test</h1></body></html>' > modules/TestBlog/resources/views/index.blade.php

# 4. Create assets
echo 'console.log("TestBlog loaded");' > modules/TestBlog/resources/js/app.js

# 5. Update controller to use view
# Edit modules/TestBlog/Http/Controllers/PostController.php
# Add: return view('testblog::index');

# 6. Add route
# Edit modules/TestBlog/routes/web.php
# Add route to controller

# 7. Enable module
php artisan module:enable TestBlog

# 8. Run migrations
php artisan migrate

# 9. Publish assets
php artisan module:publish TestBlog

# 10. Test routes
php artisan route:list --name=testblog

# 11. List modules
php artisan module:list

# 12. Test in browser
# Visit: http://your-app.test/testblog

# 13. Disable module
php artisan module:disable TestBlog

# 14. Re-enable
php artisan module:enable TestBlog

# 15. Remove module
php artisan module:remove TestBlog --force

# 16. Verify removal
php artisan module:list
```

---

## Troubleshooting Tests

### Test 1: Module Not Found

```bash
# Create module
php artisan make:module TestModule

# Verify it exists
ls modules/TestModule/module.json

# If not found, check config
cat config/modular-system.php
```

### Test 2: Views Not Loading

```bash
# Enable module
php artisan module:enable TestModule

# Clear caches
php artisan view:clear
php artisan cache:clear

# Check view exists
ls modules/TestModule/resources/views/index.blade.php

# Test view resolution
php artisan tinker
>>> view()->exists('testmodule::index');
```

### Test 3: Assets Not Publishing

```bash
# Publish assets
php artisan module:publish TestModule

# Check source exists
ls modules/TestModule/resources/js/

# Check destination
ls public/modules/testmodule/js/

# Check permissions
chmod -R 755 public/modules/
```

### Test 4: Routes Not Working

```bash
# List all routes
php artisan route:list

# Clear route cache
php artisan route:clear

# Check module is enabled
php artisan module:list

# Verify route file exists
cat modules/TestModule/routes/web.php
```

---

## Performance Testing

### Test Module Loading Time

```bash
# Time module enable
time php artisan module:enable TestModule

# Time asset publishing
time php artisan module:publish TestModule

# Time module removal
time php artisan module:remove TestModule --force
```

### Test Multiple Modules

```bash
# Create multiple modules
for i in {1..5}; do
    php artisan make:module TestModule$i
done

# Enable all
for i in {1..5}; do
    php artisan module:enable TestModule$i
done

# Publish all assets
time php artisan module:publish --all

# Remove all
for i in {1..5}; do
    php artisan module:remove TestModule$i --force
done
```

---

## Automated Testing Script

Create a test script `test-modules.sh`:

```bash
#!/bin/bash

echo "Starting module system tests..."

# Test 1: Create module
echo "Test 1: Creating module..."
php artisan make:module TestModule
if [ $? -eq 0 ]; then
    echo "✓ Module created"
else
    echo "✗ Module creation failed"
    exit 1
fi

# Test 2: Generate components
echo "Test 2: Generating components..."
php artisan module:make-controller TestModule TestController
php artisan module:make-model TestModule TestModel --migration
echo "✓ Components generated"

# Test 3: Enable module
echo "Test 3: Enabling module..."
php artisan module:enable TestModule
echo "✓ Module enabled"

# Test 4: Publish assets
echo "Test 4: Publishing assets..."
echo 'console.log("test");' > modules/TestModule/resources/js/app.js
php artisan module:publish TestModule
echo "✓ Assets published"

# Test 5: Remove module
echo "Test 5: Removing module..."
php artisan module:remove TestModule --force
echo "✓ Module removed"

echo "All tests passed! ✓"
```

Run the script:
```bash
chmod +x test-modules.sh
./test-modules.sh
```

---

## Conclusion

These testing examples demonstrate all features of the Laravel Modular System:

- ✅ Module creation and structure
- ✅ Component generation (controllers, models, migrations, etc.)
- ✅ View resolution and namespacing
- ✅ Asset management and publishing
- ✅ Module lifecycle (enable, disable, remove)
- ✅ Route registration
- ✅ API development
- ✅ Troubleshooting common issues

Use these examples as templates for your own module development and testing workflows.
