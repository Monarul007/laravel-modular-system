# Migrating Existing Modules to Inertia

This guide helps you migrate existing modules to use Inertia.js support.

## Quick Migration Checklist

- [ ] Update controllers to use new helpers or base class
- [ ] Fix Blade views using `@inertiaHead`
- [ ] Create Vue/React components for Inertia pages
- [ ] Update Vite configuration
- [ ] Test all routes

## Migration Scenarios

### Scenario 1: Blade Views with @inertiaHead Errors

**Before (Broken):**
```php
public function welcome()
{
    return view('blog::welcome', [
        'title' => 'Welcome'
    ]);
}
```

**After (Fixed):**
```php
public function welcome()
{
    return module_view('blog', 'welcome', [
        'title' => 'Welcome'
    ]);
}
```

**Why it works:** `module_view()` automatically provides the `$page` variable needed by `@inertiaHead`.

### Scenario 2: Adding Inertia Responses

**Before (Blade only):**
```php
class PostController extends Controller
{
    public function index()
    {
        return view('blog::posts.index', [
            'posts' => Post::all()
        ]);
    }
}
```

**After (Inertia):**
```php
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

### Scenario 3: Mixed Blade and Inertia

**Use Case:** Some pages use Blade, others use Inertia.

```php
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class BlogController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    // Inertia page
    public function dashboard()
    {
        return $this->inertia('Dashboard', [
            'stats' => $this->getStats()
        ]);
    }
    
    // Blade page with Inertia support
    public function about()
    {
        return $this->moduleView('about', [
            'content' => $this->getAboutContent()
        ]);
    }
    
    // Traditional Blade (no Inertia directives)
    public function legacy()
    {
        return view('blog::legacy');
    }
}
```

## Step-by-Step Migration

### Step 1: Update Controllers

Choose your approach:

**Option A: Extend ModuleInertiaController (Recommended)**
```php
// Before
class MyController extends Controller
{
    // ...
}

// After
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class MyController extends ModuleInertiaController
{
    protected string $moduleName = 'MyModule';
    
    // Use $this->inertia() and $this->moduleView()
}
```

**Option B: Use Helper Functions**
```php
// Keep existing controller, just change return statements
class MyController extends Controller
{
    public function index()
    {
        // Before: return view('mymodule::index');
        return module_view('mymodule', 'index');
    }
    
    public function dashboard()
    {
        return module_inertia('MyModule', 'Dashboard', $data);
    }
}
```

### Step 2: Fix Blade Views

**Find views using @inertiaHead:**
```bash
grep -r "@inertiaHead" modules/YourModule/resources/views/
```

**Update controller methods returning those views:**
```php
// Before
return view('mymodule::page');

// After
return module_view('mymodule', 'page');
```

### Step 3: Create Vue Components

For new Inertia pages, create Vue components:

```
modules/YourModule/resources/js/Pages/
├── Dashboard.vue
├── Posts/
│   ├── Index.vue
│   ├── Show.vue
│   └── Edit.vue
└── Settings.vue
```

**Example component:**
```vue
<template>
    <div>
        <h1>{{ title }}</h1>
        <!-- Your content -->
    </div>
</template>

<script setup>
defineProps({
    title: String,
    // other props
});
</script>
```

### Step 4: Update Routes

No changes needed! Routes work the same way:

```php
// modules/YourModule/routes/web.php
Route::get('/dashboard', [DashboardController::class, 'index']);
```

### Step 5: Update Vite Config

Add module components to Vite:

```javascript
// vite.config.js
import { glob } from 'glob';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',
                // Add this line
                ...glob.sync('modules/*/resources/js/Pages/**/*.vue'),
            ],
            refresh: true,
        }),
        vue(),
    ],
});
```

Install glob:
```bash
npm install glob
```

### Step 6: Build Assets

```bash
npm run build
# or for development
npm run dev
```

### Step 7: Test

Test each route:
- [ ] Inertia pages render
- [ ] Blade views don't have $page errors
- [ ] Props are passed correctly
- [ ] Navigation works

## Common Patterns

### Pattern 1: API + Inertia

```php
class PostController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    // Inertia page
    public function index()
    {
        return $this->inertia('Posts/Index', [
            'posts' => Post::paginate()
        ]);
    }
    
    // API endpoint
    public function store(Request $request)
    {
        $post = Post::create($request->validated());
        
        return response()->json($post, 201);
    }
}
```

### Pattern 2: Conditional Response

```php
public function show($id)
{
    $post = Post::findOrFail($id);
    
    // Return Inertia for web, JSON for API
    if (request()->wantsJson()) {
        return response()->json($post);
    }
    
    return $this->inertia('Posts/Show', [
        'post' => $post
    ]);
}
```

### Pattern 3: Shared Data

```php
class BlogController extends ModuleInertiaController
{
    protected string $moduleName = 'Blog';
    
    public function __construct()
    {
        // Share data with all Inertia responses
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

The new helpers are **fully backward compatible**:

✅ Existing Blade views still work  
✅ Existing controllers still work  
✅ No breaking changes  
✅ Migrate at your own pace  

You can migrate one controller at a time, or even one method at a time.

## Testing Your Migration

### Test Checklist

```bash
# 1. Check module is enabled
php artisan module:list

# 2. Check routes are registered
php artisan route:list | grep your-module

# 3. Build assets
npm run build

# 4. Start server
php artisan serve

# 5. Test each route manually
# Visit each URL and check:
# - Page loads without errors
# - No $page undefined errors
# - Props are passed correctly
# - Navigation works
```

### Automated Testing

```php
// tests/Feature/ModuleInertiaTest.php
public function test_inertia_page_renders()
{
    $response = $this->get('/blog/dashboard');
    
    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->component('Blog/Dashboard')
            ->has('stats')
    );
}

public function test_blade_view_with_inertia_head()
{
    $response = $this->get('/blog/about');
    
    $response->assertStatus(200);
    $response->assertViewIs('blog::about');
    $response->assertViewHas('page'); // $page variable exists
}
```

## Rollback Plan

If you need to rollback:

1. **Revert controller changes:**
   ```php
   // Change back to
   return view('module::view');
   ```

2. **Remove Vue components** (optional)

3. **Revert Vite config** (optional)

The package still supports traditional Blade views, so rollback is easy.

## Performance Considerations

### Before Migration
- Traditional page loads
- Full page refresh on navigation

### After Migration
- Faster navigation (SPA)
- Reduced server load
- Better UX

### Build Time
- Initial build may be slower (more components)
- Use `npm run dev` during development
- Use `npm run build` for production

## Best Practices

1. **Migrate incrementally**: Start with one module or one controller
2. **Test thoroughly**: Test each route after migration
3. **Use TypeScript**: Consider adding TypeScript for better type safety
4. **Share common data**: Use Inertia's share() for data needed on all pages
5. **Handle errors**: Add error handling for failed requests
6. **Optimize builds**: Use code splitting for large applications

## Getting Help

If you encounter issues:

1. Check [INERTIA-GUIDE.md](INERTIA-GUIDE.md) troubleshooting section
2. Review [INERTIA-QUICK-REFERENCE.md](INERTIA-QUICK-REFERENCE.md)
3. Check the example in `examples/TestModule-Inertia/`
4. Verify Inertia is properly installed in main app

## Example: Complete Migration

**Before:**
```php
// modules/Shop/Http/Controllers/ProductController.php
class ProductController extends Controller
{
    public function index()
    {
        return view('shop::products.index', [
            'products' => Product::all()
        ]);
    }
    
    public function show($id)
    {
        return view('shop::products.show', [
            'product' => Product::findOrFail($id)
        ]);
    }
}
```

**After:**
```php
// modules/Shop/Http/Controllers/ProductController.php
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleInertiaController;

class ProductController extends ModuleInertiaController
{
    protected string $moduleName = 'Shop';
    
    public function index()
    {
        return $this->inertia('Products/Index', [
            'products' => Product::all()
        ]);
    }
    
    public function show($id)
    {
        return $this->inertia('Products/Show', [
            'product' => Product::findOrFail($id)
        ]);
    }
}
```

**New Vue components:**
- `modules/Shop/resources/js/Pages/Products/Index.vue`
- `modules/Shop/resources/js/Pages/Products/Show.vue`

**Result:**
- Faster navigation between products
- Better UX
- Modern SPA experience
- No page refreshes

## Summary

✅ Migration is straightforward  
✅ Backward compatible  
✅ Can be done incrementally  
✅ Improves UX significantly  
✅ Well documented and supported  

Start with one module, test thoroughly, then migrate others!
