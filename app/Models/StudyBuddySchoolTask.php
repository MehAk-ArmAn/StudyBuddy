<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyBuddySchoolTask extends Model
{
    protected $table = 'studybuddy_school_tasks';
    protected $guarded = ['id'];
    protected function casts(): array { return ['due_on' => 'date', 'archived_at' => 'datetime']; }
    public function organization(): BelongsTo { return $this->belongsTo(SchoolOrganization::class, 'organization_id'); }
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
