# Publishing Guide

This guide explains how to publish your Laravel Modular System package to make it available for other Laravel applications.

## Option 1: Publish to Packagist (Recommended)

### Prerequisites

1. GitHub account
2. Packagist account (https://packagist.org)
3. Composer installed

### Steps

#### 1. Update composer.json

Replace placeholders in `composer.json`:

```json
{
    "name": "monarul007/laravel-modular-system",
    "authors": [
        {
            "name": "Your Name",
            "email": "your.email@example.com"
        }
    ]
}
```

#### 2. Create GitHub Repository

```bash
cd packages/laravel-modular-system
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/monarul007/laravel-modular-system.git
git push -u origin main
```

#### 3. Tag a Release

```bash
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

#### 4. Submit to Packagist

1. Go to https://packagist.org
2. Click "Submit"
3. Enter your GitHub repository URL: `https://github.com/monarul007/laravel-modular-system`
4. Click "Check"

#### 5. Set Up Auto-Update

In your GitHub repository:
1. Go to Settings → Webhooks
2. Packagist will automatically add a webhook
3. Or manually add: `https://packagist.org/api/github?username=monarul007`

### Installation by Users

```bash
composer require monarul007/laravel-modular-system
```

## Option 2: Private Repository

### Using GitHub Private Repository

#### composer.json in user's Laravel app:

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

### Using GitLab

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.com/monarul007/laravel-modular-system.git"
        }
    ]
}
```

### Using Bitbucket

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://bitbucket.org/monarul007/laravel-modular-system.git"
        }
    ]
}
```

## Option 3: Private Packagist

For commercial packages or private company packages.

### Steps

1. Sign up at https://packagist.com (commercial)
2. Add your repository
3. Get authentication token
4. Users add to their `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.packagist.com/yourcompany/"
        }
    ]
}
```

## Option 4: Local Path (Development)

For local development or testing.

### In User's Laravel App

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../packages/laravel-modular-system",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "monarul007/laravel-modular-system": "*"
    }
}
```

```bash
composer update monarul007/laravel-modular-system
```

## Option 5: Satis (Self-Hosted)

Host your own Composer repository.

### Setup Satis

```bash
composer create-project composer/satis --stability=dev --keep-vcs
cd satis
```

### Create satis.json

```json
{
    "name": "My Company Repository",
    "homepage": "https://packages.mycompany.com",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/monarul007/laravel-modular-system"
        }
    ],
    "require-all": true
}
```

### Build Repository

```bash
php bin/satis build satis.json public/
```

### Users Add Repository

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.mycompany.com"
        }
    ]
}
```

## Versioning

Follow Semantic Versioning (semver.org):

- **MAJOR** version (1.0.0 → 2.0.0): Breaking changes
- **MINOR** version (1.0.0 → 1.1.0): New features, backward compatible
- **PATCH** version (1.0.0 → 1.0.1): Bug fixes

### Creating Releases

```bash
# Bug fix
git tag -a v1.0.1 -m "Fix module loading issue"
git push origin v1.0.1

# New feature
git tag -a v1.1.0 -m "Add module dependencies support"
git push origin v1.1.0

# Breaking change
git tag -a v2.0.0 -m "Refactor module structure"
git push origin v2.0.0
```

## Documentation

Ensure these files are up to date:

- ✅ README.md - Overview and quick start
- ✅ INSTALLATION.md - Detailed installation steps
- ✅ USAGE.md - Usage examples
- ✅ API.md - API documentation
- ✅ CHANGELOG.md - Version history
- ✅ LICENSE - License information

## Testing Before Publishing

### 1. Test Locally

```bash
cd packages/laravel-modular-system
composer install
composer dump-autoload
```

### 2. Test in Laravel App

```bash
cd ../../your-laravel-app
composer require monarul007/laravel-modular-system:@dev
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"
php artisan migrate
php artisan module:list
```

### 3. Test Commands

```bash
php artisan make:module TestModule
php artisan module:enable TestModule
php artisan module:list
php artisan module:disable TestModule
```

## Maintenance

### Update Package

```bash
# Make changes
git add .
git commit -m "Update feature X"
git push

# Create new version
git tag -a v1.0.1 -m "Bug fixes"
git push origin v1.0.1
```

### Update Packagist

Packagist auto-updates via webhook. Manual update:
1. Go to https://packagist.org/packages/monarul007/laravel-modular-system
2. Click "Update"

## Support

Add support information to README.md:

```markdown
## Support

- Issues: https://github.com/monarul007/laravel-modular-system/issues
- Email: support@yourcompany.com
- Documentation: https://docs.yourcompany.com
```

## License

Ensure LICENSE file is included. MIT License is recommended for open source.

## Marketing

1. Add to Laravel News
2. Post on Reddit (r/laravel, r/PHP)
3. Tweet about it
4. Write blog post
5. Create video tutorial
6. Add to awesome-laravel lists
