<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ChallengeController;
use App\Http\Controllers\Admin\ClassificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\RewardRedemptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');



// Fallback route to serve storage files if symlink is not possible
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
})->where('path', '.*');

// Directly serve report images via Laravel to bypass server-level symlink restrictions
Route::get('/report-images/{filename}', function ($filename) {
    $filename = basename($filename);
    
    // Try multiple possible storage locations including typical cPanel structures
    $possiblePaths = [
        storage_path('app/public/reports/' . $filename),
        storage_path('app/public/' . $filename),
        public_path('storage/reports/' . $filename),
        public_path('storage/' . $filename),
        public_path('reports/' . $filename),
        public_path($filename),
        base_path('../public_html/storage/reports/' . $filename),
        base_path('../public_html/storage/' . $filename),
        base_path('../public_html/reports/' . $filename),
    ];

    foreach ($possiblePaths as $filePath) {
        if (file_exists($filePath)) {
            return response()->file($filePath, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }
    }

    abort(404);
})->where('filename', '.*');


Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    });

    Route::middleware(['auth', EnsureAdmin::class])->group(function () {
        Route::get('/', DashboardController::class)->name('admin.dashboard');
        Route::get('/stats', [ReportController::class, 'ajaxStats'])->name('admin.stats');
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::get('/classifications', [ClassificationController::class, 'index'])->name('admin.classifications.index');
        Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::patch('/reports/{report}/status', [ReportController::class, 'updateStatus'])->name('admin.reports.status');
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');

        Route::get('/rewards', [RewardController::class, 'index'])->name('admin.rewards.index');
        Route::get('/rewards/create', [RewardController::class, 'create'])->name('admin.rewards.create');
        Route::post('/rewards', [RewardController::class, 'store'])->name('admin.rewards.store');
        Route::get('/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('admin.rewards.edit');
        Route::put('/rewards/{reward}', [RewardController::class, 'update'])->name('admin.rewards.update');
        Route::delete('/rewards/{reward}', [RewardController::class, 'destroy'])->name('admin.rewards.destroy');

        Route::get('/redemptions', [RewardRedemptionController::class, 'index'])->name('admin.redemptions.index');
        Route::patch('/redemptions/{redemption}/status', [RewardRedemptionController::class, 'updateStatus'])->name('admin.redemptions.status');

        Route::get('/challenges', [ChallengeController::class, 'index'])->name('admin.challenges.index');
        Route::get('/challenges/create', [ChallengeController::class, 'create'])->name('admin.challenges.create');
        Route::post('/challenges', [ChallengeController::class, 'store'])->name('admin.challenges.store');
        Route::get('/challenges/{challenge}/edit', [ChallengeController::class, 'edit'])->name('admin.challenges.edit');
        Route::put('/challenges/{challenge}', [ChallengeController::class, 'update'])->name('admin.challenges.update');
        Route::delete('/challenges/{challenge}', [ChallengeController::class, 'destroy'])->name('admin.challenges.destroy');


    });
});
