<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    protected $fillable = [
        'name',
        'real_name',
        'email',
        'child_emails',
        'role_profile',
        'email_verified_at',
        'password',
        'role',
        'avatar_style',
        'profile_photo_path',
        'learning_stage',
        'cosmic_points',
        'is_admin',
        'studybuddy_test_account',
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
        'adult_verification_status',
        'adult_verification_consent_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'child_emails' => 'array',
            'role_profile' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'cosmic_points' => 'integer',
            'is_admin' => 'boolean',
            'studybuddy_test_account' => 'boolean',
            'date_of_birth' => 'date',
            'age_verified_at' => 'datetime',
            'role_verified_at' => 'datetime',
            'safeguarding_agreed_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
            'adult_verification_consent_at' => 'datetime',
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

    public function schoolMemberships(): HasMany
    {
        return $this->hasMany(SchoolOrganizationMember::class, 'user_id');
    }

    public function isAdult(): bool
    {
        return $this->date_of_birth !== null && $this->date_of_birth->age >= 18;
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

    public function hasVerifiedRole(): bool
    {
        return in_array($this->role_verification_status, ['verified', 'approved', 'not_required'], true) || (bool) $this->is_admin;
    }

    public function isPowerRole(): bool
    {
        return in_array($this->normalizedRole(), ['parent', 'teacher', 'admin'], true)
            || ($this->normalizedRole() === 'independent_learner' && $this->isAdult())
            || (bool) $this->is_admin;
    }

    public function canUseAdultControls(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        return $this->isAdult()
            && in_array($this->normalizedRole(), ['parent', 'teacher', 'independent_learner'], true)
            && in_array($this->role_verification_status, ['verified', 'approved'], true)
            && in_array($this->adult_verification_status, ['verified', 'approved'], true);
    }

    public function needsAdultVerification(): bool
    {
        $role = $this->role ?? 'student';
        $status = $this->adult_verification_status ?? 'not_required';

        return (in_array($role, ['parent', 'teacher'], true)
            || ($role === 'independent_learner' && $this->isAdult()))
            && !in_array($status, ['approved', 'verified', 'not_required'], true);
    }

    public function needsRoleVerification(): bool
    {
        $role = $this->role ?? 'student';
        $status = $this->role_verification_status ?? 'not_required';

        return (in_array($role, ['parent', 'teacher'], true)
            || ($role === 'independent_learner' && $this->isAdult()))
            && !in_array($status, ['approved', 'verified', 'not_required'], true);
    }
}
