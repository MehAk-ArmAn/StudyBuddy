<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyPointTransaction extends Model
{
    protected $fillable = [
        'user_id', 'source_type', 'source_slug', 'title', 'points', 'status', 'meta',
    ];

    protected $casts = [
        'points' => 'integer',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
