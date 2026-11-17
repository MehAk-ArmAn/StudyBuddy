<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Read-only overview built from the existing W30/W31/W32 learning tables.
 * A journalled visit is NOT a grade, mastery claim, completion certificate or points award.
 */
final class StudyBuddyLearningSnapshot
{
    public static function forUser(int $userId): array
    {
        $snapshot = [
            'available' => false,
            'appVisits' => 0,
            'savedVisits' => 0,
            'gradedRounds' => 0,
            'earnedBadges' => 0,
            'recentActivity' => collect(),
            'recentResults' => collect(),
            'badges' => collect(),
            'insightsReady' => false,
        ];

        if (! Schema::hasTable('studybuddy_learning_sessions')) {
            return $snapshot;
        }

        $snapshot['available'] = true;
        $sessions = DB::table('studybuddy_learning_sessions')->where('user_id', $userId);
        // Only W31 launcher sessions count as connected app visits.
        if (Schema::hasColumn('studybuddy_learning_sessions', 'launch_source')) {
            $snapshot['appVisits'] = (clone $sessions)->where('launch_source', 'web_launcher')->count();
            $snapshot['savedVisits'] = (clone $sessions)->where('launch_source', 'web_launcher')
                ->where('status', 'finished')->count();
            $snapshot['insightsReady'] = Schema::hasColumn('studybuddy_learning_sessions', 'observed_seconds')
                && Schema::hasColumn('studybuddy_learning_sessions', 'last_observed_at');
        }
        $snapshot['recentActivity'] = (clone $sessions)
            ->orderByDesc('started_at')->orderByDesc('id')
            ->limit(3)->get(['id', 'app_name', 'status', 'started_at']);

        if (Schema::hasTable('studybuddy_w32_math_runs')) {
            $runs = DB::table('studybuddy_w32_math_runs')
                ->where('user_id', $userId)->where('status', 'completed')->whereNotNull('score');
            $snapshot['gradedRounds'] = (clone $runs)->count();
            $snapshot['recentResults'] = (clone $runs)->orderByDesc('submitted_at')
                ->orderByDesc('id')->limit(3)->get(['id', 'level', 'score', 'total', 'submitted_at']);
        }

        if (Schema::hasTable('studybuddy_w32_achievements')) {
            $badges = DB::table('studybuddy_w32_achievements')->where('user_id', $userId);
            $snapshot['earnedBadges'] = (clone $badges)->count();
            $snapshot['badges'] = (clone $badges)->latest('earned_at')->latest('id')
                ->limit(3)->get(['code', 'earned_at'])->map(function ($badge) {
                    $badge->label = match ($badge->code) {
                        'math-first-pass' => 'Math Star',
                        'math-perfect-five' => 'Perfect Orbit',
                        default => 'Learning badge',
                    };
                    return $badge;
                });
        }

        return $snapshot;
    }
}
