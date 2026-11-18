<?php

namespace App\Http\Controllers;

use App\Support\StudyBuddyDailyPulse as Pulse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Account-only planner. No database migrations and no child/teacher permission changes. */
final class StudyBuddyDailyPulseController extends Controller
{
    public function show(Request $request): View
    {
        $raw = (string) $request->query('month', now()->format('Y-m'));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $raw)) $raw = now()->format('Y-m');
        $month = Carbon::createFromFormat('!Y-m', $raw) ?: now()->startOfMonth();
        $selected = (string) $request->query('day', '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $selected) || !str_starts_with($selected, $month->format('Y-m'))
            || !checkdate((int) substr($selected, 5, 2), (int) substr($selected, 8, 2), (int) substr($selected, 0, 4))) {
            $selected = $month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString();
        }
        $user = $request->user();
        $profile = Pulse::profile($user);
        $notices = Pulse::notices($user);
        $entries = Pulse::calendar($user, $month);
        $weekly = Pulse::weekly($user);
        $max = max(1, ...array_map(fn ($row) => $row['visits'] + $row['rounds'], $weekly));
        return view('studybuddy.p09.pulse', [
            'month' => $month, 'selected' => $selected, 'entries' => $entries,
            'selectedEntries' => $entries->filter(fn ($row) => $row['date'] === $selected)->values(),
            'notices' => $notices, 'unread' => $notices->where('read', false)->count(),
            'settingsP09' => Pulse::settings($profile), 'plansCount' => Pulse::plans($profile)->count(),
            'weekly' => $weekly, 'chartMax' => $max,
            'teacherProgress' => $user->normalizedRole() === 'teacher'
                ? \App\Support\StudyBuddyClassProgress::forTeacher($user) : null,
        ]);
    }

    public function addPlan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:2', 'max:110'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:+2 years'],
            'time' => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:240'],
        ]);
        $user = $request->user();
        $profile = Pulse::profile($user);
        $plans = Pulse::plans($profile)->all();
        if (count($plans) >= 40) throw ValidationException::withMessages([
            'title' => 'You have 40 plans already. Remove an old plan to add a new one.',
        ]);
        $plans[] = ['id' => (string) Str::uuid(), 'title' => trim($data['title']),
            'date' => $data['date'], 'time' => ($data['time'] ?? null) ?: null,
            'notes' => trim($data['notes'] ?? '')];
        $profile['sb_p09_plans'] = $plans;
        $user->role_profile = $profile;
        $user->save();
        return redirect()->route('studybuddy.p09.pulse', ['month' => substr($data['date'], 0, 7), 'day' => $data['date']])
            ->with('p09_status', 'Your plan was saved to your private account.');
    }

    public function deletePlan(Request $request, string $id): RedirectResponse
    {
        $user = $request->user();
        $profile = Pulse::profile($user);
        $plans = Pulse::plans($profile);
        abort_unless($plans->contains(fn ($p) => $p['id'] === $id), 404);
        $profile['sb_p09_plans'] = $plans->reject(fn ($p) => $p['id'] === $id)->values()->all();
        $user->role_profile = $profile;
        $user->save();
        return back()->with('p09_status', 'Plan removed.');
    }

    public function markRead(Request $request): RedirectResponse
    {
        $data = $request->validate(['notice' => ['required', 'string', 'max:120']]);
        $user = $request->user();
        $notices = Pulse::notices($user);
        $valid = $notices->pluck('id')->all();
        $requested = $data['notice'] === 'all' ? $valid : [$data['notice']];
        if ($data['notice'] !== 'all' && !in_array($data['notice'], $valid, true)) abort(404);
        $profile = Pulse::profile($user);
        $existing = array_values(array_filter(is_array($profile['sb_p09_seen'] ?? null) ? $profile['sb_p09_seen'] : [], 'is_string'));
        $profile['sb_p09_seen'] = array_slice(array_values(array_unique(array_merge($existing, $requested))), -120);
        $user->role_profile = $profile;
        $user->save();
        return back()->with('p09_status', 'Notifications marked as read.');
    }

    public function preferences(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'quiet_start' => ['required', 'date_format:H:i'],
            'quiet_end' => ['required', 'date_format:H:i'],
        ]);
        $user = $request->user();
        $profile = Pulse::profile($user);
        $profile['sb_p09_settings'] = [
            'assignment_alerts' => $request->boolean('assignment_alerts'),
            'plan_alerts' => $request->boolean('plan_alerts'),
            'quiet_enabled' => $request->boolean('quiet_enabled'),
            'quiet_start' => $data['quiet_start'],
            'quiet_end' => $data['quiet_end'],
        ];
        $user->role_profile = $profile;
        $user->save();
        return back()->with('p09_status', 'In-app notification preferences saved.');
    }
}
