<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BlueprintController;
use App\Http\Middleware\RateLimitBlueprintCreation;
use App\Http\Controllers\Api\AuthController;


Route::prefix('v1')->group(function () {
    Route::get('/blueprints', [BlueprintController::class, 'index'])->name('blueprints.index');
    Route::post('/blueprints', [BlueprintController::class, 'store'])
        ->middleware(RateLimitBlueprintCreation::class)
        ->name('blueprints.store');
});
