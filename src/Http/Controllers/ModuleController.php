<?php

namespace Monarul007\LaravelModularSystem\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Monarul007\LaravelModularSystem\Core\ModuleManager;
use Monarul007\LaravelModularSystem\Core\ApiResponse;

class ModuleController extends Controller
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function index(): JsonResponse
    {
        $modules = $this->moduleManager->getAllModules();
        return ApiResponse::success($modules, 'Modules retrieved successfully');
    }

    public function show(string $name): JsonResponse
    {
        if (!$this->moduleManager->moduleExists($name)) {
            return ApiResponse::error("Module '{$name}' does not exist", 404);
        }

        $config = $this->moduleManager->getModuleConfig($name);
        $config['enabled'] = $this->moduleManager->isModuleEnabled($name);

        return ApiResponse::success($config, 'Module details retrieved successfully');
    }

    public function enable(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string']);
        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return ApiResponse::error("Module '{$moduleName}' does not exist", 404);
        }

        if ($this->moduleManager->isModuleEnabled($moduleName)) {
            return ApiResponse::error("Module '{$moduleName}' is already enabled", 400);
        }

        if ($this->moduleManager->enableModule($moduleName)) {
            return ApiResponse::success(null, "Module '{$moduleName}' enabled successfully");
        }

        return ApiResponse::error("Failed to enable module '{$moduleName}'", 500);
    }

    public function disable(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string']);
        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return ApiResponse::error("Module '{$moduleName}' does not exist", 404);
        }

        if (!$this->moduleManager->isModuleEnabled($moduleName)) {
            return ApiResponse::error("Module '{$moduleName}' is already disabled", 400);
        }

        if ($this->moduleManager->disableModule($moduleName)) {
            return ApiResponse::success(null, "Module '{$moduleName}' disabled successfully");
        }

        return ApiResponse::error("Failed to disable module '{$moduleName}'", 500);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:' . config('modular-system.upload_max_size', 2048)
        ]);

        $file = $request->file('file');
        $result = $this->moduleManager->installModuleFromZip($file->getRealPath());

        if ($result['success']) {
            return ApiResponse::success($result['module'], $result['message']);
        }

        return ApiResponse::error($result['message'], 400);
    }

    public function uninstall(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string']);
        $moduleName = $request->name;

        if (!$this->moduleManager->moduleExists($moduleName)) {
            return ApiResponse::error("Module '{$moduleName}' does not exist", 404);
        }

        if ($this->moduleManager->uninstallModule($moduleName)) {
            return ApiResponse::success(null, "Module '{$moduleName}' uninstalled successfully");
        }

        return ApiResponse::error("Failed to uninstall module '{$moduleName}'", 500);
    }

    public function download(string $name)
    {
        if (!$this->moduleManager->moduleExists($name)) {
            return ApiResponse::error("Module '{$name}' does not exist", 404);
        }

        $zipPath = $this->moduleManager->createModuleZip($name);

        if (!$zipPath) {
            return ApiResponse::error("Failed to create ZIP file for module '{$name}'", 500);
        }

        return response()->download($zipPath, "{$name}.zip")->deleteFileAfterSend(true);
    }
}
