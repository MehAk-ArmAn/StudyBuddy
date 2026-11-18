<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyBuddySchoolMessage extends Model
{
    protected $table = 'studybuddy_school_messages';
    protected $guarded = ['id'];
    protected function casts(): array { return ['hidden_at' => 'datetime']; }
    public function conversation(): BelongsTo { return $this->belongsTo(StudyBuddySchoolConversation::class, 'conversation_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
}
