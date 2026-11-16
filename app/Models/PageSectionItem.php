<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSectionItem extends Model
{
    protected $fillable = [
        'page_section_id', 'item_key', 'title', 'subtitle', 'body', 'image_path', 'icon_path',
        'button_label', 'button_url', 'badge_text', 'sort_order', 'is_enabled', 'settings',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'settings' => 'array'];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(PageSection::class, 'page_section_id');
    }
}
