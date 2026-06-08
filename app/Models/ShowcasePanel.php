<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShowcasePanel extends Model
{
    protected $fillable = ['number', 'title', 'description', 'image_path', 'sort_order', 'is_enabled'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_enabled' => 'boolean'];
    }
}
