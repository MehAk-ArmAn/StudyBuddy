<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyBuddyMiniAppPlatform extends Model{
 protected $table='studybuddy_mini_app_platforms';
 protected $fillable=['slug','name','category','tagline','description','status','icon','accent','web_play_url','ios_url','android_url','windows_url','mac_url','support_url','points_reward','estimated_minutes','learning_tags','is_web_enabled','is_download_enabled','is_featured','is_active','sort_order'];
 protected $casts=['learning_tags'=>'array','is_web_enabled'=>'boolean','is_download_enabled'=>'boolean','is_featured'=>'boolean','is_active'=>'boolean','points_reward'=>'integer','estimated_minutes'=>'integer','sort_order'=>'integer'];
 public function scopeActive($q){return $q->where('is_active',true);} public function scopeOrdered($q){return $q->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');}
 public function getAvailablePlatformsAttribute():array{$p=[];if($this->is_web_enabled&&$this->web_play_url)$p['web']=$this->web_play_url;if($this->ios_url)$p['ios']=$this->ios_url;if($this->android_url)$p['android']=$this->android_url;if($this->windows_url)$p['windows']=$this->windows_url;if($this->mac_url)$p['mac']=$this->mac_url;return $p;}
}
