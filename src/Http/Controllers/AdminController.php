<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function dashboard(): Response
    {
        $modules = $this->moduleManager->getAllModules();
        $enabledCount = count($this->moduleManager->getEnabledModules());
        $totalCount = count($modules);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_modules' => $totalCount,
                'enabled_modules' => $enabledCount,
                'disabled_modules' => $totalCount - $enabledCount,
            ],
            'recent_modules' => array_slice($modules, 0, 5)
        ]);
    }
}
