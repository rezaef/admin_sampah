<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@sampahdetector.app'],
            [
                'display_name' => 'Administrator',
                'username' => 'admin',
                'password' => 'Admin12345',
                'role' => 'admin',
                'provider' => 'local',
                'points_balance' => 0,
                'email_verified_at' => now(),
            ],
        );
    }
}
