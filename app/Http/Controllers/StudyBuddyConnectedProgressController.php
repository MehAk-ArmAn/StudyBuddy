<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * W31: connected, learner-owned app visit journal.
 * Observed timestamps represent browser interaction with the launcher,
 * NOT proof of skill, a game level passed, active attention, or earned points.
 */
class StudyBuddyConnectedProgressController extends Controller
{
    private const TABLE = 'studybuddy_learning_sessions';

    private function ready(): void
    {
        abort_unless(Schema::hasTable(self::TABLE)
            && Schema::hasColumn(self::TABLE, 'observed_seconds')
            && Schema::hasColumn(self::TABLE, 'last_observed_at')
            && Schema::hasColumn(self::TABLE, 'launch_source'), 503,
            'Connected learning requires the W30 and W31 migrations.');
    }

    private function canOpen(StudyBuddyMiniAppPlatform $app, $user): bool
    {
        $role = method_exists($user, 'normalizedRole')
            ? $user->normalizedRole() : ($user->role ?? 'student');
        if (! $app->visibleForRole($role) || ! $app->hasPublishedWebApp()) return false;
        if (! empty($user->date_of_birth)) {
            $age = Carbon::parse($user->date_of_birth)->age;
            if ($app->age_min !== null && $age < $app->age_min) return false;
            if ($app->age_max !== null && $age > $app->age_max) return false;
        }
        return true;
    }

    public function start(Request $request): JsonResponse
    {
        $this->ready();
        $data = $request->validate(['app_slug' => ['required','string','max:120']]);
        $user = $request->user();
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $data['app_slug'])->firstOrFail();
        abort_unless($this->canOpen($app, $user), 403, 'This app is not available for this learner.');

        $outcome = DB::transaction(function () use ($user, $app) {
            // One active session per learner, including concurrent launcher tabs.
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
            $active = DB::table(self::TABLE)->where('user_id', $user->id)
                ->where('status', 'active')->orderByDesc('id')->first();
            if ($active) {
                if ($active->app_slug !== $app->slug) {
                    return ['conflict' => true];
                }
                return ['id' => (int) $active->id, 'resumed' => true];
            }
            $id = DB::table(self::TABLE)->insertGetId([
                'user_id' => $user->id,
                'app_slug' => $app->slug,
                'app_name' => $app->name,
                'status' => 'active',
                'started_at' => now(),
                'last_observed_at' => now(),
                'observed_seconds' => 0,
                'launch_source' => 'web_launcher',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return ['id' => (int) $id, 'resumed' => false];
        });
        if (isset($outcome['conflict'])) {
            return response()->json([
                'ok' => false,
                'message' => 'Another activity is still open. Close it in My Learning Activity first.',
                'activity_url' => route('studybuddy.learning.activity'),
            ], 409);
        }
        return response()->json([
            'ok' => true,
            'session_id' => $outcome['id'],
            'resumed' => $outcome['resumed'],
            'message' => 'App visit recorded privately. No skill result or points claimed.',
        ])->header('Cache-Control', 'private, no-store');
    }

    public function heartbeat(Request $request, int $session): JsonResponse
    {
        $this->ready();
        // Heartbeat is server-timed, not a client-reported duration or score.
        // On return from a hidden tab, resume resets the clock so time spent
        // elsewhere isn't counted as browser-visible observation.
        $data = $request->validate(['kind' => ['nullable', 'in:tick,resume']]);
        $resume = ($data['kind'] ?? 'tick') === 'resume';
        $seconds = DB::transaction(function () use ($request, $session, $resume) {
            $record = DB::table(self::TABLE)->where('id', $session)
                ->where('user_id', $request->user()->id)->lockForUpdate()->first();
            abort_unless($record, 404);
            abort_unless($record->status === 'active', 409, 'Session is already closed.');
            $now = now();
            $previous = $record->last_observed_at ? Carbon::parse($record->last_observed_at) : $now;
            $gap = max(0, (int) $previous->diffInSeconds($now));
            // Cap each interval; background/suspended tabs must not count as hours of play.
            $increment = ! $resume && $gap >= 10 ? min(30, $gap) : 0;
            $new = min(21600, (int) ($record->observed_seconds ?? 0) + $increment);
            DB::table(self::TABLE)->where('id', $session)->where('user_id', $request->user()->id)
                ->update(['last_observed_at' => $now, 'observed_seconds' => $new, 'updated_at' => $now]);
            return $new;
        });
        return response()->json(['ok' => true, 'observed_seconds' => $seconds,
            'note' => 'Estimated visible-launcher time, not verified gameplay.'])
            ->header('Cache-Control', 'private, no-store');
    }

    public function finish(Request $request, int $session): JsonResponse
    {
        return $this->close($request, $session, 'finished');
    }

    public function leave(Request $request, int $session): JsonResponse
    {
        return $this->close($request, $session, 'left');
    }

    private function close(Request $request, int $id, string $status): JsonResponse
    {
        $this->ready();
        $record = DB::transaction(function () use ($request, $id, $status) {
            $r = DB::table(self::TABLE)->where('id', $id)
                ->where('user_id', $request->user()->id)->lockForUpdate()->first();
            abort_unless($r, 404);
            if ($r->status !== 'active') return $r; // idempotent retries, no points
            $now = now();
            $duration = max(0, min(21600, (int) Carbon::parse($r->started_at)->diffInSeconds($now)));
            DB::table(self::TABLE)->where('id', $id)->where('user_id', $request->user()->id)
                ->where('status','active')->update([
                    'status' => $status,
                    'ended_at' => $now,
                    'duration_seconds' => $duration,
                    'updated_at' => $now,
                ]);
            return DB::table(self::TABLE)->where('id', $id)->first();
        });
        return response()->json([
            'ok' => true, 'session_id' => (int) $record->id,
            'status' => $record->status,
            'observed_seconds' => (int) ($record->observed_seconds ?? 0),
            'message' => $record->status === 'finished'
                ? 'Your app visit was saved to your private learning history.'
                : 'The app visit was closed without recording a completion.',
            'activity_url' => route('studybuddy.learning.activity'),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function insights(Request $request): View
    {
        $this->ready();
        $user = $request->user();
        $history = DB::table(self::TABLE)->where('user_id', $user->id)->orderByDesc('started_at')
            ->orderByDesc('id')->limit(150)->get();
        $finished = $history->where('status', 'finished');
        $connected = $history->filter(fn ($s) => ($s->launch_source ?? null) === 'web_launcher');
        $allApps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get()
            ->filter(fn ($app) => $this->canOpen($app, $user));
        $byApp = $history->groupBy('app_slug')->map(function ($records) {
            return (object) [
                'name' => $records->first()->app_name,
                'slug' => $records->first()->app_slug,
                'visits' => $records->count(),
                'saved' => $records->where('status','finished')->count(),
                'observed_seconds' => (int) $records->sum('observed_seconds'),
                'last_at' => $records->first()->started_at,
            ];
        })->sortByDesc('last_at')->take(8);
        $visitedSlugs = $history->pluck('app_slug')->unique();
        $suggested = $allApps->reject(fn ($a) => $visitedSlugs->contains($a->slug))
            ->take(4)->values();
        return view('learning.w31.insights', [
            'recent' => $history->take(6),
            'byApp' => $byApp,
            'suggested' => $suggested,
            'savedCount' => $finished->count(),
            'openedCount' => $connected->count(),
            'estimatedMinutes' => (int) floor($connected->sum('observed_seconds') / 60),
            'uniqueApps' => $visitedSlugs->count(),
            'limitedWindow' => $history->count() === 150,
        ]);
    }
}
