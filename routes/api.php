<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\User\HiringContractController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubSectionController;
use App\Http\Controllers\User\RunningContractController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('items', ItemController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('sections', SectionController::class);
Route::apiResource('sub_sections', SubSectionController::class);

// Public routes
Route::post('/login/admin', [AuthController::class, 'Adminlogin']);
Route::post('/login/user', [AuthController::class, 'Userlogin']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::middleware('admin')->group(function () {
    });
});


// Running Contracts
Route::apiResource('running-contracts', RunningContractController::class);

Route::apiResource('hiring-contracts', HiringContractController::class);

