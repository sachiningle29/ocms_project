<?php

use App\Http\Controllers\Admin\AdminHiringController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubSectionController;
use App\Http\Controllers\User\HiringContractController;
use App\Http\Controllers\User\RunningContractController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login/admin', [AuthController::class, 'Adminlogin']);
Route::post('/login/user', [AuthController::class, 'Userlogin']);

// Authenticated shared routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Common routes
    Route::get('/get-user-details', [UserController::class, 'getUserDetails']);

    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::get('/notifications/all', [NotificationController::class, 'all']); 
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::get('/contracts/{id}', [AdminHiringController::class, 'show']); 


    //  Admin-only routes
    Route::middleware('admin')->group(function () {
        Route::apiResource('items', ItemController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('sections', SectionController::class);
        Route::apiResource('subSections', SubSectionController::class);

        // Admin-Dashboard
        Route::post('/adminDasboard/getFilteredCases', [AdminDashboardController::class, 'getFilteredCasesWithDurations']);

        //Admin HiringContracts(procurement case)
        Route::get('/admin/hiring-contracts/sections', [HiringContractController::class, 'SectionList']);
        Route::apiResource('/admin/hiring-contracts', AdminHiringController::class);
    });

    //  User-only routes
    Route::middleware('userauth')->group(function () {
        Route::apiResource('running-contracts', RunningContractController::class);

        //Procurement case
        Route::get('/hiring-contracts/section', [HiringContractController::class, 'getCurrentUserSection']);
        Route::get('/hiring-contracts/sections', [HiringContractController::class, 'SectionList']);
        Route::apiResource('hiring-contracts', HiringContractController::class);

        // dashboard
        Route::get('/userDasboard/getSection/{status?}', [UserDashboardController::class, 'GetSection']);
        Route::get('/userDasboard/getdeliverables/{status?}', [UserDashboardController::class, 'GetDeliverables']);
        Route::get('/userDasboard/stage-durations', [UserDashboardController::class, 'GetStageToStageDurations']);
    });
});
