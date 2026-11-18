<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyBuddySchoolSafetyReport extends Model
{
    protected $table = 'studybuddy_school_safety_reports';
    protected $guarded = ['id'];
    protected function casts(): array { return ['resolved_at' => 'datetime']; }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
    public function handler(): BelongsTo { return $this->belongsTo(User::class, 'handled_by'); }
}
