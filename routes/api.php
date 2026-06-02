<?php

use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\ChallengeController;
use App\Http\Controllers\Api\Mobile\ClassificationController;
use App\Http\Controllers\Api\Mobile\DashboardController;
use App\Http\Controllers\Api\Mobile\ReportController;
use App\Http\Controllers\Api\Mobile\RewardController;
use Illuminate\Support\Facades\Route;

Route::prefix('mobile')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/google', [AuthController::class, 'google']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me/email', [AuthController::class, 'updateEmail']);
        Route::put('/me/password', [AuthController::class, 'updatePassword']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::get('/dashboard', DashboardController::class);
        Route::get('/classifications', [ClassificationController::class, 'index']);
        Route::post('/classifications', [ClassificationController::class, 'store']);
        Route::delete('/classifications', [ClassificationController::class, 'destroyMany']);
        Route::delete('/classifications/{classification}', [ClassificationController::class, 'destroy']);

        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports', [ReportController::class, 'store']);
        Route::delete('/reports/{report}', [ReportController::class, 'destroy']);

        Route::get('/rewards', [RewardController::class, 'index']);
        Route::post('/rewards/{reward}/redeem', [RewardController::class, 'redeem']);
        Route::get('/challenges', [ChallengeController::class, 'index']);
    });
});
