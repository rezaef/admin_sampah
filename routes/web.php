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

Route::get('/link-storage', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');
    
    if (file_exists($link) || is_link($link)) {
        return 'Tautan storage sudah ada di server. Jika gambar tidak muncul, silakan akses /delete-storage-link terlebih dahulu untuk membersihkannya.';
    }
    
    if (!function_exists('symlink')) {
        return 'Fungsi PHP symlink() dinonaktifkan oleh penyedia hosting Anda. Silakan akses /delete-storage-link agar sistem menggunakan rute fallback otomatis untuk menampilkan gambar.';
    }
    
    try {
        if (symlink($target, $link)) {
            return 'Tautan storage berhasil dibuat via PHP symlink!';
        }
    } catch (\Exception $e) {
        return 'Gagal membuat symlink: ' . $e->getMessage() . '. Silakan jalankan /delete-storage-link untuk mengaktifkan fallback.';
    }
    
    return 'Gagal membuat tautan storage.';
});

Route::get('/delete-storage-link', function () {
    $link = public_path('storage');
    
    if (!file_exists($link) && !is_link($link)) {
        return 'Tidak ada folder atau tautan storage yang perlu dihapus.';
    }
    
    try {
        // Force delete if it is a symlink or file
        if (is_link($link) || file_exists($link)) {
            // PHP native unlink handles symlinks
            @unlink($link);
        }
        
        if (file_exists($link) && is_dir($link)) {
            @rmdir($link);
        }
        
        if (!file_exists($link) && !is_link($link)) {
            return 'Folder/tautan storage lama berhasil dihapus! Sekarang rute fallback otomatis aktif untuk menampilkan gambar.';
        }
        
        return 'Gagal menghapus folder storage secara otomatis. Silakan hapus folder "public/storage" secara manual melalui File Manager di cPanel/hosting Anda.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

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
    $filePath = storage_path('app/public/reports/' . $filename);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath);
});

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

        // Route untuk menjalankan seeder via browser (Aman karena di dalam middleware admin auth)
        Route::get('/run-seeders', function () {
            try {
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'RewardSeeder']);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'ChallengeSeeder']);
                return 'Seeders (RewardSeeder & ChallengeSeeder) berhasil dijalankan via web!';
            } catch (\Exception $e) {
                return 'Gagal menjalankan seeder: ' . $e->getMessage();
            }
        });
    });
});
