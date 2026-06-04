<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiniApp extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'subject', 'age_band', 'description', 'card_tone', 'status', 'launch_path', 'hero_metric', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }
}
