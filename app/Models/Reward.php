<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'points_required', 'price_text', 'category', 'image_path', 'rarity', 'locked_text', 'unlocked_text', 'is_active', 'sort_order', 'icon', 'glow_color'];

    protected function casts(): array
    {
        return ['points_required' => 'integer', 'sort_order' => 'integer', 'is_active' => 'boolean'];
    }
}
