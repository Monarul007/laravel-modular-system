<?php

use Illuminate\Support\Facades\Route;
use Modules\TestModule\Http\Controllers\WelcomeController;

Route::prefix('testmodule')->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('/blade', [WelcomeController::class, 'blade']);
});
