<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['label', 'url', 'route_name', 'is_cta', 'is_enabled', 'sort_order'];

    protected function casts(): array
    {
        return ['is_cta' => 'boolean', 'is_enabled' => 'boolean', 'sort_order' => 'integer'];
    }
}
