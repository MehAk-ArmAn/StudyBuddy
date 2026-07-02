<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'real_name',
        'email',
        'email_verified_at',
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

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(StudyBuddyPointTransaction::class);
    }

    public function savedQuests(): HasMany
    {
        return $this->hasMany(SavedQuest::class);
    }

    public function hasVerifiedRole(): bool
    {
        return in_array($this->role_verification_status, ['verified', 'not_required'], true) || (bool) $this->is_admin;
    }

    public function isPowerRole(): bool
    {
        return in_array($this->role, ['parent', 'teacher', 'independent_learner', 'admin'], true) || (bool) $this->is_admin;
    }

    public function normalizedRole(): string
    {
        return match ($this->role) {
            'parent' => 'parent',
            'teacher' => 'teacher',
            'independent_learner' => 'independent_learner',
            'admin' => 'admin',
            default => 'student',
        };
    }

    public function canUseAdultControls(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return in_array($this->normalizedRole(), ['parent', 'teacher', 'independent_learner'], true)
            && ! empty($this->age_verified_at)
            && in_array($this->role_verification_status, ['verified', 'not_required', 'pending_child_approval', 'pending_admin_review'], true);
    }

    public function needsAdultVerification(): bool
    {
        $role = $this->role ?? 'student';
        $status = $this->adult_verification_status ?? 'not_required';

        return in_array($role, ['parent', 'teacher', 'independent_learner'], true)
            && !in_array($status, ['approved', 'not_required'], true);
    }

    public function needsRoleVerification(): bool
    {
        $role = $this->role ?? 'student';
        $status = $this->role_verification_status ?? 'not_required';

        return in_array($role, ['parent', 'teacher'], true)
            && !in_array($status, ['approved', 'not_required'], true);
    }

}