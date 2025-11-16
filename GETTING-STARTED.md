# Getting Started - For Package Developers

This guide is for YOU - the package developer. It explains how to customize, test, and publish this package.

## What You Have

A complete, production-ready Laravel package that provides WordPress-like modular functionality to any Laravel application.

## Before Publishing

### 1. Update Package Information

Edit `composer.json`:

```json
{
    "name": "monarul007/laravel-modular-system",  // Change this!
    "authors": [
        {
            "name": "Your Name",                   // Change this!
            "email": "your.email@example.com"      // Change this!
        }
    ]
}
```

### 2. Update Namespaces

Find and replace in ALL files:
- `YourVendor` → Your actual vendor name (e.g., `Acme`, `MyCompany`)
- `monarul007` → Your lowercase vendor name

Files to update:
- All PHP files in `src/`
- `composer.json`
- `README.md`
- All documentation files

### 3. Test Locally

#### Option A: Test in Your Current Laravel App

From your Laravel app root:

```json
// composer.json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/laravel-modular-system"
        }
    ],
    "require": {
        "monarul007/laravel-modular-system": "*"
    }
}
```

```bash
composer update monarul007/laravel-modular-system
php artisan vendor:publish --provider="YourVendor\LaravelModularSystem\ModularSystemServiceProvider"
php artisan migrate
```

#### Option B: Create Fresh Laravel App for Testing

```bash
cd ..
composer create-project laravel/laravel test-app
cd test-app

# Add to composer.json
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

composer update
php artisan vendor:publish --provider="YourVendor\LaravelModularSystem\ModularSystemServiceProvider"
php artisan migrate
php artisan serve
```

### 4. Test All Features

```bash
# Test commands
php artisan module:list
php artisan make:module TestModule
php artisan module:enable TestModule
php artisan module:list
php artisan module:disable TestModule

# Test API
curl http://localhost:8000/api/v1/admin/modules

# Test facades
php artisan tinker
>>> use YourVendor\LaravelModularSystem\Facades\ModuleManager;
>>> ModuleManager::getAllModules();
```

## Publishing Options

### Option 1: GitHub + Packagist (Free, Public)

**Best for:** Open source projects

```bash
cd packages/laravel-modular-system

# Initialize git
git init
git add .
git commit -m "Initial commit"

# Create GitHub repo (via web interface)
# Then:
git remote add origin https://github.com/monarul007/laravel-modular-system.git
git branch -M main
git push -u origin main

# Tag release
git tag -a v1.0.0 -m "First release"
git push origin v1.0.0

# Submit to Packagist
# 1. Go to https://packagist.org
# 2. Click "Submit"
# 3. Enter: https://github.com/monarul007/laravel-modular-system
```

Users install with:
```bash
composer require monarul007/laravel-modular-system
```

### Option 2: Private Repository

**Best for:** Commercial or internal packages

```bash
# Push to private GitHub/GitLab/Bitbucket
git remote add origin https://github.com/monarul007/laravel-modular-system.git
git push -u origin main
```

Users install with:
```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/monarul007/laravel-modular-system.git"
        }
    ],
    "require": {
        "monarul007/laravel-modular-system": "^1.0"
    }
}
```

### Option 3: Keep Local

**Best for:** Single project use

Keep in `packages/` directory and use path repository (already set up).

## Customization Ideas

### Add More Commands

Create in `src/Console/Commands/`:

```php
<?php

namespace YourVendor\LaravelModularSystem\Console\Commands;

use Illuminate\Console\Command;

class ModuleInstallCommand extends Command
{
    protected $signature = 'module:install {url}';
    protected $description = 'Install module from URL';

    public function handle()
    {
        // Implementation
    }
}
```

Register in `ModularSystemServiceProvider.php`:
```php
$this->commands([
    Console\Commands\ModuleInstallCommand::class,
]);
```

### Add Middleware

Create `src/Http/Middleware/CheckModuleAccess.php`:

```php
<?php

namespace YourVendor\LaravelModularSystem\Http\Middleware;

use Closure;

class CheckModuleAccess
{
    public function handle($request, Closure $next)
    {
        // Check permissions
        return $next($request);
    }
}
```

### Add Events

Create `src/Events/ModuleEnabled.php`:

```php
<?php

namespace YourVendor\LaravelModularSystem\Events;

class ModuleEnabled
{
    public function __construct(public string $moduleName) {}
}
```

Dispatch in `ModuleManager`:
```php
event(new ModuleEnabled($moduleName));
```

### Add Tests

Create `tests/ModuleManagerTest.php`:

```php
<?php

namespace YourVendor\LaravelModularSystem\Tests;

use Orchestra\Testbench\TestCase;

class ModuleManagerTest extends TestCase
{
    public function test_can_list_modules()
    {
        // Test implementation
    }
}
```

Run tests:
```bash
composer test
```

## Maintenance

### Releasing Updates

```bash
# Make changes
git add .
git commit -m "Add new feature"
git push

# Tag new version
git tag -a v1.1.0 -m "Add feature X"
git push origin v1.1.0

# Packagist auto-updates via webhook
```

### Version Numbers

Follow Semantic Versioning:
- `v1.0.0` → `v1.0.1` - Bug fixes
- `v1.0.0` → `v1.1.0` - New features (backward compatible)
- `v1.0.0` → `v2.0.0` - Breaking changes

Update `CHANGELOG.md` with each release.

## Documentation

Keep these files updated:
- ✅ `README.md` - Main documentation
- ✅ `INSTALLATION.md` - Installation steps
- ✅ `USAGE.md` - Usage examples
- ✅ `API.md` - API reference
- ✅ `QUICKSTART.md` - Quick start guide
- ✅ `CHANGELOG.md` - Version history
- ✅ `PACKAGE-SUMMARY.md` - Architecture overview

## Support

Add support channels to `README.md`:

```markdown
## Support

- **Issues**: https://github.com/monarul007/laravel-modular-system/issues
- **Discussions**: https://github.com/monarul007/laravel-modular-system/discussions
- **Email**: support@yourcompany.com
- **Documentation**: Full docs in repository
```

## Marketing (Optional)

1. **Laravel News** - Submit to Laravel News
2. **Reddit** - Post on r/laravel, r/PHP
3. **Twitter/X** - Tweet with #Laravel hashtag
4. **Dev.to** - Write tutorial article
5. **YouTube** - Create demo video
6. **Laravel Packages** - Submit to laravel-packages.com

## Next Steps

1. ✅ Update vendor name in all files
2. ✅ Test in a fresh Laravel app
3. ✅ Create GitHub repository
4. ✅ Tag first release (v1.0.0)
5. ✅ Submit to Packagist
6. ✅ Write announcement blog post
7. ✅ Share with community

## Questions?

- Check `PACKAGE-SUMMARY.md` for architecture details
- Read `PUBLISHING.md` for publishing options
- See `USAGE.md` for usage examples

## License

MIT License - Free to use, modify, and distribute.

---

**You're ready to go! 🚀**

This package is production-ready. Just update the vendor name and publish it.
