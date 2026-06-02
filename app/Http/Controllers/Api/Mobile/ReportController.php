<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\EnvironmentalReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reports = EnvironmentalReport::query()
            ->where('user_id', $request->user()->id)
            ->latest('reported_at')
            ->paginate(20);

        return response()->json($reports);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'         => ['required', 'string', 'max:150'],
            'description'   => ['required', 'string'],
            'category'      => ['required', 'string', 'max:100'],
            'location_name' => ['required', 'string', 'max:150'],
            'urgency'       => ['required', 'in:Rendah,Sedang,Tinggi'],
            'image'         => ['nullable', 'image', 'max:8192'], // file upload (multipart)
            'image_path'    => ['nullable', 'string', 'max:255'], // fallback: URL string
            'reported_at'   => ['nullable', 'date'],
        ]);

        // Handle uploaded image file
        $storedPath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $storedPath = $request->file('image')->store('reports', 'public');
        } elseif (!empty($data['image_path'])) {
            // Backwards-compatible: accept URL string if no file uploaded
            $storedPath = $data['image_path'];
        }

        $report = EnvironmentalReport::create([
            'user_id'       => $request->user()->id,
            'title'         => $data['title'],
            'description'   => $data['description'],
            'category'      => $data['category'],
            'location_name' => $data['location_name'],
            'urgency'       => $data['urgency'],
            'status'        => 'Menunggu verifikasi',
            'image_path'    => $storedPath,
            'reported_at'   => $data['reported_at'] ?? now(),
        ]);

        $request->user()->increment('points_balance', 15);

        return response()->json([
            'message'        => 'Laporan berhasil dikirim.',
            'data'           => $report,
            'points_balance' => $request->user()->fresh()->points_balance,
        ], 201);
    }

    public function destroy(Request $request, EnvironmentalReport $report): JsonResponse
    {
        abort_unless((int) $report->user_id === (int) $request->user()->id, 404);

        // Delete image from storage if it exists
        if ($report->image_path && Storage::disk('public')->exists($report->image_path)) {
            Storage::disk('public')->delete($report->image_path);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus.']);
    }
}
