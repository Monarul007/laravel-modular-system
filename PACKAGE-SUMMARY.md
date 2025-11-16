# Laravel Modular System - Package Summary

## What This Package Does

This package transforms any Laravel application into a **WordPress-like modular system** where you can:

- ✅ Upload modules as ZIP files
- ✅ Enable/disable modules without code changes
- ✅ Manage modules via web interface or CLI
- ✅ Hot-swap functionality without restart
- ✅ Manage settings dynamically

## Package Structure

```
packages/laravel-modular-system/
├── src/
│   ├── Core/
│   │   ├── ModuleManager.php          # Core module management logic
│   │   ├── SettingsManager.php        # Settings management
│   │   └── ApiResponse.php            # Standardized API responses
│   ├── Console/Commands/
│   │   ├── MakeModuleCommand.php      # Create new modules
│   │   ├── ModuleEnableCommand.php    # Enable modules
│   │   ├── ModuleDisableCommand.php   # Disable modules
│   │   ├── ModuleListCommand.php      # List all modules
│   │   └── TestModuleUploadCommand.php # Test uploads
│   ├── Http/Controllers/
│   │   ├── ModuleController.php       # Module API endpoints
│   │   └── SettingsController.php     # Settings API endpoints
│   ├── Facades/
│   │   ├── ModuleManager.php          # ModuleManager facade
│   │   └── Settings.php               # Settings facade
│   ├── Models/
│   │   └── Setting.php                # Settings model
│   └── ModularSystemServiceProvider.php # Main service provider
├── config/
│   └── modular-system.php             # Package configuration
├── database/migrations/
│   └── create_settings_table.php      # Settings table migration
├── routes/
│   ├── api.php                        # API routes
│   └── web.php                        # Web routes
├── composer.json                       # Package dependencies
├── README.md                          # Main documentation
├── INSTALLATION.md                    # Installation guide
├── USAGE.md                           # Usage examples
├── API.md                             # API documentation
├── PUBLISHING.md                      # Publishing guide
├── CHANGELOG.md                       # Version history
└── LICENSE                            # MIT License
```

## Key Components

### 1. ModuleManager (`src/Core/ModuleManager.php`)

**Responsibilities:**
- Load and manage enabled modules
- Enable/disable modules
- Install modules from ZIP files
- Uninstall modules
- Create module ZIP files
- Boot module service providers
- Cache module configurations

**Key Methods:**
```php
getAllModules()              // Get all available modules
enableModule($name)          // Enable a module
disableModule($name)         // Disable a module
installModuleFromZip($path)  // Install from ZIP
uninstallModule($name)       // Remove module
createModuleZip($name)       // Export as ZIP
```

### 2. SettingsManager (`src/Core/SettingsManager.php`)

**Responsibilities:**
- Store/retrieve settings from database
- Group settings by category
- Type casting (string, int, bool, array)
- Cache settings for performance

**Key Methods:**
```php
get($key, $default)          // Get setting value
set($key, $value, $group)    // Set setting value
getGroup($group)             // Get all settings in group
forget($key)                 // Delete setting
```

### 3. Console Commands

**Available Commands:**
```bash
php artisan make:module YourModule        # Create new module
php artisan module:list                   # List all modules
php artisan module:enable YourModule      # Enable module
php artisan module:disable YourModule     # Disable module
php artisan test:module-upload file.zip   # Test ZIP upload
```

### 4. API Controllers

**ModuleController** (`src/Http/Controllers/ModuleController.php`):
- `index()` - List all modules
- `show($name)` - Get module details
- `enable()` - Enable module
- `disable()` - Disable module
- `upload()` - Upload ZIP file
- `uninstall()` - Remove module
- `download($name)` - Download as ZIP

**SettingsController** (`src/Http/Controllers/SettingsController.php`):
- `getGroup($group)` - Get settings group
- `updateGroup($group)` - Update settings group
- `get($key)` - Get single setting
- `set()` - Set single setting

### 5. Facades

**ModuleManager Facade:**
```php
use Monarul007\LaravelModularSystem\Facades\ModuleManager;

ModuleManager::getAllModules();
ModuleManager::enableModule('Blog');
```

**Settings Facade:**
```php
use Monarul007\LaravelModularSystem\Facades\Settings;

Settings::get('site_name', 'Default');
Settings::set('site_name', 'My Site', 'general');
```

## How It Works

### 1. Module Discovery

- Modules are stored in `modules/` directory (configurable)
- Each module has a `module.json` file with metadata
- Enabled modules are tracked in `modules/enabled.json`

### 2. Module Loading

1. `ModularSystemServiceProvider` boots
2. Reads `enabled.json` to get active modules
3. Loads each module's service provider
4. Registers module routes (api.php, web.php)
5. Modules become active immediately

### 3. Module Structure

```
modules/YourModule/
├── module.json                    # Required: Module metadata
├── Providers/
│   └── YourModuleServiceProvider.php  # Required: Service provider
├── Http/Controllers/              # Optional: Controllers
├── routes/
│   ├── api.php                   # Optional: API routes
│   └── web.php                   # Optional: Web routes
├── config/YourModule.php         # Optional: Configuration
├── Database/migrations/          # Optional: Migrations
├── resources/views/              # Optional: Views
└── README.md                     # Optional: Documentation
```

### 4. Settings System

- Settings stored in `settings` table
- Grouped by category (general, otp, system, etc.)
- Type-aware (string, integer, boolean, array)
- Cached for performance
- Accessible via facade or manager

## Configuration

### config/modular-system.php

```php
return [
    'modules_path' => base_path('modules'),  // Where modules are stored
    'cache_enabled' => true,                 // Enable caching
    'cache_ttl' => 3600,                    // Cache duration (seconds)
    'upload_max_size' => 2048,              // Max upload size (KB)
    'allowed_extensions' => ['zip'],         // Allowed file types
    'api_prefix' => 'api/v1/admin',         // API route prefix
    'web_prefix' => 'admin',                // Web route prefix
];
```

## Installation in Laravel App

```bash
# 1. Install package
composer require monarul007/laravel-modular-system

# 2. Publish assets
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"

# 3. Run migrations
php artisan migrate

# 4. Start using
php artisan make:module Blog
php artisan module:enable Blog
```

## Use Cases

### 1. SaaS Applications
Enable/disable features based on subscription plans

### 2. Multi-Tenant Systems
Different modules for different clients

### 3. Plugin Marketplaces
Allow users to install third-party modules

### 4. Feature Flags
Gradually roll out features by enabling modules

### 5. Development
Isolate features in modules for better organization

## Benefits

1. **Modularity**: Organize code into independent modules
2. **Flexibility**: Enable/disable features without code changes
3. **Scalability**: Add unlimited modules
4. **Maintainability**: Each module is self-contained
5. **Reusability**: Share modules across projects
6. **Hot-swappable**: No restart required
7. **User-friendly**: WordPress-like experience

## Technical Details

### Dependencies

- PHP 8.2+
- Laravel 11.0+ or 12.0+
- illuminate/support
- illuminate/console
- illuminate/database
- illuminate/http

### Database

**settings table:**
- `id` - Primary key
- `key` - Setting key (unique)
- `value` - Setting value (text)
- `type` - Data type (string, integer, boolean, array)
- `group` - Category (general, otp, system, etc.)
- `timestamps` - Created/updated timestamps

### Caching

- Module configurations cached for 1 hour (configurable)
- Settings cached for 1 hour (configurable)
- Cache automatically cleared on changes
- Can be disabled in config

### Security

- ZIP file validation
- File size limits
- Module structure validation
- Automatic cleanup on failures
- No arbitrary code execution

## Customization

### Custom Module Path

```php
// config/modular-system.php
'modules_path' => base_path('custom-modules'),
```

### Custom API Prefix

```php
// config/modular-system.php
'api_prefix' => 'api/v2/modules',
```

### Add Middleware

```php
// In your app's route service provider
Route::middleware(['auth', 'admin'])
    ->prefix('api/v1/admin')
    ->group(base_path('routes/modular-api.php'));
```

## Support & Documentation

- **README.md** - Quick start and overview
- **INSTALLATION.md** - Detailed installation
- **USAGE.md** - Usage examples and best practices
- **API.md** - Complete API documentation
- **PUBLISHING.md** - How to publish the package
- **CHANGELOG.md** - Version history

## Next Steps

1. **Customize** - Update vendor name in composer.json
2. **Test** - Test in a Laravel application
3. **Publish** - Push to GitHub and Packagist
4. **Document** - Add more examples and use cases
5. **Extend** - Add more features as needed

## License

MIT License - Free to use, modify, and distribute
