<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSection extends Model
{
    protected $fillable = ['section_key','section_type','eyebrow','title','subtitle','body','image_path','background_image_path','button_label','button_url','secondary_button_label','secondary_button_url','sort_order','is_enabled','settings'];
    protected function casts(): array { return ['is_enabled'=>'boolean','settings'=>'array']; }
    public function items(): HasMany { return $this->hasMany(HomepageSectionItem::class)->orderBy('sort_order'); }
}
