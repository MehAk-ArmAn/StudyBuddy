<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StudyBuddyMiniAppPlatform extends Model
{
    protected $table = 'studybuddy_mini_app_platforms';

    protected $fillable = [
        'slug',
        'name',
        'category',
        'tagline',
        'description',
        'long_description',
        'hero_heading',
        'preview_text',
        'status',
        'icon',
        'image_url',
        'accent',
        'age_range',
        'role_scope',
        'learning_tags',
        'learning_outcomes',
        'how_it_works',
        'screenshot_urls',
        'safety_note',
        'locked_preview_note',
        'platform_notes',
        'detail_cta_label',
        'web_play_url',
        'ios_url',
        'android_url',
        'windows_url',
        'mac_url',
        'support_url',
        'points_reward',
        'estimated_minutes',
        'is_web_enabled',
        'is_download_enabled',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'role_scope' => 'array',
        'learning_tags' => 'array',
        'learning_outcomes' => 'array',
        'how_it_works' => 'array',
        'screenshot_urls' => 'array',
        'is_web_enabled' => 'boolean',
        'is_download_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'points_reward' => 'integer',
        'estimated_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');
    }

    public function scopeForRole(Builder $query, ?string $role): Builder
    {
        if (! $role || $role === 'all') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($role): void {
            $subQuery->whereNull('role_scope')
                ->orWhereJsonContains('role_scope', $role);
        });
    }

    public function rolesList(): array
    {
        $roles = $this->role_scope ?: ['student', 'parent', 'teacher', 'independent_learner'];

        return collect($roles)
            ->filter()
            ->map(fn ($role) => match ($role) {
                'student' => 'Student',
                'parent' => 'Parent',
                'teacher' => 'Teacher',
                'independent_learner' => 'Independent Learner',
                default => Str::headline((string) $role),
            })
            ->values()
            ->all();
    }

    public function learningTagsList(): array
    {
        return collect($this->learning_tags ?: [])
            ->filter()
            ->map(fn ($tag) => Str::headline((string) $tag))
            ->values()
            ->all();
    }

    public function outcomesList(): array
    {
        return collect($this->learning_outcomes ?: [])
            ->filter()
            ->values()
            ->all();
    }

    public function howItWorksList(): array
    {
        return collect($this->how_it_works ?: [])
            ->filter()
            ->values()
            ->all();
    }

    public function screenshotList(): array
    {
        return collect($this->screenshot_urls ?: [])
            ->filter()
            ->values()
            ->all();
    }

    public function statusLabel(): string
    {
        return Str::headline((string) $this->status);
    }

    public function detailUrl(): string
    {
        return route('studybuddy.apps.show', $this->slug);
    }
}
