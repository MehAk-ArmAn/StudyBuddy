<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyContentItem extends Model
{
    protected $fillable = [
        'page_slug', 'item_type', 'title', 'subtitle', 'description', 'icon', 'badge',
        'image_path', 'button_label', 'button_url', 'extra', 'status', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'extra' => 'array',
        'is_active' => 'boolean',
    ];
}
