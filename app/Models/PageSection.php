<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageSection extends Model
{
    protected $fillable = [
        'page_id', 'section_key', 'section_type', 'eyebrow', 'title', 'subtitle', 'body',
        'image_path', 'button_label', 'button_url', 'sort_order', 'is_enabled', 'settings',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'settings' => 'array'];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PageSectionItem::class)->orderBy('sort_order');
    }
}
