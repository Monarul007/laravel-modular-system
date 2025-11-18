# Laravel Modular System

A Laravel package that provides **WordPress-like plug-and-play functionality** for managing modules dynamically. Upload, enable, disable, and manage modules through a web interface without touching code.

## Features

- 🔌 **Module Upload**: Drag-and-drop ZIP files to install modules
- ⚡ **Hot-swappable**: Enable/disable modules without restart
- 🎛️ **Admin Panel**: Vue.js interface for module management
- 🔧 **Settings Manager**: Dynamic configuration system
- 📡 **API-First**: RESTful endpoints for SPA frontends
- 🛠️ **CLI Tools**: Artisan commands for developers
- 🗑️ **Module Removal**: Safely uninstall modules with confirmation
- 🎨 **View Resolution**: Automatic view namespace registration for modules
- 🏗️ **Component Generators**: Create controllers, models, migrations, and more
- 📦 **Asset Management**: Publish and manage module-specific assets
- ⚛️ **Inertia.js Support**: Full compatibility with Inertia.js for modern SPAs

## Installation

```bash
composer require monarul007/laravel-modular-system
```

## Setup

### 1. Publish Configuration & Assets

```bash
# Publish everything
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"

# Or publish individually
php artisan vendor:publish --tag=modular-config
php artisan vendor:publish --tag=modular-migrations
php artisan vendor:publish --tag=modular-routes
```

### 2. Run Migrations

```bash
php artisan migrate
```

### 3. Configure (Optional)

Edit `config/modular-system.php`:

```php
return [
    'modules_path' => base_path('modules'),
    'cache_enabled' => true,
    'cache_ttl' => 3600,
    'upload_max_size' => 2048, // KB
];
```

## Usage

### CLI Commands

#### Module Management

```bash
# List all modules
php artisan module:list

# Create new module (interactive with route alias setup)
php artisan make:module YourModule

# Create with custom alias
php artisan make:module YourModule --alias=custom-route

# Skip confirmation
php artisan make:module YourModule --skip-confirmation

# Enable module (interactive with route alias setup)
php artisan module:enable YourModule

# Enable with custom alias
php artisan module:enable YourModule --alias=custom-route

# Skip confirmation
php artisan module:enable YourModule --skip-confirmation

# Disable module
php artisan module:disable YourModule

# Remove/uninstall module (with confirmation)
php artisan module:remove YourModule

# Force remove (skip confirmation and delete assets)
php artisan module:remove YourModule --force

# Test module upload
php artisan test:module-upload module.zip
```

#### Component Generation

```bash
# Create controller
php artisan module:make-controller Blog PostController
php artisan module:make-controller Blog PostController --api
php artisan module:make-controller Blog PostController --resource

# Create model
php artisan module:make-model Blog Post
php artisan module:make-model Blog Post --migration

# Create migration
php artisan module:make-migration Blog create_posts_table --create=posts
php artisan module:make-migration Blog add_status_to_posts --table=posts

# Create middleware
php artisan module:make-middleware Blog CheckBlogAccess

# Create command
php artisan module:make-command Blog PublishPostCommand
```

#### Asset Management

```bash
# Publish module assets
php artisan module:publish Blog
php artisan module:publish --all

# Set or change module route alias
php artisan module:set-alias Blog
php artisan module:set-alias Blog new-alias
php artisan module:set-alias Blog custom-route --force
```



### API Endpoints

```bash
# Module Management
GET    /api/v1/admin/modules              # List modules
POST   /api/v1/admin/modules/upload       # Upload module ZIP
POST   /api/v1/admin/modules/enable       # Enable module
POST   /api/v1/admin/modules/disable      # Disable module
POST   /api/v1/admin/modules/uninstall    # Uninstall module
GET    /api/v1/admin/modules/download/{name}  # Download module

# Settings Management
GET    /api/v1/admin/settings/{group}    # Get settings group
POST   /api/v1/admin/settings/{group}    # Update settings
GET    /api/v1/admin/setting/{key}       # Get single setting
POST   /api/v1/admin/setting              # Set single setting
```

For detailed API documentation with request/response examples, see [API.md](API.md).

### Web Interface

```bash
# Admin Panel
http://your-app.test/admin/modules
```

### Programmatic Usage

```php
use Monarul007\LaravelModularSystem\Facades\ModuleManager;
use Monarul007\LaravelModularSystem\Facades\Settings;
use Monarul007\LaravelModularSystem\Facades\ModuleView;

// Module Management
$modules = ModuleManager::getAllModules();
ModuleManager::enableModule('YourModule');
ModuleManager::disableModule('YourModule');
ModuleManager::uninstallModule('YourModule');

// Settings Management
Settings::set('key', 'value', 'group');
$value = Settings::get('key', 'default');
$groupSettings = Settings::getGroup('general');

// View & Asset Management
$viewPath = ModuleView::view('Blog', 'index'); // Returns: 'blog::index'
$assetUrl = ModuleView::asset('Blog', 'app.js');
$exists = ModuleView::viewExists('Blog', 'custom-template');
ModuleView::publishAssets('Blog');
```

## Creating Modules

### 1. Generate Module

```bash
php artisan make:module YourModule
```

### 2. Module Structure

```
modules/YourModule/
├── module.json                      # Module configuration
├── Providers/ServiceProvider.php    # Laravel service provider
├── Console/Commands/                # Artisan commands
├── Http/
│   ├── Controllers/                 # Controllers
│   └── Middleware/                  # Middleware
├── Models/                          # Eloquent models
├── Database/migrations/             # Database migrations
├── routes/
│   ├── api.php                      # API routes
│   └── web.php                      # Web routes
├── resources/
│   ├── views/                       # Blade templates
│   └── js/                          # Frontend assets
├── config/YourModule.php            # Module settings
└── README.md                        # Module documentation
```

### 3. Module Configuration (module.json)

```json
{
    "name": "YourModule",
    "description": "Your module description",
    "version": "1.0.0",
    "namespace": "Modules\\YourModule",
    "providers": [
        "Modules\\YourModule\\Providers\\YourModuleServiceProvider"
    ],
    "dependencies": []
}
```

### 4. Using Module Views

In your module's controller:

```php
namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function index()
    {
        // Views are automatically namespaced with lowercase module name
        return view('blog::index');
    }
    
    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('blog::show', compact('post'));
    }
}
```

In your Blade templates:

```blade
{{-- Extend module layout --}}
@extends('blog::layouts.app')

{{-- Include module partials --}}
@include('blog::partials.header')

{{-- Use module assets --}}
<link rel="stylesheet" href="{{ asset('modules/blog/js/styles/blog.css') }}">
<script src="{{ asset('modules/blog/js/app.js') }}"></script>
```



### 5. Package & Distribute

```bash
zip -r YourModule.zip modules/YourModule/
```

## Configuration

### Module Path

By default, modules are stored in `base_path('modules')`. Change this in config:

```php
'modules_path' => base_path('custom-modules'),
```

### Cache Settings

```php
'cache_enabled' => true,
'cache_ttl' => 3600, // seconds
```

### Upload Limits

```php
'upload_max_size' => 2048, // KB
'allowed_extensions' => ['zip'],
```

## Security

- File validation (ZIP only)
- Module structure validation
- Automatic cleanup on failures
- Size limits on uploads

## Complete Example: Building a Blog Module

```bash
# 1. Create module
php artisan make:module Blog

# 2. Generate components
php artisan module:make-model Blog Post --migration
php artisan module:make-model Blog Category --migration
php artisan module:make-controller Blog PostController --resource
php artisan module:make-controller Blog Api/PostController --api
php artisan module:make-middleware Blog CheckPostOwnership

# 3. Enable and setup
php artisan module:enable Blog
php artisan migrate
php artisan module:publish Blog
```

**Controller Example** (`modules/Blog/Http/Controllers/PostController.php`):
```php
namespace Modules\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Blog\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('blog::index', compact('posts'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('blog::show', compact('post'));
    }
}
```

**View Example** (`modules/Blog/resources/views/index.blade.php`):
```blade
@extends('blog::layouts.app')

@section('content')
    <h1>Blog Posts</h1>
    @foreach($posts as $post)
        <article>
            <h2>{{ $post->title }}</h2>
            <p>{{ $post->excerpt }}</p>
            <a href="{{ route('blog.show', $post->slug) }}">Read More</a>
        </article>
    @endforeach
@endsection
```

**Routes Example** (`modules/Blog/routes/web.php`):
```php
use Illuminate\Support\Facades\Route;
use Modules\Blog\Http\Controllers\PostController;

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('index');
    Route::get('/{slug}', [PostController::class, 'show'])->name('show');
});
```

## Interactive Features

### Module Creation with Route Alias

When creating a module, you'll be prompted to:
1. Confirm module creation
2. Choose a route alias (suggested: kebab-case version of module name)
3. Enter custom alias or use suggested one

```bash
php artisan make:module BlogSystem

# Output:
# Creating new module: BlogSystem
# Do you want to create this module? (yes/no) [yes]: yes
#
# Route Alias Configuration
# Module: BlogSystem
# Suggested alias: blog-system
# Choose route alias option:
#   [use_suggested] Use suggested (blog-system)
#   [custom      ] Enter custom alias
#   [skip        ] Skip (use default)
```

### Module Enable with Confirmation

Enabling a module now shows module information and asks for confirmation:

```bash
php artisan module:enable Blog

# Output:
# Module: Blog
# Description: The Blog module
# Do you want to enable this module? (yes/no) [yes]:
```

### Module Removal with Asset Cleanup

Removing a module asks about published assets:

```bash
php artisan module:remove Blog

# Output:
# Are you sure you want to remove module 'Blog'? (yes/no) [no]: yes
# Removing module 'Blog'...
# Do you want to delete published assets for this module? (yes/no) [yes]:
```

### Change Module Route Alias

Update the route alias for an existing module:

```bash
php artisan module:set-alias Blog

# Output:
# Module: Blog
# Current alias: blog
#
# Route Alias Configuration
# Suggested alias: blog
# Choose route alias option:
#   [suggested] Use suggested (blog)
#   [custom   ] Enter custom alias
#   [remove   ] Remove alias (use default)
#   [cancel   ] Cancel
```

You can also specify the alias directly:

```bash
# Set specific alias
php artisan module:set-alias Blog blog-posts

# Force update without confirmation
php artisan module:set-alias Blog articles --force
```

## Advanced Features

### Using Settings

```php
use Monarul007\LaravelModularSystem\Facades\Settings;

// Set module settings
Settings::set('blog.posts_per_page', 15, 'blog');
Settings::set('blog.show_excerpt', true, 'blog');

// Get settings
$postsPerPage = Settings::get('blog.posts_per_page', 10);
$blogSettings = Settings::getGroup('blog');
```

### View Helpers

```php
use Monarul007\LaravelModularSystem\Facades\ModuleView;

// Check if view exists
if (ModuleView::viewExists('Blog', 'custom-template')) {
    return view('blog::custom-template');
}

// Get all module views
$views = ModuleView::getModuleViews('Blog');

// Get asset URL
$jsUrl = ModuleView::asset('Blog', 'app.js');
```

### Middleware Registration

In your module's service provider:
```php
use Illuminate\Routing\Router;

public function boot(): void
{
    $router = $this->app->make(Router::class);
    $router->aliasMiddleware('check-blog-access', 
        \Modules\Blog\Http\Middleware\CheckBlogAccess::class
    );
}
```

### Custom Commands

Register in your module's service provider:
```php
public function boot(): void
{
    if ($this->app->runningInConsole()) {
        $this->commands([
            \Modules\Blog\Console\Commands\PublishPostCommand::class,
        ]);
    }
}
```

## Troubleshooting

### Module Not Found
```bash
# Verify module.json exists
ls modules/YourModule/module.json

# Clear cache
php artisan cache:clear
```

### Views Not Loading
```bash
# Ensure module is enabled
php artisan module:enable YourModule

# Clear view cache
php artisan view:clear
```

### Assets Not Loading
```bash
# Publish assets
php artisan module:publish YourModule

# Check permissions
chmod -R 755 public/modules/
```

## API Reference

### ModuleManager Facade

```php
ModuleManager::getAllModules()              // Get all modules
ModuleManager::getEnabledModules()          // Get enabled modules
ModuleManager::enableModule($name)          // Enable a module
ModuleManager::disableModule($name)         // Disable a module
ModuleManager::uninstallModule($name)       // Uninstall a module
ModuleManager::moduleExists($name)          // Check if module exists
ModuleManager::isModuleEnabled($name)       // Check if enabled
ModuleManager::getModuleConfig($name)       // Get module config
```

### Settings Facade

```php
Settings::get($key, $default)               // Get setting
Settings::set($key, $value, $group)         // Set setting
Settings::getGroup($group)                  // Get all settings in group
Settings::has($key)                         // Check if setting exists
Settings::forget($key)                      // Delete setting
```

### ModuleView Facade

```php
ModuleView::view($module, $view)            // Get view path
ModuleView::asset($module, $asset)          // Get asset URL
ModuleView::viewExists($module, $view)      // Check if view exists
ModuleView::getModuleViews($module)         // Get all module views
ModuleView::publicPath($module)             // Get public path
ModuleView::publishAssets($module)          // Publish assets
```

## Command Options

### Module Creation Options

```bash
# Interactive with all prompts
php artisan make:module Blog

# Skip confirmation
php artisan make:module Blog --skip-confirmation

# Set custom alias
php artisan make:module Blog --alias=articles

# Both options
php artisan make:module Blog --skip-confirmation --alias=blog-posts
```

### Module Enable Options

```bash
# Interactive with confirmation
php artisan module:enable Blog

# Skip confirmation
php artisan module:enable Blog --skip-confirmation

# Set custom alias during enable
php artisan module:enable Blog --alias=blog-posts
```

### Module Remove Options

```bash
# Interactive with confirmation and asset cleanup prompt
php artisan module:remove Blog

# Force remove (skip all prompts, delete everything)
php artisan module:remove Blog --force
```

### Route Alias Management

```bash
# Interactive alias change
php artisan module:set-alias Blog

# Set specific alias
php artisan module:set-alias Blog articles

# Force update without prompts
php artisan module:set-alias Blog blog-posts --force
```

## Testing & Examples

For comprehensive testing workflows and real-world examples, see [TESTING-EXAMPLES.md](TESTING-EXAMPLES.md).

## Inertia.js Integration

Full Inertia.js support for building modern SPAs with your modules. See [INERTIA-GUIDE.md](INERTIA-GUIDE.md) for complete documentation.

**Quick Start:**

```php
// Return Inertia response from module
return module_inertia('Blog', 'Posts/Index', ['posts' => $posts]);

// Return Blade view with Inertia support
return module_view('blog', 'welcome', ['title' => 'Welcome']);
```

See [INERTIA-QUICK-REFERENCE.md](INERTIA-QUICK-REFERENCE.md) for quick reference.

## Architecture

For detailed system architecture, component interactions, and technical implementation details, see [ARCHITECTURE.md](ARCHITECTURE.md).

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history.

## License

MIT License

## Support

For issues, questions, or contributions, please visit the GitHub repository.

## Credits

Created for Laravel applications requiring dynamic module management.
