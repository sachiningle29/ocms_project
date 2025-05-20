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

Route::middleware(['user'])->prefix('running-contracts')->group(function () {
    Route::get('/list', [RunningContractController::class, 'index']);           // List all contracts
    Route::post('/add', [RunningContractController::class, 'store']);           // Add new contract
    Route::get('/view/{id}', [RunningContractController::class, 'show']);       // View single contract
    Route::put('/edit/{id}', [RunningContractController::class, 'update']);     // Edit existing contract
    Route::delete('/delete/{id}', [RunningContractController::class, 'destroy']); // Delete contract
});

Route::middleware(['user'])->prefix('hiring-contracts')->group(function () {
    Route::get('/list', [RunningContractController::class, 'index']);           // List all contracts
    Route::post('/add', [RunningContractController::class, 'store']);           // Add new contract
    Route::get('/view/{id}', [RunningContractController::class, 'show']);       // View single contract
    Route::put('/edit/{id}', [RunningContractController::class, 'update']);     // Edit existing contract
    Route::delete('/delete/{id}', [RunningContractController::class, 'destroy']); // Delete contract
});

