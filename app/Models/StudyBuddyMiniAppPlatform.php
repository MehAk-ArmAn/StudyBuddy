<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyMiniAppPlatform extends Model
{
    protected $table = 'studybuddy_mini_app_platforms';

    protected $fillable = [
        'slug', 'name', 'category', 'tagline', 'description', 'status', 'icon', 'accent', 'hero_image',
        'preview_text', 'safety_note', 'web_play_url', 'ios_url', 'android_url', 'windows_url', 'mac_url', 'support_url',
        'points_reward', 'estimated_minutes', 'age_min', 'age_max', 'audience_roles', 'learning_tags',
        'learning_outcomes', 'detail_sections', 'is_web_enabled', 'is_download_enabled', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'audience_roles' => 'array',
        'learning_tags' => 'array',
        'learning_outcomes' => 'array',
        'detail_sections' => 'array',
        'is_web_enabled' => 'boolean',
        'is_download_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'points_reward' => 'integer',
        'estimated_minutes' => 'integer',
        'age_min' => 'integer',
        'age_max' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');
    }

    public function getAvailablePlatformsAttribute(): array
    {
        $platforms = [];
        if ($this->is_web_enabled) {
            $platforms['web'] = $this->web_play_url ?: route('studybuddy.final.web-play', $this->slug);
        }
        foreach (['ios' => 'ios_url', 'android' => 'android_url', 'windows' => 'windows_url', 'mac' => 'mac_url'] as $key => $column) {
            if (! empty($this->{$column})) {
                $platforms[$key] = $this->{$column};
            }
        }
        return $platforms;
    }

    public function visibleForRole(?string $role): bool
    {
        $roles = $this->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];
        if ($role === null) {
            return true;
        }
        return in_array($role, $roles, true);
    }

    public function safeHeroImage(): ?string
    {
        return $this->hero_image ?: null;
    }
}
