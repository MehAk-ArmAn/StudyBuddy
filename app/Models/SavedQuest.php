<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedQuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'app_slug',
        'app_title',
        'mission_title',
        'mission_description',
        'difficulty',
        'estimated_minutes',
        'status',
        'progress',
        'notes',
        'source_url',
        'metadata',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'estimated_minutes' => 'integer',
        'progress' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'archived' => 'Archived',
            default => 'Saved',
        };
    }
}
