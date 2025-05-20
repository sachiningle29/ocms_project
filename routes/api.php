<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\RunningContractController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/login', [AuthController::class, 'login']);

// Public routes
Route::post('/login/admin', [AuthController::class, 'Adminlogin']);
Route::post('/login/user', [AuthController::class, 'Userlogin']);



// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // Add admin routes here later
    });
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

Route::middleware(['user'])->prefix('hiring-contracts')->group(function () {
    Route::get('/list', [RunningContractController::class, 'index']);           // List all contracts
    Route::post('/add', [RunningContractController::class, 'store']);           // Add new contract
    Route::get('/view/{id}', [RunningContractController::class, 'show']);       // View single contract
    Route::put('/edit/{id}', [RunningContractController::class, 'update']);     // Edit existing contract
    Route::delete('/delete/{id}', [RunningContractController::class, 'destroy']); // Delete contract
});

