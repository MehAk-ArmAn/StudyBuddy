<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterSection extends Model
{
    protected $fillable = ['title', 'handle', 'sort_order', 'is_enabled'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_enabled' => 'boolean'];
    }

    public function links()
    {
        return $this->hasMany(FooterLink::class)->orderBy('sort_order');
    }
}
