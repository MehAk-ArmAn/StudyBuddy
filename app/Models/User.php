<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'real_name',
        'email',
        'password',
        'role',
        'avatar_style',
        'learning_stage',
        'cosmic_points',
        'is_admin',
        'date_of_birth',
        'country',
        'guardian_email',
        'organization_name',
        'organization_email',
        'position_title',
        'role_verification_status',
        'role_verification_notes',
        'age_verified_at',
        'role_verified_at',
        'safeguarding_agreed_at',
        'verification_submitted_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cosmic_points' => 'integer',
            'is_admin' => 'boolean',
            'date_of_birth' => 'date',
            'age_verified_at' => 'datetime',
            'role_verified_at' => 'datetime',
            'safeguarding_agreed_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
        ];
    }

    public function requestedConnections(): HasMany
    {
        return $this->hasMany(AccountConnection::class, 'requester_id');
    }

    public function receivedConnections(): HasMany
    {
        return $this->hasMany(AccountConnection::class, 'target_id');
    }

    public function hasVerifiedRole(): bool
    {
        return in_array($this->role_verification_status, ['verified', 'not_required'], true);
    }

    public function isPowerRole(): bool
    {
        return in_array($this->role, ['parent', 'teacher', 'independent_learner', 'admin'], true);
    }
}
