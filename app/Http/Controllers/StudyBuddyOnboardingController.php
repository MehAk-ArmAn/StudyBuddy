<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Private, account-scoped onboarding. Never changes authorization, roles or privacy sharing. */
class StudyBuddyOnboardingController extends Controller
{
    private const SUBJECTS = [
        'Math', 'Reading', 'English', 'Science', 'Geography', 'History',
        'Languages', 'Coding', 'Writing', 'Art', 'Focus', 'Study Skills',
    ];

    private const AVATARS = ['dolphin-cadet', 'cosmic-explorer', 'parent-guide', 'teacher-mentor', 'star-builder'];

    public function show(Request $request): View
    {
        $user = $request->user();
        $profile = $this->profile($user->role_profile);
        $setup = $this->setup($profile);
        $step = min(6, max(1, (int) $request->query('step', 1)));
        $role = method_exists($user, 'normalizedRole') ? $user->normalizedRole() : ($user->role ?: 'student');

        return view('studybuddy.p08.onboarding', [
            'user' => $user,
            'profile' => $profile,
            'setup' => $setup,
            'role' => $role,
            'step' => $step,
            'subjects' => self::SUBJECTS,
            'progress' => $this->progress($setup),
            'finished' => (bool) ($setup['finished_at'] ?? false),
            'nextStep' => $this->nextMissing($setup),
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate(['step' => ['required', 'integer', 'between:1,6']]);
        $step = (int) $data['step'];
        $profile = $this->profile($user->role_profile);
        $setup = $this->setup($profile);
        $steps = collect($setup['steps'] ?? [])->map(fn ($s) => (int) $s)->filter(fn ($s) => $s >= 1 && $s <= 5)->unique();

        switch ($step) {
            case 1:
                $values = $request->validate([
                    'display_name' => ['required', 'string', 'min:2', 'max:120'],
                    'avatar_style' => ['required', Rule::in(self::AVATARS)],
                ]);
                $user->name = trim($values['display_name']);
                $user->avatar_style = $values['avatar_style'];
                break;
            case 2:
                $values = $request->validate([
                    'subjects' => ['sometimes', 'array', 'max:6'],
                    'subjects.*' => ['string', Rule::in(self::SUBJECTS)],
                    'learning_goal' => ['required', 'string', 'min:3', 'max:160'],
                ]);
                $profile['favorite_subjects'] = implode(', ', array_unique($values['subjects'] ?? []));
                $profile['learning_goal'] = trim($values['learning_goal']);
                break;
            case 3:
                $values = $request->validate([
                    'dashboard_density' => ['required', Rule::in(['comfortable', 'compact'])],
                    'daily_minutes' => ['required', Rule::in(['5', '10', '20', '30', 'flexible'])],
                    'guide_level' => ['required', Rule::in(['guided', 'standard'])],
                    'accent' => ['required', Rule::in(['violet', 'cyan', 'rose'])],
                ]);
                $setup = array_merge($setup, Arr::only($values, ['dashboard_density', 'daily_minutes', 'guide_level', 'accent']));
                break;
            case 4:
                $request->validate(['privacy_reviewed' => ['accepted']]);
                // This records that the person saw the explainer, not legal consent or approval.
                $setup['privacy_reviewed_at'] = now()->toIso8601String();
                break;
            case 5:
                $values = $request->validate([
                    'next_destination' => ['required', Rule::in(['dashboard', 'apps', 'subjects', 'study-space', 'profile'])],
                ]);
                $setup['next_destination'] = $values['next_destination'];
                break;
            case 6:
                if ($missing = $this->nextMissing($setup)) {
                    return redirect()->route('studybuddy.onboarding.show', ['step' => $missing])
                        ->with('onboarding_notice', 'Complete these setup choices before finishing.');
                }
                $setup['finished_at'] = now()->toIso8601String();
                $profile['sb_p08_setup'] = $setup;
                $user->role_profile = $profile;
                $user->save();
                return redirect()->route('dashboard')->with('status', 'Your StudyBuddy space is ready. You can revisit setup anytime.');
        }

        $steps->push($step);
        $setup['steps'] = $steps->unique()->sort()->values()->all();
        $setup['version'] = 1;
        $profile['sb_p08_setup'] = $setup;
        $user->role_profile = $profile;
        $user->save();

        return redirect()->route('studybuddy.onboarding.show', ['step' => min(6, $step + 1)])
            ->with('onboarding_notice', 'Saved to your private StudyBuddy account.');
    }

    private function profile(mixed $value): array
    {
        if (is_array($value)) return $value;
        $decoded = is_string($value) ? json_decode($value, true) : null;
        return is_array($decoded) ? $decoded : [];
    }

    private function setup(array $profile): array
    {
        return is_array($profile['sb_p08_setup'] ?? null) ? $profile['sb_p08_setup'] : [];
    }

    private function nextMissing(array $setup): ?int
    {
        $done = array_map('intval', is_array($setup['steps'] ?? null) ? $setup['steps'] : []);
        foreach (range(1, 5) as $step) if (!in_array($step, $done, true)) return $step;
        return null;
    }

    private function progress(array $setup): int
    {
        $done = array_unique(array_map('intval', is_array($setup['steps'] ?? null) ? $setup['steps'] : []));
        return (int) (20 * count(array_intersect(range(1, 5), $done)));
    }
}
