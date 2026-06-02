<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnvironmentalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location_name',
        'urgency',
        'status',
        'image_path',
        'reported_at',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'reported_at' => 'datetime',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        
        // If the path is already a full URL, return it directly
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return url('report-images/' . basename($this->image_path));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
