<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Classification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $aggregate = Classification::query()
            ->where('user_id', $user->id)
            ->selectRaw('COUNT(*) as total_scans')
            ->selectRaw("SUM(CASE WHEN category = 'organik' THEN 1 ELSE 0 END) as organic_count")
            ->selectRaw("SUM(CASE WHEN category = 'anorganik' THEN 1 ELSE 0 END) as anorganic_count")
            ->selectRaw("AVG(CASE WHEN category = 'organik' THEN confidence END) as organic_avg_confidence")
            ->selectRaw("AVG(CASE WHEN category = 'anorganik' THEN confidence END) as anorganic_avg_confidence")
            ->first();

        $activeChallenges = Challenge::query()
            ->where('is_active', true)
            ->orderBy('starts_at')
            ->get()
            ->map(function (Challenge $challenge) use ($user) {
                $progress = $this->resolveProgress($challenge, $user);

                return [
                    'id' => $challenge->id,
                    'title' => $challenge->title,
                    'description' => $challenge->description,
                    'target' => $challenge->target,
                    'progress' => $progress,
                    'reward_points' => $challenge->reward_points,
                    'is_completed' => $progress >= $challenge->target,
                ];
            })
            ->values();

        return response()->json([
            'points' => (int) $user->points_balance,
            'total_scans' => (int) ($aggregate->total_scans ?? 0),
            'organic_count' => (int) ($aggregate->organic_count ?? 0),
            'anorganic_count' => (int) ($aggregate->anorganic_count ?? 0),
            'organic_avg_confidence' => $aggregate->organic_avg_confidence ? round($aggregate->organic_avg_confidence * 100, 1) : null,
            'anorganic_avg_confidence' => $aggregate->anorganic_avg_confidence ? round($aggregate->anorganic_avg_confidence * 100, 1) : null,
            'report_count' => $user->reports()->count(),
            'completed_challenges' => $activeChallenges->where('is_completed', true)->count(),
            'active_challenges' => $activeChallenges,
        ]);
    }

    private function resolveProgress(Challenge $challenge, User $user): int
    {
        $content = str($challenge->title . ' ' . $challenge->description)->lower()->value();

        if (str_contains($content, 'lapor')) {
            return $user->reports()->count();
        }

        return $user->classifications()->count();
    }
}
