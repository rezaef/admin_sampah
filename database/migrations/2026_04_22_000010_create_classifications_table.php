<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->enum('category', ['organik', 'anorganik', 'tidak_diketahui']);
            $table->decimal('confidence', 5, 4);
            $table->decimal('organic_score', 5, 4)->nullable();
            $table->decimal('anorganic_score', 5, 4)->nullable();
            $table->decimal('unknown_score', 5, 4)->nullable();
            $table->string('engine')->default('custom-model');
            $table->unsignedInteger('latency_ms')->default(0);
            $table->timestamp('detected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
