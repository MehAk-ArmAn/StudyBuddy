<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = [
        'slug', 'template', 'title', 'nav_label', 'meta_title', 'meta_description',
        'eyebrow', 'hero_title', 'hero_subtitle', 'hero_body', 'hero_image_path',
        'button_label', 'button_url', 'secondary_button_label', 'secondary_button_url',
        'sort_order', 'is_enabled', 'settings',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'settings' => 'array'];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort_order');
    }
}
