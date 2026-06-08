<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $fillable = ['page_id', 'key', 'title', 'sort_order', 'is_enabled'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_enabled' => 'boolean'];
    }

    public function blocks()
    {
        return $this->hasMany(ContentBlock::class, 'page_section_id')->orderBy('sort_order');
    }
}
