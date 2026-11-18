<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyBuddySchoolConversation extends Model
{
    protected $table = 'studybuddy_school_conversations';
    protected $guarded = ['id'];
    public function organization(): BelongsTo { return $this->belongsTo(SchoolOrganization::class, 'organization_id'); }
    public function messages(): HasMany { return $this->hasMany(StudyBuddySchoolMessage::class, 'conversation_id'); }
}
