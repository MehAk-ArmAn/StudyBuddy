<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyPlatformSetting extends Model
{
    protected $table = 'studybuddy_platform_settings';

    protected $fillable = [
        'setting_key', 'label', 'group_name', 'setting_value', 'field_type', 'help_text', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function publicMap(): array
    {
        return static::query()
            ->where('is_public', true)
            ->pluck('setting_value', 'setting_key')
            ->toArray();
    }
}
