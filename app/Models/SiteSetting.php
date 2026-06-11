<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'is_active' => 'boolean', 'is_public' => 'boolean', 'opens_new_tab' => 'boolean', 'metadata' => 'array', 'settings' => 'array', 'preferences' => 'array', 'earned_at' => 'datetime', 'last_login_at' => 'datetime'];
    }
}
