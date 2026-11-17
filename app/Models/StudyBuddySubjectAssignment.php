<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One learning subject per app. No second app catalogue. */
class StudyBuddySubjectAssignment extends Model
{
    protected $table = 'studybuddy_w34_subject_assignments';

    protected $fillable = [
        'app_id', 'primary_subject', 'learning_stage', 'topics', 'learning_outcome',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(StudyBuddyMiniAppPlatform::class, 'app_id');
    }
}
