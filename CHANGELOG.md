# Changelog

All notable changes to `laravel-modular-system` will be documented in this file.

## [1.4.0] - 2024-11-18

### Added
- **Smart View Publishing**: Automatic template engine detection and appropriate view publishing
  - `TemplateEngineDetector` class for detecting Blade, Inertia+Vue, or Inertia+React
  - Automatic detection based on `composer.json` and `package.json`
  - Smart publishing via `--tag=modular-views` (auto-detects appropriate views)
  - Manual override options: `--tag=modular-views-blade` and `--tag=modular-views-inertia`
- **Blade Template Support**: Complete admin panel with Blade views
  - `resources/views/admin/layout.blade.php` - Main admin layout
  - `resources/views/admin/dashboard.blade.php` - Dashboard page
  - `resources/views/admin/modules/index.blade.php` - Module management

  - Tailwind CSS styling with responsive design
  - Modal dialogs and form handling
- **React Component Support**: Inertia.js with React/JSX components
  - `resources/js/Pages/Admin/Layout.jsx` - React admin layout
  - `resources/js/Pages/Admin/Dashboard.jsx` - Dashboard component
  - `resources/js/Pages/Admin/Modules/Index.jsx` - Module management

  - React Hooks integration (useState, useForm)
  - Inertia React adapter support
- **Detection Command**: New `modular:detect-engine` command
  - Shows detected templating engine
  - Displays what will be published
  - Provides publishing recommendations
- **Inertia Middleware**: HandleInertiaRequests middleware for flash message sharing
- **Documentation**:
  - VIEW-PUBLISHING.md - Complete guide to smart view publishing
  - UPGRADE-GUIDE.md - Migration guide for existing users
  - INERTIA-SETUP.md - Inertia setup and troubleshooting guide
  - Updated README.md with smart publishing information

### Fixed
- **Vue Flash Messages**: Added optional chaining to prevent "Cannot read properties of undefined" errors
- **Inertia Flash Data**: Improved flash message handling in Vue components

### Enhanced
- Service provider now uses smart detection for view publishing
- Routes work seamlessly with Blade, Inertia+Vue, and Inertia+React
- Publishing system supports multiple templating engines
- Better organization of view files by engine type
- Vue components now safely handle missing flash data

### Features
- Automatic detection of Blade, Inertia+Vue, or Inertia+React
- Publish only the views you need based on your stack
- Force publish specific view types when needed
- Complete admin panel available in all three formats
- Backward compatible with existing Inertia+Vue setups
- Proper flash message handling for Inertia applications

## [1.3.0] - 2024-11-17

### Added
- **Inertia.js Support**: Full compatibility with Inertia.js for modern SPAs
  - `InertiaHelper` class for rendering Inertia responses from modules
  - `ModuleInertia` facade for easy access
  - `module_inertia()` helper function for returning Inertia responses
  - `module_view()` helper function for Blade views with Inertia support
  - `ModuleInertiaController` base controller for module controllers
  - `ShareInertiaDataWithModules` middleware for sharing module data
- **Admin Panel**: Complete admin interface with Inertia.js
  - `AdminController` - Dashboard with module statistics
  - `AdminModuleController` - Module management (enable, disable, upload, download, uninstall)

  - Vue components for admin interface (Dashboard, Modules)
  - Admin routes with authentication middleware
- **Documentation**: 
  - INERTIA-INTEGRATION.md - Complete Inertia integration guide
  - ADMIN-PANEL-SETUP.md - Admin panel setup instructions
  - Example TestModule demonstrating Inertia integration
- **Helper Functions**: Global helper functions for Inertia and view rendering

### Fixed
- **Undefined $page Variable**: Module Blade views using `@inertiaHead` now work correctly
- **Inertia Response**: Modules can now easily return Inertia responses
- **Vue Component Imports**: Fixed import paths in admin Vue components

### Enhanced
- Service provider now registers Inertia helper singleton
- Automatic loading of helper functions
- Better integration with Inertia middleware
- Publishable Vue components for admin panel
- Routes automatically loaded via service provider

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

- Console commands (make:module, module:enable, module:disable, module:list)
- API endpoints for module management
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

- Automatic service provider registration

### Security
- File validation for uploads
- Module structure validation
- Size limits on uploads
- Automatic cleanup on failures
