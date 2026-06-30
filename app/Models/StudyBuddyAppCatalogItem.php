<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyAppCatalogItem extends Model
{
    protected $fillable = [
        'app_key', 'title', 'category', 'summary', 'description', 'icon', 'image_path',
        'available_web', 'available_ios', 'available_android', 'available_windows',
        'web_play_url', 'ios_url', 'android_url', 'windows_url', 'points_reward',
        'launch_status', 'is_active', 'sort_order', 'extra',
    ];

    protected $casts = [
        'available_web' => 'boolean',
        'available_ios' => 'boolean',
        'available_android' => 'boolean',
        'available_windows' => 'boolean',
        'is_active' => 'boolean',
        'extra' => 'array',
    ];
}
