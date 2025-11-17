# TestModule - Inertia.js Example

This is an example module demonstrating Inertia.js integration with the Laravel Modular System.

## Features Demonstrated

1. **Inertia Response**: Returns an Inertia.js response with Vue component
2. **Blade View with Inertia**: Shows how to use `@inertiaHead` in module Blade views
3. **ModuleInertiaController**: Extends the base controller for easy Inertia usage

## Installation

1. Copy this folder to your `modules/` directory:
   ```bash
   cp -r examples/TestModule-Inertia modules/TestModule
   ```

2. Enable the module:
   ```bash
   php artisan module:enable TestModule
   ```

3. Update your `vite.config.js` to include module components:
   ```javascript
   import { glob } from 'glob';
   
   export default defineConfig({
       plugins: [
           laravel({
               input: [
                   'resources/js/app.js',
                   ...glob.sync('modules/*/resources/js/Pages/**/*.vue'),
               ],
           }),
       ],
   });
   ```

4. Build assets:
   ```bash
   npm install glob
   npm run build
   ```

## Usage

Visit these URLs:

- `/testmodule` - Inertia.js page with Vue component
- `/testmodule/blade` - Blade view with Inertia directives

## Code Examples

### Controller (ModuleInertiaController)

```php
class WelcomeController extends ModuleInertiaController
{
    protected string $moduleName = 'TestModule';

    public function index()
    {
        return $this->inertia('Welcome', [
            'message' => 'Hello from TestModule!'
        ]);
    }

    public function blade()
    {
        return $this->moduleView('welcome', [
            'title' => 'Blade View'
        ]);
    }
}
```

### Vue Component

```vue
<template>
    <div>
        <h1>{{ message }}</h1>
    </div>
</template>

<script setup>
defineProps({
    message: String
});
</script>
```

### Blade View with Inertia

```blade
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    @inertiaHead
</head>
<body>
    <h1>{{ $title }}</h1>
</body>
</html>
```

## Key Points

1. **No `$page` errors**: The `moduleView()` method automatically provides the `$page` variable
2. **Component namespacing**: Vue components are namespaced by module (e.g., `TestModule/Welcome`)
3. **Easy to use**: Extend `ModuleInertiaController` for clean, simple code

## Learn More

See [INERTIA-GUIDE.md](../../INERTIA-GUIDE.md) for complete documentation.
