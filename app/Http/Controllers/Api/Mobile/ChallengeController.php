<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $items = Challenge::query()
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
                    'reward_points' => $challenge->reward_points,
                    'starts_at' => optional($challenge->starts_at)->toISOString(),
                    'ends_at' => optional($challenge->ends_at)->toISOString(),
                    'progress' => $progress,
                    'is_completed' => $progress >= $challenge->target,
                ];
            })
            ->values();

        return response()->json(['data' => $items]);
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
