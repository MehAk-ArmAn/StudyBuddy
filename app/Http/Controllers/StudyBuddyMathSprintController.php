<?php

namespace App\Http\Controllers;

use App\Support\StudyBuddyRewardIntegrity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * W32: first-party, server-graded math mini-app.
 * Unlike W31 visit tracking, these results are graded against server-held answers.
 * This still isn't a proctored assessment and NEVER changes the legacy points wallet.
 */
class StudyBuddyMathSprintController extends Controller
{
    private const RUNS = 'studybuddy_w32_math_runs';
    private const BADGES = 'studybuddy_w32_achievements';
    private const SESSIONS = 'studybuddy_learning_sessions';
    private const SLUG = 'studybuddy-math-sprint';
    private const QUESTION_COUNT = 5;

    private function requireSchema(): void
    {
        abort_unless(Schema::hasTable(self::RUNS) && Schema::hasTable(self::BADGES)
            && Schema::hasTable(self::SESSIONS) && Schema::hasColumn(self::SESSIONS, 'launch_source'), 503,
            'Math Sprint needs W30, W31 and W32 migrations.');
    }

    private function questions(string $level): array
    {
        $items = [];
        for ($i = 0; $i < self::QUESTION_COUNT; $i++) {
            $max = $level === 'explorer' ? 45 : 14;
            $a = random_int(1, $max);
            $b = random_int(1, $max);
            $minus = ($i % 2 === 1);
            if ($minus && $a < $b) [$a, $b] = [$b, $a];
            $items[] = [
                'prompt' => $a . ($minus ? ' − ' : ' + ') . $b . ' = ?',
                'answer' => $minus ? $a - $b : $a + $b,
            ];
        }
        return $items;
    }

    private function activeFor(int $userId): ?object
    {
        return DB::table(self::RUNS)->where('user_id', $userId)
            ->where('status', 'active')->orderByDesc('id')->first();
    }

    private function tooOld(object $run): bool
    {
        return Carbon::parse($run->started_at)->lte(now()->subMinutes(30));
    }

    private function expire(object $run): void
    {
        DB::table(self::RUNS)->where('id', $run->id)->where('status', 'active')
            ->update(['status' => 'expired', 'updated_at' => now()]);
        if ($run->learning_session_id) {
            DB::table(self::SESSIONS)->where('id', $run->learning_session_id)
                ->where('user_id', $run->user_id)->where('status', 'active')
                ->update(['status' => 'left', 'ended_at' => now(), 'duration_seconds' => 0, 'updated_at' => now()]);
        }
    }

    public function index(Request $request): View
    {
        $this->requireSchema();
        $userId = (int) $request->user()->id;
        DB::transaction(function () use ($userId): void {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();
            if (($run = $this->activeFor($userId)) && $this->tooOld($run)) $this->expire($run);
        });
        $run = $this->activeFor($userId);
        $questions = $run ? json_decode($run->questions_json, true, 512, JSON_THROW_ON_ERROR) : [];
        $achievements = DB::table(self::BADGES)->where('user_id', $userId)->pluck('code')->all();
        $recent = DB::table(self::RUNS)->where('user_id', $userId)
            ->where('status', 'completed')->orderByDesc('submitted_at')->limit(5)->get();
        return view('learning.w32.math-sprint', compact('run', 'questions', 'achievements', 'recent'));
    }

    public function start(Request $request): RedirectResponse
    {
        $this->requireSchema();
        $data = $request->validate(['level' => ['required', 'in:starter,explorer']]);
        $userId = (int) $request->user()->id;
        $outcome = DB::transaction(function () use ($userId, $data): array {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();
            $active = $this->activeFor($userId);
            if ($active && $this->tooOld($active)) {
                $this->expire($active);
                $active = null;
            }
            if ($active) return ['ok' => true]; // Resuming does not mint another activity.
            $other = DB::table(self::SESSIONS)->where('user_id', $userId)
                ->where('status', 'active')->first();
            if ($other) return ['ok' => false];
            $now = now();
            $sessionId = DB::table(self::SESSIONS)->insertGetId([
                'user_id' => $userId, 'app_slug' => self::SLUG, 'app_name' => 'Math Sprint',
                'status' => 'active', 'started_at' => $now, 'launch_source' => 'w32_server_graded',
                'observed_seconds' => 0, 'last_observed_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table(self::RUNS)->insert([
                'user_id' => $userId, 'learning_session_id' => $sessionId,
                'level' => $data['level'], 'status' => 'active',
                'questions_json' => json_encode($this->questions($data['level']), JSON_THROW_ON_ERROR),
                'total' => self::QUESTION_COUNT, 'started_at' => $now,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            return ['ok' => true];
        });
        if (! $outcome['ok']) return back()->withErrors(['session' => 'Finish or leave your other active learning session before starting Math Sprint.']);
        return redirect()->route('studybuddy.w32.index');
    }

    public function submit(Request $request, int $run): RedirectResponse
    {
        $this->requireSchema();
        $answers = $request->validate([
            'answers' => ['required', 'array', 'size:'.self::QUESTION_COUNT],
            'answers.*' => ['required', 'integer', 'between:-1000,1000'],
        ])['answers'];
        // Disallow omitted or unusual keys: exactly answers[0..4] in sequence.
        if (array_keys($answers) !== range(0, self::QUESTION_COUNT - 1)) {
            return back()->withErrors(['answers' => 'Please answer all five questions.']);
        }
        $userId = (int) $request->user()->id;
        $outcome = DB::transaction(function () use ($userId, $run, $answers): string {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();
            $record = DB::table(self::RUNS)->where('id', $run)->where('user_id', $userId)
                ->lockForUpdate()->first();
            abort_unless($record, 404);
            if ($record->status === 'completed') return 'completed'; // Idempotent retry, no duplicate badges.
            if ($record->status !== 'active') return 'expired';
            if ($this->tooOld($record)) {
                $this->expire($record);
                return 'expired';
            }
            $questions = json_decode($record->questions_json, true, 512, JSON_THROW_ON_ERROR);
            if (count($questions) !== self::QUESTION_COUNT) abort(503, 'Question integrity failure.');
            $score = 0;
            foreach ($questions as $i => $question) {
                if ((int) $answers[$i] === (int) $question['answer']) $score++;
            }
            $now = now();
            DB::table(self::RUNS)->where('id', $run)->where('user_id', $userId)
                ->update(['status' => 'completed', 'score' => $score, 'submitted_at' => $now, 'updated_at' => $now]);
            if ($record->learning_session_id) {
                DB::table(self::SESSIONS)->where('id', $record->learning_session_id)
                    ->where('user_id', $userId)->where('status', 'active')
                    ->update(['status' => 'finished', 'ended_at' => $now,
                        'duration_seconds' => max(0, min(1800, (int) Carbon::parse($record->started_at)->diffInSeconds($now))),
                        'updated_at' => $now]);
            }
            if ($score >= 4) $this->award($userId, $run, 'math-first-pass', $now);
            if ($score === 5) $this->award($userId, $run, 'math-perfect-five', $now);
            // First-party questions are checked above against server-held answers.
            // Never trust client score/points fields. Fast-finished rounds earn no points.
            app(StudyBuddyRewardIntegrity::class)->verifiedMathRun(
                $userId, $run, $score,
                max(0, min(1800, (int) Carbon::parse($record->started_at)->diffInSeconds($now))),
                $record->level
            );
            return 'completed';
        });
        if ($outcome !== 'completed') return redirect()->route('studybuddy.w32.index')
            ->withErrors(['session' => 'That challenge expired. Start a new Math Sprint.']);
        return redirect()->route('studybuddy.w32.result', ['run' => $run]);
    }

    private function award(int $userId, int $run, string $code, Carbon $now): void
    {
        DB::table(self::BADGES)->insertOrIgnore([
            'user_id' => $userId, 'run_id' => $run, 'code' => $code,
            'earned_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    public function result(Request $request, int $run): View
    {
        $this->requireSchema();
        $userId = (int) $request->user()->id;
        $record = DB::table(self::RUNS)->where('id', $run)->where('user_id', $userId)
            ->where('status', 'completed')->firstOrFail();
        $achievements = DB::table(self::BADGES)->where('user_id', $userId)->pluck('code')->all();
        return view('learning.w32.result', compact('record', 'achievements'));
    }
}
