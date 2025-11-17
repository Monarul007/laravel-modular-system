# Inertia Quick Reference

## Return Inertia Response

```php
// Option 1: Helper (Recommended)
return module_inertia('ModuleName', 'Component', ['data' => $value]);

// Option 2: Facade
use Monarul007\LaravelModularSystem\Facades\ModuleInertia;
return ModuleInertia::render('ModuleName', 'Component', ['data' => $value]);

// Option 3: Controller Method
class MyController extends ModuleInertiaController {
    protected string $moduleName = 'ModuleName';
    
    public function index() {
        return $this->inertia('Component', ['data' => $value]);
    }
}

// Option 4: Direct Inertia
use Inertia\Inertia;
return Inertia::render('ModuleName/Component', ['data' => $value]);
```

## Return Blade View with Inertia Support

```php
// Option 1: Helper (Recommended)
return module_view('modulename', 'viewname', ['data' => $value]);

// Option 2: Controller Method
class MyController extends ModuleInertiaController {
    protected string $moduleName = 'ModuleName';
    
    public function page() {
        return $this->moduleView('viewname', ['data' => $value]);
    }
}

// Option 3: Manual
use Inertia\Inertia;
return view('modulename::viewname', [
    'page' => Inertia::getShared(),
    'data' => $value
]);
```

## Module Structure

```
modules/YourModule/
├── Http/Controllers/
│   └── MyController.php
├── resources/
│   ├── js/Pages/
│   │   └── Component.vue
│   └── views/
│       └── page.blade.php
└── routes/
    └── web.php
```

## Common Issues

### ❌ Undefined variable $page
```php
return view('module::welcome'); // Wrong
```

### ✅ Solution
```php
return module_view('module', 'welcome'); // Correct
```

### ❌ Component not found
Check: `modules/Module/resources/js/Pages/Component.vue`

Update `vite.config.js`:
```javascript
input: [
    ...glob.sync('modules/*/resources/js/Pages/**/*.vue'),
]
```

## Middleware Setup

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \Monarul007\LaravelModularSystem\Http\Middleware\ShareInertiaDataWithModules::class,
    ]);
})
```
