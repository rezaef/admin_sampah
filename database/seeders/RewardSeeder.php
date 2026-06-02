<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['title' => 'Voucher Belanja Rp10.000', 'description' => 'Voucher digital untuk pengguna aktif.', 'points_cost' => 120, 'stock' => 50, 'is_active' => true],
            ['title' => 'Totebag Daur Ulang', 'description' => 'Merchandise ramah lingkungan untuk penukaran poin.', 'points_cost' => 180, 'stock' => 25, 'is_active' => true],
            ['title' => 'Spotify Premium 1 Bulan', 'description' => 'No ads, playlist skena lestari lancar jaya.', 'points_cost' => 250, 'stock' => 30, 'is_active' => true],
            ['title' => 'Saldo Gopay Rp20.000', 'description' => 'Saldo jajan anti kering-kering club.', 'points_cost' => 200, 'stock' => 100, 'is_active' => true],
            ['title' => 'Kopi Susu Senja Rp15.000', 'description' => 'Voucher diskon nongkrong estetik bareng sirkelmu.', 'points_cost' => 150, 'stock' => 50, 'is_active' => true],
            ['title' => 'Reusable Tumbler Estetik', 'description' => 'Resmi join gerakan zero-waste penyelamat penyu.', 'points_cost' => 300, 'stock' => 15, 'is_active' => true],
        ] as $item) {
            Reward::query()->updateOrCreate(['title' => $item['title']], $item);
        }
    }
}
