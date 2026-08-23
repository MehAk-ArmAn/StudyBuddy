<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyBuddyContentItem extends Model{ protected $table='studybuddy_content_items'; protected $fillable=['page_slug','item_type','title','subtitle','description','icon','badge','image_path','button_label','button_url','status','sort_order','extra','is_active']; protected $casts=['extra'=>'array','is_active'=>'boolean','sort_order'=>'integer']; }
