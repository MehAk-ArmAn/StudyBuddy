<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: Reward journal: never adjust spendable points without corresponding authorized transaction/audit semantics. See docs/handbook/REWARDS.md.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyBuddyPointTransaction extends Model
{
    protected $table = 'studybuddy_point_transactions';

    protected $fillable = [
        'user_id', 'source_type', 'source_slug', 'title', 'points', 'status', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
