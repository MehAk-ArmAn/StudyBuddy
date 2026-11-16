<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyBuddyContentPage extends Model{
 protected $table='studybuddy_content_pages';
 protected $fillable=['slug','title','eyebrow','subtitle','hero_badge','hero_image','primary_cta_label','primary_cta_url','secondary_cta_label','secondary_cta_url','content_blocks','is_published','sort_order','meta_title','meta_description'];
 protected $casts=['content_blocks'=>'array','is_published'=>'boolean'];
 public function items(){return $this->hasMany(StudyBuddyContentItem::class,'page_slug','slug')->where('is_active',true)->orderBy('sort_order')->orderBy('title');}
}
