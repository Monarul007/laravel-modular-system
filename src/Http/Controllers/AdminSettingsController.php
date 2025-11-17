<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\SettingsManager;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingsController extends Controller
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    public function index(): Response
    {
        // Get settings grouped by their group
        $settingsGroups = [
            'general' => $this->settings->getGroup('general'),
            'otp' => $this->settings->getGroup('otp'),
            'system' => $this->settings->getGroup('system'),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settingsGroups
        ]);
    }

    public function update(Request $request, string $group)
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            $this->settings->set($key, $value, $group);
        }

        return back()->with('success', "Settings for '{$group}' updated successfully");
    }
}