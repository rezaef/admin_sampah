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
        $reports = EnvironmentalReport::query()->with('user')->latest('reported_at')->paginate(20);

        return view('admin.reports.index', compact('reports'));
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
        ]);
    }
}
