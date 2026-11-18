<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyBuddySchoolNotice extends Model
{
    protected $table = 'studybuddy_school_notices';
    protected $guarded = ['id'];
    protected function casts(): array { return ['expires_at' => 'datetime', 'archived_at' => 'datetime']; }
    public function organization(): BelongsTo { return $this->belongsTo(SchoolOrganization::class, 'organization_id'); }
    public function poster(): BelongsTo { return $this->belongsTo(User::class, 'posted_by'); }
}
