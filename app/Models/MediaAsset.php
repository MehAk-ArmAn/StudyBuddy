<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = [
        'title',
        'alt_text',
        'path',
        'type',
        'mime_type',
        'file_size',
        'width',
        'height',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}