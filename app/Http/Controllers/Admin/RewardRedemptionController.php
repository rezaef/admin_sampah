<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardRedemption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RewardRedemptionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = RewardRedemption::query()
            ->with(['user', 'reward'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $redemptions = $query->paginate(20)->withQueryString();

        return view('admin.redemptions.index', compact('redemptions', 'status'));
    }

    public function updateStatus(Request $request, RewardRedemption $redemption): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:Diproses,Selesai,Ditolak'],
        ]);

        $status = $data['status'];

        if (in_array($redemption->status, ['Selesai', 'Ditolak'])) {
            return back()->with('error', 'Status penukaran ini sudah final dan tidak dapat diubah.');
        }

        try {
            DB::transaction(function () use ($redemption, $status) {
                // If rejected, refund points to user
                if ($status === 'Ditolak' && $redemption->status !== 'Ditolak') {
                    $redemption->user->increment('points_balance', $redemption->points_spent);
                }

                $redemption->update(['status' => $status]);
            });

            return back()->with('success', 'Status penukaran berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}
