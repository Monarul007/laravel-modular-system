# Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Laravel Application                      │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌───────────────────────────────────────────────────────┐  │
│  │         Laravel Modular System Package                 │  │
│  │                                                         │  │
│  │  ┌─────────────────────────────────────────────────┐  │  │
│  │  │   ModularSystemServiceProvider                   │  │  │
│  │  │   - Registers ModuleManager                      │  │  │
│  │  │   - Loads module routes                          │  │  │
│  │  │   - Boots enabled modules                        │  │  │
│  │  └─────────────────────────────────────────────────┘  │  │
│  │                                                         │  │
│  │  ┌──────────────┐  ┌────────────┐  │  │
│  │  │ ModuleManager│  │ApiResponse │  │  │
│  │  │              │  │            │  │  │
│  │  │ - Load       │  │ - Success  │  │  │
│  │  │ - Enable     │  │ - Groups      │  │ - Error    │  │  │
│  │  │ - Disable    │  │ - Cache       │  │ - Paginate │  │  │
│  │  │ - Install    │  │ - Types       │  │            │  │  │
│  │  │ - Uninstall  │  │               │  │            │  │  │
│  │  └──────────────┘  └──────────────┘  └────────────┘  │  │
│  │                                                         │  │
│  │  ┌─────────────────────────────────────────────────┐  │  │
│  │  │              Console Commands                    │  │  │
│  │  │  - make:module                                   │  │  │
│  │  │  - module:enable / module:disable                │  │  │
│  │  │  - module:list                                   │  │  │
│  │  │  - test:module-upload                            │  │  │
│  │  └─────────────────────────────────────────────────┘  │  │
│  │                                                         │  │
│  │  ┌─────────────────────────────────────────────────┐  │  │
│  │  │           HTTP Controllers                       │  │  │
│  │  │  - ModuleController (CRUD operations)            │  │  │
│  │  └─────────────────────────────────────────────────┘  │  │
│  │                                                         │  │
│  │  ┌─────────────────────────────────────────────────┐  │  │
│  │  │                 Facades                          │  │  │
│  │  │  - ModuleManager::getAllModules()                │  │  │
│  │  └─────────────────────────────────────────────────┘  │  │
│  │                                                         │  │
│  └─────────────────────────────────────────────────────┘  │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐  │
│  │                  Modules Directory                     │  │
│  │                                                         │  │
│  │  modules/                                              │  │
│  │  ├── enabled.json (tracks enabled modules)            │  │
│  │  ├── Blog/                                             │  │
│  │  │   ├── module.json                                  │  │
│  │  │   ├── Providers/BlogServiceProvider.php            │  │
│  │  │   ├── Http/Controllers/                            │  │
│  │  │   ├── routes/api.php                               │  │
│  │  │   └── config/Blog.php                              │  │
│  │  ├── Shop/                                             │  │
│  │  │   └── ... (same structure)                         │  │
│  │  └── OTP/                                              │  │
│  │      └── ... (same structure)                         │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐  │
│  │                    Database                            │  │
│  │                                                         │  │
│  │  ├── value                                             │  │
│  │  ├── type (string|integer|boolean|array)              │  │
│  │  ├── group (general|otp|system|...)                   │  │
│  │  └── timestamps                                        │  │
│  └───────────────────────────────────────────────────────┘  │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Request Flow

### 1. Module Enable Flow

```
User Request
    │
    ├─→ CLI: php artisan module:enable Blog
    │       │
    │       └─→ ModuleEnableCommand
    │               │
    │               └─→ ModuleManager::enableModule('Blog')
    │                       │
    │                       ├─→ Check if module exists
    │                       ├─→ Add to enabled array
    │                       ├─→ Save to enabled.json
    │                       └─→ Clear cache
    │
    └─→ API: POST /api/v1/admin/modules/enable
            │
            └─→ ModuleController::enable()
                    │
                    └─→ ModuleManager::enableModule('Blog')
                            │
                            └─→ (same as above)
```

### 2. Module Loading Flow (Application Boot)

```
Laravel Boot
    │
    └─→ ModularSystemServiceProvider::boot()
            │
            ├─→ ModuleManager::loadEnabledModules()
            │       │
            │       ├─→ Read enabled.json
            │       └─→ Cache enabled modules list
            │
            ├─→ ModuleManager::bootModules()
            │       │
            │       └─→ For each enabled module:
            │               │
            │               ├─→ Read module.json
            │               ├─→ Get service providers
            │               └─→ Register providers
            │
            └─→ Load module routes
                    │
                    └─→ For each enabled module:
                            │
                            ├─→ Load routes/api.php
                            └─→ Load routes/web.php
```

### 3. Module Configuration Flow

```
ModuleManager::getModuleConfig('Blog')
    │
    └─→ Check in-memory cache
            │       │
            │       └─→ If found: return value
            │
            └─→ Check Laravel cache
                    │
                    ├─→ If found: return value
                    │
                    └─→ Query database
                            │
                            ├─→ Find setting by key
                            ├─→ Cast value to correct type
                            ├─→ Cache result
                            └─→ Return value
```

### 4. Module Upload Flow

```
User uploads ZIP file
    │
    └─→ POST /api/v1/admin/modules/upload
            │
            └─→ ModuleController::upload()
                    │
                    └─→ ModuleManager::installModuleFromZip()
                            │
                            ├─→ Validate ZIP file
                            ├─→ Extract to temp directory
                            ├─→ Find module.json
                            ├─→ Validate module structure
                            ├─→ Check if module exists
                            ├─→ Copy to modules directory
                            ├─→ Clean up temp files
                            └─→ Return success/error
```

## Component Interactions

```
┌──────────────┐
│   Facades    │
│              │
│ ModuleManager│◄─────┐
└──────────────┘      │
                      │
┌──────────────┐      │
│  Controllers │      │
│              │      │
│   Module     │──────┤
└──────────────┘      │
                      │
┌──────────────┐      │
│   Commands   │      │
│              │      │
│ make:module  │──────┤
│ module:*     │      │
└──────────────┘      │
                      │
                      ▼
            ┌──────────────────┐
            │   Core Classes   │
            │                  │
            │  ModuleManager   │◄──────┐
            └──────────────────┘       │
                      │                │
                      │                │
                      ▼                │
            ┌──────────────────┐       │
            │   File System    │       │
            │                  │       │
            │  modules/        │───────┘
            │  enabled.json    │
            └──────────────────┘
```

## Data Flow

### Module Configuration

```
modules/Blog/module.json
    │
    ├─→ Read by ModuleManager
    │       │
    │       └─→ Cached in memory
    │               │
    │               └─→ Used for:
    │                       ├─→ Service provider registration
    │                       ├─→ Dependency checking
    │                       └─→ Module metadata
    │
    └─→ Returned via API
            │
            └─→ GET /api/v1/admin/modules
```



## Caching Strategy

```
┌─────────────────────────────────────────────────────┐
│                  Cache Layers                        │
├─────────────────────────────────────────────────────┤
│                                                       │
│  Layer 1: In-Memory (PHP Variables)                 │
│  ├─→ ModuleManager::$enabledModules                 │
│  └─→ ModuleManager::$moduleConfigs                  │
│                                                       │
│  Layer 2: Laravel Cache (Redis/Memcached/File)      │
│  └─→ 'modular_system.enabled_modules' (1 hour)      │
│                                                       │
│  Layer 3: File System                                │
│  └─→ modules/{name}/module.json                     │
│                                                       │
└─────────────────────────────────────────────────────┘

Cache Invalidation:
├─→ Module enabled/disabled: Clear module cache
├─→ Setting updated: Clear setting cache
└─→ Manual: php artisan cache:clear
```

## Security Layers

```
┌─────────────────────────────────────────────────────┐
│                Security Measures                     │
├─────────────────────────────────────────────────────┤
│                                                       │
│  1. File Upload Validation                          │
│     ├─→ File type check (ZIP only)                  │
│     ├─→ File size limit (2MB default)               │
│     └─→ MIME type validation                        │
│                                                       │
│  2. Module Structure Validation                     │
│     ├─→ module.json must exist                      │
│     ├─→ Valid JSON format                           │
│     └─→ Required fields present                     │
│                                                       │
│  3. Path Validation                                 │
│     ├─→ No directory traversal                      │
│     ├─→ Restricted to modules directory             │
│     └─→ Safe file operations                        │
│                                                       │
│  4. Input Validation                                │
│     ├─→ Request validation in controllers           │
│     ├─→ Type checking                               │
│     └─→ Sanitization                                │
│                                                       │
│  5. Error Handling                                  │
│     ├─→ Try-catch blocks                            │
│     ├─→ Automatic cleanup on failure                │
│     └─→ Safe error messages                         │
│                                                       │
└─────────────────────────────────────────────────────┘
```

## Extension Points

```
┌─────────────────────────────────────────────────────┐
│            How to Extend the Package                 │
├─────────────────────────────────────────────────────┤
│                                                       │
│  1. Add Custom Commands                             │
│     └─→ Create in Console/Commands/                 │
│         Register in ServiceProvider                  │
│                                                       │
│  2. Add Middleware                                  │
│     └─→ Create in Http/Middleware/                  │
│         Apply to routes                              │
│                                                       │
│  3. Add Events                                      │
│     └─→ Create in Events/                           │
│         Dispatch in ModuleManager                    │
│                                                       │
│  4. Add Listeners                                   │
│     └─→ Create in Listeners/                        │
│         Register in ServiceProvider                  │
│                                                       │
│  5. Extend ModuleManager                            │
│     └─→ Create custom manager                       │
│         Bind in ServiceProvider                      │
│                                                       │
│  6. Add Custom Routes                               │
│     └─→ Publish routes                              │
│         Modify as needed                             │
│                                                       │
└─────────────────────────────────────────────────────┘
```

## Performance Considerations

1. **Caching**: Two-layer cache (memory + Laravel cache)
2. **Lazy Loading**: Modules loaded only when enabled
3. **File Operations**: Minimal file system access
4. **Route Loading**: Only enabled module routes loaded

## Scalability

- **Horizontal**: Multiple app instances share same modules directory
- **Vertical**: Handles hundreds of modules efficiently
- **Cache**: Distributed cache (Redis) for multi-server setups
