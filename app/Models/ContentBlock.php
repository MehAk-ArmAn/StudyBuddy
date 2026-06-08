<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = ['page_section_id', 'key', 'label', 'value', 'type', 'sort_order', 'is_enabled'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_enabled' => 'boolean'];
    }
}
