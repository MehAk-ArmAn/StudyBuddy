<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterItem extends Model
{
    protected $fillable = ['group','label','url','sort_order','is_enabled','opens_new_tab'];
    protected function casts(): array { return ['is_enabled'=>'boolean','opens_new_tab'=>'boolean']; }
}
