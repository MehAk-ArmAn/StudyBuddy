<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;

/** Private, read-only event/notification feed from verified links and the owner's saved plans. */
final class StudyBuddyDailyPulse
{
    public static function profile($user): array
    {
        return is_array($user->role_profile) ? $user->role_profile : [];
    }

    public static function settings(array $profile): array
    {
        $raw = is_array($profile['sb_p09_settings'] ?? null) ? $profile['sb_p09_settings'] : [];
        return [
            'assignment_alerts' => (bool) ($raw['assignment_alerts'] ?? true),
            'plan_alerts' => (bool) ($raw['plan_alerts'] ?? true),
            'quiet_enabled' => (bool) ($raw['quiet_enabled'] ?? false),
            'quiet_start' => preg_match('/^\d{2}:\d{2}$/', (string) ($raw['quiet_start'] ?? '21:00')) ? $raw['quiet_start'] ?? '21:00' : '21:00',
            'quiet_end' => preg_match('/^\d{2}:\d{2}$/', (string) ($raw['quiet_end'] ?? '07:00')) ? $raw['quiet_end'] ?? '07:00' : '07:00',
        ];
    }

    public static function plans(array $profile): Collection
    {
        $rows = is_array($profile['sb_p09_plans'] ?? null) ? $profile['sb_p09_plans'] : [];
        return collect($rows)->filter(fn ($row) => is_array($row)
            && is_string($row['id'] ?? null) && is_string($row['date'] ?? null)
            && is_string($row['title'] ?? null) && preg_match('/^\d{4}-\d\d-\d\d$/', $row['date']))
            ->take(40)->values();
    }

    /** Only class assignments still connected via a verified learner Connect Code. */
    public static function assignments($user, Carbon $start, Carbon $end): Collection
    {
        $role = method_exists($user, 'normalizedRole') ? $user->normalizedRole() : ($user->role ?? 'student');
        foreach (['studybuddy_assignments', 'studybuddy_learning_groups', 'studybuddy_assignment_recipients'] as $table) {
            if (!Schema::hasTable($table)) return collect();
        }
        if ($role === 'teacher') {
            if (!$user->hasVerifiedEmail() || !$user->canUseAdultControls()) return collect();
            return DB::table('studybuddy_assignments as a')
                ->join('studybuddy_learning_groups as g', function ($join) {
                    $join->on('g.id', '=', 'a.group_id')->on('g.owner_id', '=', 'a.owner_id');
                })
                ->where('a.owner_id', $user->id)->where('a.status', 'assigned')
                ->where('g.type', 'class')->where('g.is_active', true)
                ->whereBetween('a.due_at', [$start->toDateTimeString(), $end->toDateTimeString()])
                ->orderBy('a.due_at')->limit(120)
                ->get(['a.id', 'a.title', 'a.due_at'])
                ->map(fn ($row) => ['id' => 'class-'.$row->id, 'title' => $row->title,
                    'date' => Carbon::parse($row->due_at)->toDateString(),
                    'time' => Carbon::parse($row->due_at)->format('H:i'), 'kind' => 'class',
                    'source_id' => (int) $row->id]);
        }
        if (!in_array($role, ['student', 'independent_learner'], true)
            || !$user->hasVerifiedEmail() || $user->is_admin || !Schema::hasTable('studybuddy_group_members')) {
            return collect();
        }
        return DB::table('studybuddy_assignment_recipients as r')
            ->join('studybuddy_assignments as a', 'a.id', '=', 'r.assignment_id')
            ->join('studybuddy_learning_groups as g', function ($join) {
                $join->on('g.id', '=', 'a.group_id')->on('g.owner_id', '=', 'a.owner_id');
            })
            ->join('studybuddy_group_members as m', function ($join) {
                $join->on('m.group_id', '=', 'a.group_id')
                    ->on('m.user_id', '=', 'r.user_id')
                    ->on('m.owner_id', '=', 'a.owner_id');
            })
            ->where('r.user_id', $user->id)->where('r.status', 'assigned')
            ->whereRaw('LOWER(r.email) = LOWER(?)', [$user->email])
            ->whereRaw('LOWER(m.email) = LOWER(r.email)')
            ->where('m.status', 'connected_with_code')->where('m.member_role', 'student')
            ->where('g.type', 'class')->where('g.is_active', true)
            ->where('a.status', 'assigned')
            ->whereBetween('a.due_at', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->orderBy('a.due_at')->limit(120)
            ->get(['r.id', 'a.title', 'a.due_at'])
            ->unique('id')
            ->map(fn ($row) => ['id' => 'task-'.$row->id, 'title' => $row->title,
                'date' => Carbon::parse($row->due_at)->toDateString(),
                'time' => Carbon::parse($row->due_at)->format('H:i'), 'kind' => 'task',
                'source_id' => (int) $row->id]);
    }

    public static function calendar($user, Carbon $month): Collection
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $school = self::assignments($user, $start->copy()->startOfDay(), $end->copy()->endOfDay());
        $plans = self::plans(self::profile($user))->filter(fn ($p) => $p['date'] >= $start->toDateString()
            && $p['date'] <= $end->toDateString())
            ->map(fn ($p) => array_merge($p, ['kind' => 'plan']));
        return $school->concat($plans)->sortBy(fn ($row) => $row['date'].' '.($row['time'] ?? '23:59'))
            ->values();
    }

    public static function notices($user): Collection
    {
        $profile = self::profile($user);
        $settings = self::settings($profile);
        $start = now()->subDays(14)->startOfDay();
        $end = now()->addDays(7)->endOfDay();
        $items = collect();
        if ($settings['assignment_alerts']) {
            foreach (self::assignments($user, $start, $end) as $a) {
                $items->push(['id' => 'due:'.$a['id'], 'title' => $a['title'],
                    'label' => $a['kind'] === 'class' ? 'Your class deadline' : 'Assigned work deadline',
                    'date' => $a['date'], 'kind' => 'assignment']);
            }
        }
        if ($settings['plan_alerts']) {
            foreach (self::plans($profile) as $p) {
                if ($p['date'] >= $start->toDateString() && $p['date'] <= $end->toDateString()) {
                    $items->push(['id' => 'plan:'.$p['id'], 'title' => $p['title'],
                        'label' => 'Your saved plan', 'date' => $p['date'], 'kind' => 'plan']);
                }
            }
        }
        $seen = array_values(array_filter(is_array($profile['sb_p09_seen'] ?? null) ? $profile['sb_p09_seen'] : [], 'is_string'));
        return $items->sortBy('date')->values()->map(fn ($n) => $n + ['read' => in_array($n['id'], $seen, true)]);
    }

    public static function weekly($user): array
    {
        $start = now()->startOfDay()->subDays(6);
        $end = now()->endOfDay();
        $keys = [];
        for ($day = 0; $day < 7; $day++) $keys[$start->copy()->addDays($day)->toDateString()] = ['visits' => 0, 'rounds' => 0];
        if (Schema::hasTable('studybuddy_learning_sessions')
            && Schema::hasColumn('studybuddy_learning_sessions', 'launch_source')) {
            $visits = DB::table('studybuddy_learning_sessions')->where('user_id', $user->id)
                ->where('launch_source', 'web_launcher')
                ->whereBetween('started_at', [$start, $end])->get(['started_at']);
            foreach ($visits as $row) {
                $key = Carbon::parse($row->started_at)->toDateString();
                if (isset($keys[$key])) $keys[$key]['visits']++;
            }
        }
        if (Schema::hasTable('studybuddy_w32_math_runs')) {
            $rounds = DB::table('studybuddy_w32_math_runs')->where('user_id', $user->id)
                ->where('status', 'completed')->whereNotNull('submitted_at')
                ->whereBetween('submitted_at', [$start, $end])->get(['submitted_at']);
            foreach ($rounds as $row) {
                $key = Carbon::parse($row->submitted_at)->toDateString();
                if (isset($keys[$key])) $keys[$key]['rounds']++;
            }
        }
        return collect($keys)->map(fn ($value, $date) => [
            'date' => $date, 'day' => Carbon::parse($date)->format('D'),
            'visits' => $value['visits'], 'rounds' => $value['rounds'],
        ])->values()->all();
    }
}
