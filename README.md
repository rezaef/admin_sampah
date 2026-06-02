# Backend Laravel + Web Admin Sampah Detector

Paket ini adalah source overlay untuk project Laravel baru.

## Cakupan
- API mobile user
- Web admin berbasis Blade
- Login admin dengan username/email + password
- Login mobile lokal dengan username/email + password
- Manajemen reward
- Manajemen challenge
- Moderasi laporan lingkungan
- Statistik dashboard admin
- Struktur data untuk riwayat klasifikasi dan poin

## Cara pakai
1. Buat project Laravel baru.
2. Pasang Sanctum.
3. Salin semua file dari paket ini ke project Laravel.
4. Jalankan migrasi dan seeder.
5. Login ke admin panel.

## Dependensi
- Laravel
- Laravel Sanctum

## Langkah setup
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Akun admin default
- Email: `admin@sampahdetector.app`
- Username: `admin`
- Password: `Admin12345`

## URL penting
- Admin login: `/admin/login`
- Admin dashboard: `/admin`
- API mobile: `/api/mobile/*`

## Endpoint mobile
- `POST /api/mobile/auth/register`
- `POST /api/mobile/auth/login`
- `POST /api/mobile/auth/logout`
- `GET /api/mobile/me`
- `PUT /api/mobile/me/email`
- `PUT /api/mobile/me/password`
- `GET /api/mobile/dashboard`
- `GET /api/mobile/rewards`
- `GET /api/mobile/challenges`
- `GET /api/mobile/classifications`
- `POST /api/mobile/classifications`
- `GET /api/mobile/reports`
- `POST /api/mobile/reports`

## Catatan Google login
Field database untuk Google sudah disiapkan (`provider`, `google_id`, `avatar_url`), tetapi verifikasi ID token Google belum diaktifkan di paket ini. Integrasi verifikasi token dapat ditambahkan pada tahap berikutnya.
