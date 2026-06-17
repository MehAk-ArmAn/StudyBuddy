<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaAsset extends Model
{
    protected $fillable = ['title','alt_text','path','type','mime_type','file_size','width','height','is_active'];

    protected function casts(): array { return ['is_active' => 'boolean']; }
}
