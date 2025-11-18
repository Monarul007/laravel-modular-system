<?php

use Illuminate\Support\Facades\Route;
use Monarul007\LaravelModularSystem\Http\Controllers\ModuleController;

// Note: Don't include 'api' in prefix as Laravel adds it automatically
$prefix = config('modular-system.api_prefix', 'v1/admin');

Route::prefix($prefix)->group(function () {
    // Module Management
    Route::get('modules', [ModuleController::class, 'index']);
    Route::get('modules/{name}', [ModuleController::class, 'show']);
    Route::post('modules/enable', [ModuleController::class, 'enable']);
    Route::post('modules/disable', [ModuleController::class, 'disable']);
    Route::post('modules/upload', [ModuleController::class, 'upload']);
    Route::post('modules/uninstall', [ModuleController::class, 'uninstall']);
    Route::get('modules/download/{name}', [ModuleController::class, 'download']);
});
