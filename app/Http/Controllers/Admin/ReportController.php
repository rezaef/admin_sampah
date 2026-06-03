<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use App\Models\EnvironmentalReport;
use App\Models\RewardRedemption;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $query = EnvironmentalReport::query()->with('user');

        $reportsTinggi = (clone $query)->where('urgency', 'Tinggi')->latest('reported_at')->get();
        $reportsSedang = (clone $query)->where('urgency', 'Sedang')->latest('reported_at')->get();
        $reportsRendah = (clone $query)->where('urgency', 'Rendah')->latest('reported_at')->get();

        $totalCount = EnvironmentalReport::query()->count();

        return view('admin.reports.index', compact('reportsTinggi', 'reportsSedang', 'reportsRendah', 'totalCount'));
    }

    public function updateStatus(Request $request, EnvironmentalReport $report): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['Menunggu verifikasi', 'Diproses', 'Selesai'])],
        ]);

        $report->update(['status' => $data['status']]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Status berhasil diperbarui.', 'status' => $data['status']]);
        }

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function destroy(EnvironmentalReport $report): RedirectResponse
    {
        if ($report->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($report->image_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($report->image_path);
        }

        $report->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * JSON endpoint for realtime dashboard polling.
     */
    public function ajaxStats(): JsonResponse
    {
        $recentReports = EnvironmentalReport::query()
            ->with('user')
            ->latest('id')
            ->take(5)
            ->get()
            ->map(function ($report) {
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'location_name' => $report->location_name,
                    'urgency' => $report->urgency,
                    'status' => $report->status,
                    'image_url' => $report->image_url,
                    'user_name' => $report->user?->display_name ?? 'Pengguna',
                    'time_formatted' => optional($report->reported_at)->format('d/m H:i') ?? $report->created_at->format('d/m H:i'),
                ];
            });

        return response()->json([
            'total_users'            => User::query()->where('role', 'user')->count(),
            'total_admins'           => User::query()->where('role', 'admin')->count(),
            'total_classifications'  => Classification::query()->count(),
            'organic_count'          => Classification::query()->where('category', 'organik')->count(),
            'anorganic_count'        => Classification::query()->where('category', 'anorganik')->count(),
            'total_reports'          => EnvironmentalReport::query()->count(),
            'pending_reports'        => EnvironmentalReport::query()->where('status', 'Menunggu verifikasi')->count(),
            'total_redemptions'      => RewardRedemption::query()->count(),
            'organic_avg_confidence' => Classification::query()->where('category', 'organik')->avg('confidence') ?? 0,
            'anorganic_avg_confidence' => Classification::query()->where('category', 'anorganik')->avg('confidence') ?? 0,
            'other_avg_confidence' => Classification::query()->whereNotIn('category', ['organik', 'anorganik'])->avg('confidence') ?? 0,
            'high_urgency_count'     => EnvironmentalReport::query()->where('urgency', 'Tinggi')->where('status', '!=', 'Selesai')->count(),
            'medium_urgency_count'   => EnvironmentalReport::query()->where('urgency', 'Sedang')->where('status', '!=', 'Selesai')->count(),
            'low_urgency_count'      => EnvironmentalReport::query()->where('urgency', 'Rendah')->where('status', '!=', 'Selesai')->count(),
            'recent_reports'         => $recentReports,
        ]);
    }
}
