<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $table = 'pages';

    protected $fillable = ['key', 'title', 'slug', 'description', 'is_enabled'];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean'];
    }

    public function sections()
    {
        return $this->hasMany(PageSection::class, 'page_id')->orderBy('sort_order');
    }
}
