# Pre-Publishing Checklist

Use this checklist before publishing your package.

## 1. Package Information

- [ ] Update `composer.json` with your vendor name
- [ ] Update `composer.json` with your name and email
- [ ] Update package description if needed
- [ ] Verify PHP version requirement (^8.2)
- [ ] Verify Laravel version requirement (^11.0|^12.0)

## 2. Namespace Updates

Replace `YourVendor` with your actual vendor name in:

- [ ] `src/ModularSystemServiceProvider.php`
- [ ] `src/Core/ModuleManager.php`
- [ ] `src/Core/SettingsManager.php`
- [ ] `src/Core/ApiResponse.php`
- [ ] `src/Console/Commands/*.php` (all command files)
- [ ] `src/Http/Controllers/*.php` (all controller files)
- [ ] `src/Facades/*.php` (all facade files)
- [ ] `src/Models/Setting.php`
- [ ] `routes/api.php`
- [ ] `composer.json` (autoload section)
- [ ] `README.md` (all code examples)
- [ ] `INSTALLATION.md` (all code examples)
- [ ] `USAGE.md` (all code examples)
- [ ] `QUICKSTART.md` (all code examples)
- [ ] `GETTING-STARTED.md` (all code examples)

## 3. Testing

- [ ] Install in a fresh Laravel application
- [ ] Run `composer install` successfully
- [ ] Run `php artisan vendor:publish` successfully
- [ ] Run `php artisan migrate` successfully
- [ ] Test `php artisan module:list` command
- [ ] Test `php artisan make:module TestModule` command
- [ ] Test `php artisan module:enable TestModule` command
- [ ] Test `php artisan module:disable TestModule` command
- [ ] Test API endpoint: GET `/api/v1/admin/modules`
- [ ] Test API endpoint: POST `/api/v1/admin/modules/enable`
- [ ] Test API endpoint: POST `/api/v1/admin/modules/disable`
- [ ] Test ModuleManager facade in tinker
- [ ] Test Settings facade in tinker
- [ ] Create a test module with routes and verify it works
- [ ] Test module ZIP upload functionality
- [ ] Test module download functionality

## 4. Documentation

- [ ] Review `README.md` for accuracy
- [ ] Review `INSTALLATION.md` for completeness
- [ ] Review `USAGE.md` for clarity
- [ ] Review `API.md` for correctness
- [ ] Review `QUICKSTART.md` for simplicity
- [ ] Update `CHANGELOG.md` with initial release
- [ ] Verify all code examples work
- [ ] Check for broken links
- [ ] Verify all commands are documented
- [ ] Verify all API endpoints are documented

## 5. Code Quality

- [ ] Remove any debug code (dd(), dump(), var_dump())
- [ ] Remove commented-out code
- [ ] Check for proper error handling
- [ ] Verify all methods have proper return types
- [ ] Check for consistent code style
- [ ] Verify proper use of type hints
- [ ] Check for security vulnerabilities
- [ ] Verify input validation in controllers
- [ ] Check file upload security
- [ ] Verify proper use of facades

## 6. Configuration

- [ ] Review `config/modular-system.php` defaults
- [ ] Verify cache settings are appropriate
- [ ] Verify upload size limits are reasonable
- [ ] Check API prefix configuration
- [ ] Verify modules path configuration

## 7. Database

- [ ] Verify migration file is correct
- [ ] Test migration up and down
- [ ] Check table structure
- [ ] Verify indexes are appropriate
- [ ] Test with SQLite, MySQL, and PostgreSQL (if possible)

## 8. Git & Version Control

- [ ] Initialize git repository
- [ ] Create `.gitignore` file
- [ ] Add all files to git
- [ ] Create initial commit
- [ ] Create GitHub/GitLab repository
- [ ] Push to remote repository
- [ ] Create v1.0.0 tag
- [ ] Push tags to remote

## 9. Packagist (if publishing publicly)

- [ ] Create Packagist account
- [ ] Submit package to Packagist
- [ ] Verify package appears on Packagist
- [ ] Set up GitHub webhook for auto-updates
- [ ] Test installation via `composer require`

## 10. License & Legal

- [ ] Verify LICENSE file is present
- [ ] Ensure license is appropriate (MIT recommended)
- [ ] Add copyright year and name
- [ ] Review any third-party dependencies licenses

## 11. Support & Community

- [ ] Add support information to README
- [ ] Enable GitHub Issues
- [ ] Enable GitHub Discussions (optional)
- [ ] Add contributing guidelines (optional)
- [ ] Add code of conduct (optional)

## 12. Marketing (Optional)

- [ ] Write announcement blog post
- [ ] Post on Reddit (r/laravel, r/PHP)
- [ ] Tweet about release
- [ ] Submit to Laravel News
- [ ] Add to awesome-laravel lists
- [ ] Create demo video
- [ ] Create example project

## 13. Final Checks

- [ ] Package installs without errors
- [ ] All commands work as expected
- [ ] All API endpoints return correct responses
- [ ] Documentation is clear and complete
- [ ] No sensitive information in code
- [ ] No hardcoded credentials
- [ ] No absolute paths
- [ ] Works on Windows, Mac, and Linux
- [ ] Compatible with Laravel 11 and 12
- [ ] PHP 8.2+ compatible

## 14. Post-Publishing

- [ ] Monitor GitHub issues
- [ ] Respond to questions
- [ ] Fix reported bugs
- [ ] Consider feature requests
- [ ] Keep dependencies updated
- [ ] Release updates regularly
- [ ] Update CHANGELOG.md with each release

## Quick Test Script

Run this in a fresh Laravel app after installing:

```bash
# Install package
composer require monarul007/laravel-modular-system

# Publish and migrate
php artisan vendor:publish --provider="Monarul007\LaravelModularSystem\ModularSystemServiceProvider"
php artisan migrate

# Test commands
php artisan module:list
php artisan make:module TestModule
php artisan module:enable TestModule
php artisan module:list
php artisan module:disable TestModule

# Test API
php artisan serve &
curl http://localhost:8000/api/v1/admin/modules
curl -X POST http://localhost:8000/api/v1/admin/modules/enable \
  -H "Content-Type: application/json" \
  -d '{"name": "TestModule"}'

# Test facades
php artisan tinker
>>> use Monarul007\LaravelModularSystem\Facades\ModuleManager;
>>> ModuleManager::getAllModules();
>>> exit

# Cleanup
php artisan module:disable TestModule
rm -rf modules/TestModule
```

## Common Issues

### Issue: Namespace not found
**Solution**: Run `composer dump-autoload`

### Issue: Commands not registered
**Solution**: Clear config cache with `php artisan config:clear`

### Issue: Routes not loading
**Solution**: Clear route cache with `php artisan route:clear`

### Issue: Migration already exists
**Solution**: Check if settings table already exists, or rename migration

### Issue: Permission denied on modules directory
**Solution**: Run `chmod -R 755 modules`

## Ready to Publish?

If all items are checked, you're ready to publish! 🎉

```bash
git tag -a v1.0.0 -m "Initial release"
git push origin v1.0.0
```

Then submit to Packagist or share with your team.
