<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | The directory where modules will be stored and loaded from.
    |
    */
    'modules_path' => base_path('modules'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Enable caching for module configurations and settings.
    |
    */
    'cache_enabled' => true,
    'cache_ttl' => 3600, // seconds

    /*
    |--------------------------------------------------------------------------
    | Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for module ZIP file uploads.
    |
    */
    'upload_max_size' => 2048, // KB
    'allowed_extensions' => ['zip'],

    /*
    |--------------------------------------------------------------------------
    | Module Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for modules.
    |
    */
    'enabled_modules_file' => 'enabled.json',
    'module_config_file' => 'module.json',

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for API routes and responses.
    | Note: Don't include 'api' prefix as Laravel adds it automatically
    |
    */
    'api_prefix' => 'v1/admin',
    'web_prefix' => 'admin',
];
