<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'points_required', 'rarity', 'icon', 'glow_color'];

    protected function casts(): array
    {
        return ['points_required' => 'integer'];
    }
}
