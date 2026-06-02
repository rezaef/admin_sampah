<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Classification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            Classification::query()
                ->where('user_id', $request->user()->id)
                ->latest('detected_at')
                ->paginate(20)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'image_path' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'in:organik,anorganik,tidak_diketahui'],
            'confidence' => ['required', 'numeric', 'min:0', 'max:1'],
            'organic_score' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'anorganic_score' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'unknown_score' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'engine' => ['nullable', 'string', 'max:100'],
            'latency_ms' => ['nullable', 'integer', 'min:0'],
            'detected_at' => ['nullable', 'date'],
        ]);

        $classification = Classification::create([
            'user_id' => $request->user()->id,
            'image_path' => $data['image_path'] ?? null,
            'category' => $data['category'],
            'confidence' => $data['confidence'],
            'organic_score' => $data['organic_score'] ?? null,
            'anorganic_score' => $data['anorganic_score'] ?? null,
            'unknown_score' => $data['unknown_score'] ?? null,
            'engine' => $data['engine'] ?? 'custom-model',
            'latency_ms' => $data['latency_ms'] ?? 0,
            'detected_at' => $data['detected_at'] ?? now(),
        ]);

        $request->user()->increment('points_balance', 10);

        return response()->json([
            'message' => 'Riwayat klasifikasi berhasil disimpan.',
            'data' => $classification,
            'points_balance' => $request->user()->fresh()->points_balance,
        ], 201);
    }

    public function destroy(Request $request, Classification $classification): JsonResponse
    {
        abort_unless($classification->user_id === $request->user()->id, 404);

        $classification->delete();

        return response()->json(['message' => 'Riwayat klasifikasi berhasil dihapus.']);
    }

    public function destroyMany(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        $query = Classification::query()->where('user_id', $request->user()->id);

        if (is_array($ids) && count($ids) > 0) {
            $query->whereIn('id', $ids);
        }

        $deleted = $query->delete();

        return response()->json([
            'message' => 'Riwayat klasifikasi berhasil dihapus.',
            'deleted_count' => $deleted,
        ]);
    }
}
