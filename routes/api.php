<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubSectionController;
use App\Http\Controllers\User\HiringContractController;
use App\Http\Controllers\User\RunningContractController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes


// Authenticated shared routes
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 🛡️ Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('items', ItemController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subSections', SubSectionController::class);
    });

    // 👤 User-only routes
    Route::middleware('userauth')->group(function () {
        Route::apiResource('running-contracts', RunningContractController::class);
        Route::apiResource('hiring-contracts', HiringContractController::class);
    });
});
