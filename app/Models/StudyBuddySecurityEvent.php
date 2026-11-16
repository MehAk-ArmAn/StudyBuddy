<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StudyBuddySecurityEvent extends Model
{
    public $timestamps = false;
    protected $table = 'studybuddy_security_events';
    protected $guarded = ['id'];
    protected function casts(): array { return ['context' => 'array', 'created_at' => 'datetime']; }

    public static function record(string $event, string $severity = 'info', ?int $actorId = null, array $context = []): void
    {
        try {
            if (! Schema::hasTable('studybuddy_security_events')) { return; }
            static::create([
                'event' => $event,
                'severity' => $severity,
                'actor_id' => $actorId,
                'ip_address' => request()->ip(),
                'context' => $context,
                'created_at' => now(),
            ]);
        } catch (\Throwable $error) {
            // Audit-storage outages should not produce authentication outages.
            Log::warning('StudyBuddy audit write failed', ['type' => $event]);
        }
    }
}
