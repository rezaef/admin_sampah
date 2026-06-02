<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'display_name',
        'username',
        'email',
        'password',
        'role',
        'provider',
        'google_id',
        'avatar_url',
        'points_balance',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function classifications(): HasMany
    {
        return $this->hasMany(Classification::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(EnvironmentalReport::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(RewardRedemption::class);
    }

    public function challengeProgress(): HasMany
    {
        return $this->hasMany(ChallengeProgress::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
