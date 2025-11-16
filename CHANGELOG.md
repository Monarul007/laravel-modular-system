# Changelog

All notable changes to `laravel-modular-system` will be documented in this file.

## [1.2.0] - 2024-11-16

### Added
- **Interactive Module Creation**: Confirmation prompts and route alias configuration during module creation
- **Interactive Module Enable**: Confirmation prompts with module information display
- **Enhanced Module Removal**: Asset cleanup prompts and confirmation before deletion
- **Route Alias Management**: New `module:set-alias` command to change module route aliases
- **Smart Route Suggestions**: Automatic kebab-case conversion (e.g., BlogSystem → blog-system)
- **Route Conflict Detection**: Checks for existing routes before allowing alias assignment
- **Comprehensive Testing Guide**: New TESTING-EXAMPLES.md with real-world workflows

### Enhanced
- `make:module` - Added `--skip-confirmation` and `--alias` options
- `module:enable` - Added `--skip-confirmation` and `--alias` options
- `module:remove` - Added asset cleanup prompt and better confirmation flow
- All commands now provide helpful next steps and clear instructions

### Documentation
- Added TESTING-EXAMPLES.md with complete testing workflows
- Updated README.md with interactive features section
- Added examples for all new interactive prompts

## [1.1.0] - 2024-11-16

### Added
- **Module Removal Command**: `module:remove` command for safely uninstalling modules
- **View Resolution System**: Automatic view namespace registration for all enabled modules
- **Component Generators**: New commands for creating module-specific components:
  - `module:make-controller` - Create controllers with --api and --resource options
  - `module:make-model` - Create models with optional migration generation
  - `module:make-migration` - Create migrations with --create and --table options
  - `module:make-middleware` - Create middleware classes
  - `module:make-command` - Create artisan commands
- **Asset Management**: 
  - `module:publish` command for publishing module assets to public directory
  - ModuleViewHelper class for asset and view path resolution
  - ModuleView facade for easy access to view and asset helpers
- **Documentation**:
  - MODULE-COMMANDS.md - Complete command reference guide
  - MODULE-VIEWS-GUIDE.md - Comprehensive view and asset usage guide
  - QUICK-REFERENCE.md - Quick reference cheat sheet

### Enhanced
- Service provider now automatically loads module views with proper namespacing
- Module structure includes Console/Commands and Models directories
- Updated README with new features and usage examples

### Features
- View namespace: Access module views via `view('modulename::viewname')`
- Asset publishing: Publish JS/CSS assets to public directory
- Component scaffolding: Generate controllers, models, migrations, etc. for modules
- Safe module removal with confirmation prompts

## [1.0.0] - 2024-11-16

### Added
- Initial release
- ModuleManager for dynamic module management
- SettingsManager for configuration management
- Console commands (make:module, module:enable, module:disable, module:list)
- API endpoints for module and settings management
- ZIP upload/download functionality
- Hot-swappable module system
- Caching support for performance
- Comprehensive documentation

### Features
- WordPress-like plug-and-play functionality
- Enable/disable modules without restart
- Upload modules via ZIP files
- RESTful API endpoints
- Artisan CLI commands
- Configurable module paths
- Settings grouped by category
- Automatic service provider registration

### Security
- File validation for uploads
- Module structure validation
- Size limits on uploads
- Automatic cleanup on failures
