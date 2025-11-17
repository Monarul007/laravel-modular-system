# API Documentation

## Base URL

```
/api/v1/admin
```

(Configurable via `config/modular-system.php`)

## Module Management

### List All Modules

```http
GET /api/v1/admin/modules
```

**Response:**
```json
{
    "success": true,
    "message": "Modules retrieved successfully",
    "data": {
        "YourModule": {
            "name": "YourModule",
            "description": "Module description",
            "version": "1.0.0",
            "enabled": true,
            "path": "/path/to/modules/YourModule"
        }
    }
}
```

### Get Module Details

```http
GET /api/v1/admin/modules/{name}
```

**Response:**
```json
{
    "success": true,
    "message": "Module details retrieved successfully",
    "data": {
        "name": "YourModule",
        "description": "Module description",
        "version": "1.0.0",
        "enabled": true
    }
}
```

### Enable Module

```http
POST /api/v1/admin/modules/enable
Content-Type: application/json

{
    "name": "YourModule"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Module 'YourModule' enabled successfully",
    "data": null
}
```

### Disable Module

```http
POST /api/v1/admin/modules/disable
Content-Type: application/json

{
    "name": "YourModule"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Module 'YourModule' disabled successfully",
    "data": null
}
```

### Upload Module

```http
POST /api/v1/admin/modules/upload
Content-Type: multipart/form-data

file: [ZIP file]
```

**Response:**
```json
{
    "success": true,
    "message": "Module 'YourModule' installed successfully",
    "data": {
        "name": "YourModule",
        "version": "1.0.0",
        "description": "Module description"
    }
}
```

### Uninstall Module

```http
POST /api/v1/admin/modules/uninstall
Content-Type: application/json

{
    "name": "YourModule"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Module 'YourModule' uninstalled successfully",
    "data": null
}
```

### Download Module

```http
GET /api/v1/admin/modules/download/{name}
```

**Response:** ZIP file download

## Settings Management

### Get Settings Group

```http
GET /api/v1/admin/settings/{group}
```

**Response:**
```json
{
    "success": true,
    "message": "Settings for group 'general' retrieved successfully",
    "data": {
        "site_name": "My Site",
        "site_email": "admin@example.com"
    }
}
```

### Update Settings Group

```http
POST /api/v1/admin/settings/{group}
Content-Type: application/json

{
    "site_name": "New Site Name",
    "site_email": "newemail@example.com"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Settings for group 'general' updated successfully",
    "data": null
}
```

### Get Single Setting

```http
GET /api/v1/admin/setting/{key}
```

**Response:**
```json
{
    "success": true,
    "message": "Setting retrieved successfully",
    "data": {
        "key": "site_name",
        "value": "My Site"
    }
}
```

### Set Single Setting

```http
POST /api/v1/admin/setting
Content-Type: application/json

{
    "key": "site_name",
    "value": "New Site Name",
    "group": "general"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Setting updated successfully",
    "data": null
}
```

## Error Responses

All endpoints return errors in this format:

```json
{
    "success": false,
    "message": "Error message here",
    "errors": {} // Optional validation errors
}
```

### Common HTTP Status Codes

- `200` - Success
- `400` - Bad Request (validation error, already enabled/disabled)
- `404` - Not Found (module doesn't exist)
- `500` - Server Error (operation failed)

## Authentication

By default, routes are not protected. Add middleware in your application:

```php
Route::middleware(['auth:sanctum'])->group(function () {
    // Module routes
});
```

## Rate Limiting

Consider adding rate limiting for upload endpoints:

```php
Route::middleware(['throttle:uploads'])->group(function () {
    Route::post('modules/upload', [ModuleController::class, 'upload']);
});
```
