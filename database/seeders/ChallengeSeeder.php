<?php

namespace Database\Seeders;

use App\Models\Challenge;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['title' => 'Scan 5 Sampah Minggu Ini', 'description' => 'Lakukan lima klasifikasi valid dalam satu minggu.', 'target' => 5, 'reward_points' => 50, 'starts_at' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'is_active' => true],
            ['title' => 'Kirim 2 Laporan Lingkungan', 'description' => 'Bantu pelaporan titik sampah di area sekitar.', 'target' => 2, 'reward_points' => 40, 'starts_at' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'is_active' => true],
            ['title' => 'Anti Mager-Mager Club (Scan 3x)', 'description' => 'Jangan mager! Deteksi 3 sampah organik/anorganik di sekitar kamarmu.', 'target' => 3, 'reward_points' => 30, 'starts_at' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'is_active' => true],
            ['title' => 'Skena Lestari (Scan 7x)', 'description' => 'Pilah sampah demi vibes estetik ramah lingkungan. Lakukan 7 scan valid.', 'target' => 7, 'reward_points' => 75, 'starts_at' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'is_active' => true],
            ['title' => 'Spill Titik Sampah Terbengkalai', 'description' => 'Bantu bumi! Kirim 1 laporan titik tumpukan sampah liar di sekitarmu.', 'target' => 1, 'reward_points' => 25, 'starts_at' => now()->startOfWeek(), 'ends_at' => now()->endOfWeek(), 'is_active' => true],
        ] as $item) {
            Challenge::query()->updateOrCreate(['title' => $item['title']], $item);
        }
    }
}
