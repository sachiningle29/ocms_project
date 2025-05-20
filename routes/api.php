<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\RunningContractController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['user'])->group(function () {
    
    // Running Contracts
    Route::prefix('running-contracts')->group(function () {
        Route::get('/list', [RunningContractController::class, 'index']);
        Route::post('/add', [RunningContractController::class, 'store']);
        Route::get('/view/{id}', [RunningContractController::class, 'show']);
        Route::put('/edit/{id}', [RunningContractController::class, 'update']);
        Route::delete('/delete/{id}', [RunningContractController::class, 'destroy']);
    });

    // Add other user-only routes here:
    // Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
    // Route::get('/user/profile', [UserProfileController::class, 'show']);
    // ... and more
});
