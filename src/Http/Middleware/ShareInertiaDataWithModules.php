<?php

namespace Monarul007\LaravelModularSystem\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareInertiaDataWithModules
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Share module-specific data with Inertia
        Inertia::share([
            'modules' => function () {
                if (app()->bound('module-manager')) {
                    $moduleManager = app('module-manager');
                    return [
                        'enabled' => $moduleManager->getEnabledModules(),
                        'all' => array_keys($moduleManager->getAllModules()),
                    ];
                }
                return ['enabled' => [], 'all' => []];
            },
        ]);

        return $next($request);
    }
}
