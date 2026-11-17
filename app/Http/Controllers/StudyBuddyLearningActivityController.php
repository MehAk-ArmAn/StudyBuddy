<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Learner-owned activity journal. Sessions represent a learner opening and
 * closing an activity, NOT independently assessed learning outcomes.
 */
class StudyBuddyLearningActivityController extends Controller
{
    public function index(Request $request): View
    {
        $this->requireSchema();
        $user = $request->user();
        $sessions = DB::table('studybuddy_learning_sessions')
            ->where('user_id', $user->id)->latest('started_at')->latest('id')->paginate(12);
        $active = DB::table('studybuddy_learning_sessions')
            ->where('user_id', $user->id)->where('status', 'active')->latest('id')->first();

        $apps = StudyBuddyMiniAppPlatform::query()->active()->orderBy('name')->get()
            ->filter(fn ($app) => $this->canOpen($app, $user))->values();

        $finished = DB::table('studybuddy_learning_sessions')
            ->where('user_id', $user->id)->where('status', 'finished');

        return view('learning.activity', [
            'apps' => $apps,
            'sessions' => $sessions,
            'active' => $active,
            'totalFinished' => (clone $finished)->count(),
            'minutesLogged' => (int) floor((clone $finished)->sum('duration_seconds') / 60),
            'daysExplored' => (clone $finished)->selectRaw('date(started_at) as study_day')->distinct()->get()->count(),
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $this->requireSchema();
        $data = $request->validate(['app_slug' => ['required', 'string', 'max:120']]);
        $user = $request->user();
        $app = StudyBuddyMiniAppPlatform::query()->active()
            ->where('slug', $data['app_slug'])->firstOrFail();

        if (! $this->canOpen($app, $user)) {
            throw ValidationException::withMessages([
                'app_slug' => 'This learning app is not available for this account.',
            ]);
        }

        DB::transaction(function () use ($user, $app) {
            // Serialize starts for the same user, including concurrent browser tabs.
            DB::table('users')->where('id', $user->id)->lockForUpdate()->first();
            $existing = DB::table('studybuddy_learning_sessions')
                ->where('user_id', $user->id)->where('status', 'active')->first();
            if ($existing) {
                throw ValidationException::withMessages([
                    'app_slug' => 'Finish or leave your current session before starting another.',
                ]);
            }
            DB::table('studybuddy_learning_sessions')->insert([
                'user_id' => $user->id,
                'app_slug' => $app->slug,
                'app_name' => $app->name,
                'status' => 'active',
                'started_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('studybuddy.learning.activity')
            ->with('status', 'Session started. When you finish exploring, come back and save it here.');
    }

    public function finish(Request $request, int $session): RedirectResponse
    {
        $this->requireSchema();
        $data = $request->validate([
            'reflection' => ['nullable', 'string', 'max:280'],
        ]);
        $this->closeSession($request, $session, 'finished', $data['reflection'] ?? null);
        return redirect()->route('studybuddy.learning.activity')
            ->with('status', 'Saved to your private activity journal. No points or achievement claims were added.');
    }

    public function leave(Request $request, int $session): RedirectResponse
    {
        $this->requireSchema();
        $this->closeSession($request, $session, 'left', null);
        return redirect()->route('studybuddy.learning.activity')
            ->with('status', 'Session closed without recording a completed activity.');
    }

    public function destroy(Request $request, int $session): RedirectResponse
    {
        $this->requireSchema();
        DB::table('studybuddy_learning_sessions')->where('id', $session)
            ->where('user_id', $request->user()->id)->delete();
        return redirect()->route('studybuddy.learning.activity')
            ->with('status', 'Activity entry deleted.');
    }

    private function closeSession(Request $request, int $id, string $status, ?string $reflection): void
    {
        DB::transaction(function () use ($request, $id, $status, $reflection) {
            $session = DB::table('studybuddy_learning_sessions')->where('id', $id)
                ->where('user_id', $request->user()->id)->lockForUpdate()->first();
            abort_unless($session, 404);
            if ($session->status !== 'active') {
                throw ValidationException::withMessages(['session' => 'This session has already been closed.']);
            }

            $seconds = max(0, min(21600, (int) now()->diffInSeconds(
                \Illuminate\Support\Carbon::parse($session->started_at), true
            )));

            DB::table('studybuddy_learning_sessions')->where('id', $id)
                ->where('user_id', $request->user()->id)->where('status', 'active')
                ->update([
                    'status' => $status,
                    'ended_at' => now(),
                    'duration_seconds' => $seconds,
                    'reflection' => $status === 'finished' ? $reflection : null,
                    'updated_at' => now(),
                ]);
        });
    }

    private function canOpen(StudyBuddyMiniAppPlatform $app, $user): bool
    {
        $role = method_exists($user, 'normalizedRole')
            ? $user->normalizedRole() : ($user->role ?? 'student');
        if (! $app->visibleForRole($role) || ! $app->hasPublishedWebApp()) {
            return false;
        }

        if (! empty($user->date_of_birth)) {
            $age = \Illuminate\Support\Carbon::parse($user->date_of_birth)->age;
            if ($app->age_min !== null && $age < $app->age_min) return false;
            if ($app->age_max !== null && $age > $app->age_max) return false;
        }
        return true;
    }

    private function requireSchema(): void
    {
        abort_unless(Schema::hasTable('studybuddy_learning_sessions'), 503,
            'StudyBuddy learning activity requires its W30 migration.');
    }
}
