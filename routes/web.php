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

Route::get('/clear-cache', function () {
    // Recreate missing Laravel storage directories if they were deleted
    $requiredDirs = [
        storage_path(),
        storage_path('app'),
        storage_path('app/public'),
        storage_path('app/public/reports'),
        storage_path('framework'),
        storage_path('framework/cache'),
        storage_path('framework/cache/data'),
        storage_path('framework/sessions'),
        storage_path('framework/views'),
        storage_path('logs'),
    ];

    $createdDirs = [];
    foreach ($requiredDirs as $dir) {
        if (!file_exists($dir)) {
            if (@mkdir($dir, 0755, true)) {
                $createdDirs[] = "Created: " . str_replace(base_path(), '', $dir);
            } else {
                $createdDirs[] = "Failed to create: " . str_replace(base_path(), '', $dir);
            }
        }
    }

    $results = [];
    $commands = ['config:clear', 'route:clear', 'view:clear', 'cache:clear'];
    foreach ($commands as $cmd) {
        try {
            \Illuminate\Support\Facades\Artisan::call($cmd);
            $results[] = "$cmd: Success";
        } catch (\Exception $e) {
            $results[] = "$cmd: Failed (" . $e->getMessage() . ")";
        }
    }
    
    $dirMsg = count($createdDirs) > 0 ? '<h4>Storage Folders Status:</h4>' . implode('<br>', $createdDirs) . '<br>' : '';
    
    return '<h3>Laravel Cache Clear</h3>' . $dirMsg . '<h4>Artisan Commands:</h4>' . implode('<br>', $results);
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

// Temporary debug route — remove after fixing image issues
Route::get('/debug-images', function () {
    $reports = \App\Models\EnvironmentalReport::whereNotNull('image_path')
        ->take(10)->get(['id', 'image_path']);

    // Find all image files in storage and public directories
    $foundFiles = [];
    $scanDirs = [
        storage_path(), // Scan the ENTIRE storage folder recursively
        public_path(),
        base_path('../public_html'),
    ];

    foreach ($scanDirs as $dir) {
        if (is_dir($dir)) {
            try {
                $di = new RecursiveDirectoryIterator($dir);
                foreach (new RecursiveIteratorIterator($di) as $filename => $file) {
                    if (in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        // Keep path relative to base_path for readability
                        $foundFiles[] = str_replace(base_path(), '', $filename);
                    }
                }
            } catch (\Exception $e) {
                $foundFiles[] = 'Error scanning ' . $dir . ': ' . $e->getMessage();
            }
        }
    }

    // Try to write a test file to see if upload directories are writeable
    $writeTests = [];
    $testDirs = [
        'storage' => storage_path(),
        'storage_app' => storage_path('app'),
        'storage_app_public' => storage_path('app/public'),
        'storage_app_public_reports' => storage_path('app/public/reports'),
        'public' => public_path(),
    ];

    foreach ($testDirs as $name => $path) {
        if (!file_exists($path)) {
            // Try to create it if it's public/reports
            if ($name === 'storage_app_public_reports') {
                @mkdir($path, 0755, true);
            }
        }
        $exists = file_exists($path);
        $writable = $exists ? is_writable($path) : false;
        
        $writeSuccessful = false;
        if ($writable) {
            $testFile = $path . '/test_write.txt';
            if (@file_put_contents($testFile, 'test') !== false) {
                $writeSuccessful = true;
                @unlink($testFile);
            }
        }
        
        $writeTests[$name] = [
            'path' => $path,
            'exists' => $exists,
            'writable' => $writable,
            'write_test_successful' => $writeSuccessful,
        ];
    }

    $results = $reports->map(function ($report) {
        $filename = basename($report->image_path);
        return [
            'id' => $report->id,
            'raw_image_path' => $report->image_path,
            'basename' => $filename,
            'generated_url' => $report->image_url,
            'file_exists_in_reports' => file_exists(storage_path('app/public/reports/' . $filename)),
            'file_exists_in_public' => file_exists(storage_path('app/public/' . $filename)),
            'file_exists_raw_path' => file_exists(storage_path('app/public/' . $report->image_path)),
            'public_reports_path_exists' => file_exists(public_path('storage/reports/' . $filename)),
            'public_path_direct_exists' => file_exists(public_path('reports/' . $filename)),
            'cpanel_public_html_exists' => file_exists(base_path('../public_html/storage/reports/' . $filename)),
        ];
    });

    return response()->json([
        'reports' => $results,
        'all_image_files_on_server' => $foundFiles,
        'diagnostics' => $writeTests,
        'paths' => [
            'base_path' => base_path(),
            'storage_path' => storage_path(),
            'public_path' => public_path(),
            'public_html_path' => realpath(base_path('../public_html')) ?: 'not found',
        ]
    ], 200, [], JSON_PRETTY_PRINT);
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
