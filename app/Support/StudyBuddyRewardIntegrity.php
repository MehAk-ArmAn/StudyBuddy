<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: P11F ledger invariant: keep reward event deduplication, per-user row locks, caps, wallet balance and transaction journal in the same atomic operation. No direct client-side point mint API.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Support;

use App\Models\StudyBuddyPointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * All real new points are minted or spent atomically against users.cosmic_points
 * and the existing StudyBuddyPointTransaction journal. No user-facing mint API.
 * Only call verifiedMathRun() from the FIRST-PARTY, server-graded W32 submit flow.
 */
final class StudyBuddyRewardIntegrity
{
    public static function installed(): bool
    {
        return Schema::hasTable('studybuddy_reward_events')
            && Schema::hasTable('studybuddy_reward_rules')
            && Schema::hasTable('studybuddy_reward_settings')
            && Schema::hasTable('studybuddy_user_reward_badges');
    }

    /** Called after server answer grading, inside the W32 transaction. */
    public function verifiedMathRun(int $userId, int $runId, int $score, int $elapsed, string $level): int
    {
        if (!self::installed() || !in_array($level, ['starter','explorer'], true)) return 0;
        $key = 'math-sprint:'.$level;
        $eventKey = 'w32:run:'.$runId;
        return DB::transaction(function () use ($userId, $runId, $score, $elapsed, $key, $eventKey): int {
            $user = User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
            $existing = DB::table('studybuddy_reward_events')->where('event_key', $eventKey)->first();
            if ($existing) return 0;
            $settings = DB::table('studybuddy_reward_settings')->where('id', 1)->first();
            $rule = DB::table('studybuddy_reward_rules')->where('game_key', $key)->first();
            if (!$settings || !$settings->enabled || !$rule || !$rule->enabled) return 0;
            // Ensure this event is attached to the same user and an actual graded result.
            $run = DB::table('studybuddy_w32_math_runs')->where('id', $runId)
                ->where('user_id', $userId)->where('status','completed')->first();
            if (!$run || (int)$run->score !== $score || $run->submitted_at === null) return 0;
            if ($score < (int)$rule->min_correct || $score > 5 || $elapsed < (int)$rule->min_seconds) return 0;
            $raw = $score * (int)$rule->points_per_correct;
            if ($elapsed >= (int)$rule->time_bonus_after_seconds) {
                $raw += (int)$rule->time_bonus_points;
            }
            $raw = min($raw, (int)$rule->max_points_per_run);
            $dayTotal = (int) DB::table('studybuddy_reward_events')->where('user_id',$userId)
                ->where('created_at', '>=', now()->startOfDay())->sum('points_awarded');
            $amount = max(0, min($raw, max(0, (int)$settings->global_daily_cap - $dayTotal)));
            if ($amount === 0) return 0;
            $txn = StudyBuddyPointTransaction::create([
                'user_id'=>$userId, 'source_type'=>'verified_math_sprint',
                'source_slug'=>$key, 'title'=>'Completed a verified Math Sprint',
                'points'=>$amount, 'status'=>'earned',
                'meta'=>['run_id'=>$runId,'correct_answers'=>$score,'elapsed_seconds'=>min(1800,$elapsed),'policy'=>'P11F'],
            ]);
            $newBalance = (int)$user->cosmic_points + $amount;
            if ($newBalance > 2147483647) throw new \RuntimeException('Wallet maximum reached.');
            $user->cosmic_points = $newBalance;
            $user->save();
            $eventId = DB::table('studybuddy_reward_events')->insertGetId([
                'user_id'=>$userId, 'event_key'=>$eventKey, 'game_key'=>$key,
                'score'=>$score, 'max_score'=>5, 'elapsed_seconds'=>min($elapsed,1800),
                'points_awarded'=>$amount, 'point_transaction_id'=>$txn->id,
                'created_at'=>now(), 'updated_at'=>now(),
            ]);
            $this->badges($user, $score, $key, $eventId);
            return $amount;
        });
    }

    private function badges(User $user, int $score, string $gameKey, int $eventId): void
    {
        $count = DB::table('studybuddy_reward_events')->where('user_id',$user->id)->count();
        $codes = ['first-orbit'];
        if ($score === 5) $codes[] = 'math-perfect';
        if ($score === 5 && $gameKey === 'math-sprint:explorer') $codes[] = 'explorer-master';
        if ($count >= 5) $codes[] = 'five-finished';
        if ($count >= 10) $codes[] = 'hidden-constellation';
        foreach ($codes as $code) {
            DB::table('studybuddy_user_reward_badges')->insertOrIgnore([
                'user_id'=>$user->id,'code'=>$code,'earned_from_event_id'=>$eventId,
                'earned_at'=>now(),'created_at'=>now(),'updated_at'=>now(),
            ]);
        }
        $profile = is_array($user->role_profile) ? $user->role_profile : [];
        $unlocked = $profile['unlocked_profile_items'] ?? [
            'profile_theme:cosmic', 'profile_theme:ocean', 'profile_theme:forest',
            'profile_frame:none', 'profile_badge:learning-spark',
            'profile_color:purple', 'profile_color:cyan',
            'avatar_shape:rounded', 'avatar_shape:circle',
        ];
        if (!is_array($unlocked)) $unlocked = [];
        if ($score === 5) $unlocked[] = 'profile_badge:math-star';
        if ($count >= 5) $unlocked[] = 'profile_frame:math-orbit';
        $profile['unlocked_profile_items'] = array_values(array_unique($unlocked));
        $user->role_profile = $profile;
        $user->save();
    }

    public function headAdjustment(int $headId, int $targetId, string $reason, int $requested): int
    {
        if (!self::isHead($headId)) abort(403);
        return DB::transaction(function () use ($headId,$targetId,$reason,$requested): int {
            $target = User::query()->whereKey($targetId)->lockForUpdate()->firstOrFail();
            $newBalance = (int)$target->cosmic_points + $requested;
            if ($newBalance < 0 || $newBalance > 2147483647) {
                throw ValidationException::withMessages(['points'=>'Adjustment exceeds available balance or wallet limit.']);
            }
            $target->cosmic_points = $newBalance;
            $target->save();
            StudyBuddyPointTransaction::create([
                'user_id'=>$targetId,'source_type'=>'head_admin_adjustment','source_slug'=>'head-admin',
                'title'=>$reason,'points'=>$requested,'status'=>$requested >= 0 ? 'earned':'adjusted',
                'meta'=>['actor_id'=>$headId],
            ]);
            DB::table('studybuddy_reward_rule_audits')->insert([
                'actor_id'=>$headId,'action'=>'manual_balance_adjustment','target'=>'user:'.$targetId,
                'before_value'=>json_encode(['balance'=>$newBalance-$requested]),
                'after_value'=>json_encode(['balance'=>$newBalance,'change'=>$requested]),
                'created_at'=>now(),'updated_at'=>now(),
            ]);
            return $newBalance;
        });
    }

    public static function isHead(int $userId): bool
    {
        if (!self::installed() || $userId < 1) return false;
        $user = User::find($userId);
        if (!$user || !$user->is_admin || $user->email !== StudyBuddyAdminAuthority::HEAD_EMAIL
            || StudyBuddyAdminAuthority::headId() !== $userId) return false;
        return DB::table('studybuddy_admin_grants')->where('user_id',$userId)
            ->where('kind','head')->where('is_active',true)->exists();
    }

    public static function badgeNames(): array
    {
        return [
            'first-orbit'=>'First Orbit',
            'math-perfect'=>'Perfect Numbers',
            'explorer-master'=>'Explorer Master',
            'five-finished'=>'Steady Explorer',
            'hidden-constellation'=>'Hidden Constellation',
        ];
    }
}
