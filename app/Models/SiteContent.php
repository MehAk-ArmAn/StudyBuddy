<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'section', 'title', 'body', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }
}
