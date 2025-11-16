<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\SettingsManager;
use Monarul007\LaravelModularSystem\Core\ApiResponse;

class SettingsController extends Controller
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    public function getGroup(string $group): JsonResponse
    {
        $settings = $this->settings->getGroup($group);
        return ApiResponse::success($settings, "Settings for group '{$group}' retrieved successfully");
    }

    public function updateGroup(Request $request, string $group): JsonResponse
    {
        $settings = $request->all();

        foreach ($settings as $key => $value) {
            $this->settings->set($key, $value, $group);
        }

        return ApiResponse::success(null, "Settings for group '{$group}' updated successfully");
    }

    public function get(string $key): JsonResponse
    {
        $value = $this->settings->get($key);
        return ApiResponse::success(['key' => $key, 'value' => $value], 'Setting retrieved successfully');
    }

    public function set(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required',
            'group' => 'string'
        ]);

        $this->settings->set(
            $request->key,
            $request->value,
            $request->group ?? 'general'
        );

        return ApiResponse::success(null, 'Setting updated successfully');
    }
}
