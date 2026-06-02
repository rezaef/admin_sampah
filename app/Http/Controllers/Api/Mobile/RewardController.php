<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\RewardRedemption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Reward::query()->where('is_active', true)->orderBy('points_cost')->get(),
        ]);
    }

    public function redeem(Request $request, Reward $reward): JsonResponse
    {
        $user = $request->user();

        if (! $reward->is_active) {
            return response()->json(['message' => 'Reward tidak aktif.'], 422);
        }

        if ($reward->stock !== null && $reward->stock <= 0) {
            return response()->json(['message' => 'Stok reward habis.'], 422);
        }

        if ($user->points_balance < $reward->points_cost) {
            return response()->json(['message' => 'Poin tidak mencukupi.'], 422);
        }

        DB::transaction(function () use ($user, $reward) {
            RewardRedemption::create([
                'user_id' => $user->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_cost,
                'status' => 'Menunggu proses',
                'redeemed_at' => now(),
            ]);

            $user->decrement('points_balance', $reward->points_cost);

            if ($reward->stock !== null) {
                $reward->decrement('stock');
            }
        });

        return response()->json([
            'message' => 'Reward berhasil ditukar.',
            'points_balance' => $user->fresh()->points_balance,
        ]);
    }
}
