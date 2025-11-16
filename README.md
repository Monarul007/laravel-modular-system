# Laravel Modular System

A Laravel package that provides **WordPress-like plug-and-play functionality** for managing modules dynamically. Upload, enable, disable, and manage modules through a web interface without touching code.

## Features

- 🔌 **Module Upload**: Drag-and-drop ZIP files to install modules
- ⚡ **Hot-swappable**: Enable/disable modules without restart
- 🎛️ **Admin Panel**: Vue.js interface for module management
- 🔧 **Settings Manager**: Dynamic configuration system
- 📡 **API-First**: RESTful endpoints for SPA frontends
- 🛠️ **CLI Tools**: Artisan commands for developers

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

```bash
# List all modules
php artisan module:list

# Create new module
php artisan make:module YourModule

# Enable module
php artisan module:enable YourModule

# Disable module
php artisan module:disable YourModule

# Test module upload
php artisan test:module-upload module.zip
```

### API Endpoints

```bash
# Module Management
GET    /api/v1/admin/modules           # List modules
POST   /api/v1/admin/modules/enable    # Enable module
POST   /api/v1/admin/modules/disable   # Disable module

# Settings Management
GET    /api/v1/admin/settings/{group}  # Get settings group
POST   /api/v1/admin/settings/{group}  # Update settings
```

### Web Interface

```bash
# Admin Panel
http://your-app.test/admin/modules
```

### Programmatic Usage

```php
use Monarul007\LaravelModularSystem\Facades\ModuleManager;
use Monarul007\LaravelModularSystem\Facades\Settings;

// Module Management
$modules = ModuleManager::getAllModules();
ModuleManager::enableModule('YourModule');
ModuleManager::disableModule('YourModule');

// Settings Management
Settings::set('key', 'value', 'group');
$value = Settings::get('key', 'default');
$groupSettings = Settings::getGroup('general');
```

## Creating Modules

### 1. Generate Module

```bash
php artisan make:module YourModule
```

### 2. Module Structure

```
modules/YourModule/
├── module.json                 # Module configuration
├── Providers/ServiceProvider   # Laravel service provider
├── Http/Controllers/           # API controllers
├── routes/api.php             # API routes
├── routes/web.php             # Web routes
└── config/YourModule.php      # Module settings
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

### 4. Package & Distribute

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

## Testing

```bash
composer test
```

## License

MIT License

## Credits

Created for Laravel applications requiring dynamic module management.
