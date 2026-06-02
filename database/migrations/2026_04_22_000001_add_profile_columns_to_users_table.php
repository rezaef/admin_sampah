<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('id');
            $table->string('username')->unique()->after('display_name');
            $table->string('role')->default('user')->after('password');
            $table->string('provider')->default('local')->after('role');
            $table->string('google_id')->nullable()->unique()->after('provider');
            $table->string('avatar_url')->nullable()->after('google_id');
            $table->unsignedInteger('points_balance')->default(0)->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'username', 'role', 'provider', 'google_id', 'avatar_url', 'points_balance']);
        });
    }
};
