<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetReference extends Model
{
    protected $fillable = ['name', 'path', 'type', 'notes', 'is_required'];

    protected function casts(): array
    {
        return ['is_required' => 'boolean'];
    }
}
