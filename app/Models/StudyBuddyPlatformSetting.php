<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyPlatformSetting extends Model
{
    protected $fillable = [
        'setting_key', 'label', 'group_name', 'setting_value', 'field_type', 'help_text', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function value(string $key, ?string $default = null): ?string
    {
        return static::query()->where('setting_key', $key)->value('setting_value') ?? $default;
    }

    public static function publicMap(): array
    {
        return static::query()
            ->where('is_public', true)
            ->orderBy('sort_order')
            ->pluck('setting_value', 'setting_key')
            ->toArray();
    }
}
