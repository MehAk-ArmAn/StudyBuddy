<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;
class StudyBuddyPlatformSetting extends Model{
 protected $table='studybuddy_platform_settings';
 protected $fillable=['setting_key','label','group_name','setting_value','field_type','help_text','is_public','sort_order'];
 protected $casts=['is_public'=>'boolean','sort_order'=>'integer'];
 public static function value(string $key,?string $default=null):?string{try{if(!Schema::hasTable('studybuddy_platform_settings'))return $default;return static::where('setting_key',$key)->value('setting_value')??$default;}catch(Throwable $e){return $default;}}
 public static function publicMap():array{try{if(!Schema::hasTable('studybuddy_platform_settings'))return static::fallbackMap();$map=static::where('is_public',true)->orderBy('sort_order')->pluck('setting_value','setting_key')->toArray();return $map?:static::fallbackMap();}catch(Throwable $e){return static::fallbackMap();}}
 public static function fallbackMap():array{return ['platform_name'=>'StudyBuddy','platform_tagline'=>'One dashboard for learning apps, quests, points, and safe study routines.','launchpad_heading'=>'Choose your learning world.','launchpad_intro'=>'Play on web when available or download the right version for your device. Every app connects back to StudyBuddy points, quests, and your main dashboard.','points_policy'=>'Learners earn points by completing safe, verified StudyBuddy activities.','support_email'=>'support@studybuddy.fun','final_launch_note'=>'Final app hosting, store uploads, and playable builds should be connected after each mini-app is packaged and tested.'];}
}
