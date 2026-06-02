<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'category',
        'confidence',
        'organic_score',
        'anorganic_score',
        'unknown_score',
        'engine',
        'latency_ms',
        'detected_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'organic_score' => 'float',
            'anorganic_score' => 'float',
            'unknown_score' => 'float',
            'detected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
