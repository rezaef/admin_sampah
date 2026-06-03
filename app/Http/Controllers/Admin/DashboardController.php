<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\EnvironmentalReport;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'total_users' => User::query()->where('role', 'user')->count(),
            'total_admins' => User::query()->where('role', 'admin')->count(),
            'total_classifications' => Classification::query()->count(),
            'organic_count' => Classification::query()->where('category', 'organik')->count(),
            'anorganic_count' => Classification::query()->where('category', 'anorganik')->count(),
            'total_reports' => EnvironmentalReport::query()->count(),
            'pending_reports' => EnvironmentalReport::query()->where('status', 'Menunggu verifikasi')->count(),
            'total_redemptions' => RewardRedemption::query()->count(),
            'organic_avg_confidence' => Classification::query()->where('category', 'organik')->avg('confidence') ?? 0,
            'anorganic_avg_confidence' => Classification::query()->where('category', 'anorganik')->avg('confidence') ?? 0,
            'other_avg_confidence' => Classification::query()->whereNotIn('category', ['organik', 'anorganik'])->avg('confidence') ?? 0,
            'high_urgency_count' => EnvironmentalReport::query()->where('urgency', 'Tinggi')->where('status', '!=', 'Selesai')->count(),
            'medium_urgency_count' => EnvironmentalReport::query()->where('urgency', 'Sedang')->where('status', '!=', 'Selesai')->count(),
            'low_urgency_count' => EnvironmentalReport::query()->where('urgency', 'Rendah')->where('status', '!=', 'Selesai')->count(),
        ];

        $recentReports = EnvironmentalReport::query()->with('user')->latest('id')->take(5)->get();
        $recentClassifications = Classification::query()->with('user')->latest('detected_at')->take(8)->get();

        return view('admin.dashboard.index', compact('stats', 'recentReports', 'recentClassifications'));
    }
}
