<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudyBuddyVerificationCase extends Model{
 protected $table='studybuddy_verification_cases';
 protected $fillable=['user_id','role_type','method','status','provider_reference','submitted_name','submitted_country','adult_confirmed','consent_confirmed','notes','admin_notes','reviewed_by','submitted_at','reviewed_at'];
 protected $casts=['adult_confirmed'=>'boolean','consent_confirmed'=>'boolean','submitted_at'=>'datetime','reviewed_at'=>'datetime'];
 public function user(){return $this->belongsTo(User::class);} public function reviewer(){return $this->belongsTo(User::class,'reviewed_by');}
}
