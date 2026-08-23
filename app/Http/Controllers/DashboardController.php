<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $role = method_exists($user, 'normalizedRole') ? $user->normalizedRole() : ($user->role ?? 'student');
        $profile = $this->profileArray($user->role_profile ?? null);

        $apps = $this->apps();
        $recentPoints = $this->recentPoints($user->id);
        $assignments = $this->learnerAssignments($user);
        $leaderboard = $this->leaderboard();
        $rank = $leaderboard->search(fn ($item) => (int) $item->id === (int) $user->id);
        $rank = $rank === false ? null : $rank + 1;

        return view('dashboard.index', [
            'user' => $user,
            'role' => $role,
            'profile' => $profile,
            'settings' => $this->settings(),
            'apps' => $apps,
            'recommendedApps' => $this->recommendedApps($apps, $profile, $role),
            'recentPoints' => $recentPoints,
            'assignments' => $assignments,
            'leaderboard' => $leaderboard,
            'rank' => $rank,
            'completion' => $this->profileCompletion($user, $profile),
            'parentData' => $this->parentData($user),
            'teacherData' => $this->teacherData($user),
            'learnerData' => $this->learnerData($user, $profile, $recentPoints, $assignments),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        return redirect()->route('profile')
            ->with('status', 'Profile settings now live in Profile Studio.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return back()->with('status', 'Access key updated safely.');
    }

    private function settings(): array
    {
        if (!Schema::hasTable('site_settings')) return [];

        return DB::table('site_settings')->pluck('value', 'key')->all();
    }

    private function apps(): Collection
    {
        if (!Schema::hasTable('studybuddy_mini_app_platforms')) return collect();

        $query = DB::table('studybuddy_mini_app_platforms');

        if (Schema::hasColumn('studybuddy_mini_app_platforms', 'is_active')) {
            $query->where('is_active', true);
        }

        return collect($query
            ->orderBy(Schema::hasColumn('studybuddy_mini_app_platforms', 'sort_order') ? 'sort_order' : 'id')
            ->get());
    }

    private function recommendedApps(Collection $apps, array $profile, string $role): Collection
    {
        $favorites = collect($profile['favorite_app_slugs'] ?? [])->filter()->values();

        if ($favorites->count()) {
            $matched = $apps->filter(fn ($app) => $favorites->contains($app->slug ?? null))->values();
            if ($matched->count()) return $matched->take(4);
        }

        return match ($role) {
            'parent' => $apps->filter(fn ($app) => str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'focus') || str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'reading'))->take(4)->values(),
            'teacher' => $apps->filter(fn ($app) => str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'quiz') || str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'math'))->take(4)->values(),
            'independent_learner' => $apps->filter(fn ($app) => str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'focus') || str_contains(strtolower(($app->category ?? '').' '.($app->tagline ?? '')), 'planner'))->take(4)->values(),
            default => $apps->take(4)->values(),
        };
    }

    private function recentPoints(int $userId): Collection
    {
        if (!Schema::hasTable('studybuddy_point_transactions')) return collect();

        return collect(DB::table('studybuddy_point_transactions')
            ->where('user_id', $userId)
            ->latest('id')
            ->limit(8)
            ->get());
    }

    private function leaderboard(): Collection
    {
        if (!Schema::hasTable('users')) return collect();

        return collect(DB::table('users')
            ->select('id', 'name', 'role', 'cosmic_points', 'avatar_style', 'profile_photo_path', 'role_profile')
            ->where('is_admin', false)
            ->orderByDesc('cosmic_points')
            ->limit(10)
            ->get());
    }

    private function learnerAssignments($user): Collection
    {
        if (!Schema::hasTable('studybuddy_assignment_recipients') || !Schema::hasTable('studybuddy_assignments')) {
            return collect();
        }

        return collect(DB::table('studybuddy_assignment_recipients as r')
            ->join('studybuddy_assignments as a', 'a.id', '=', 'r.assignment_id')
            ->where(function ($query) use ($user) {
                $query->where('r.user_id', $user->id)
                    ->orWhere('r.email', $user->email);
            })
            ->select('r.*', 'a.title', 'a.type', 'a.app_slug', 'a.instructions', 'a.due_at', 'a.points_reward')
            ->latest('a.id')
            ->limit(8)
            ->get());
    }

    private function parentData($user): array
    {
        $children = collect();
        $familyGroup = null;

        if (Schema::hasTable('studybuddy_learning_groups')) {
            $familyGroup = DB::table('studybuddy_learning_groups')
                ->where('owner_id', $user->id)
                ->where('type', 'family')
                ->first();
        }

        if ($familyGroup && Schema::hasTable('studybuddy_group_members')) {
            $children = collect(DB::table('studybuddy_group_members')
                ->where('group_id', $familyGroup->id)
                ->where('owner_id', $user->id)
                ->orderBy('display_name')
                ->get());
        }

        $childUsers = collect();

        if ($children->count() && Schema::hasTable('users')) {
            $emails = $children->pluck('email')->filter()->unique()->values();
            $ids = $children->pluck('user_id')->filter()->unique()->values();

            $childUsers = collect(DB::table('users')
                ->where(function ($q) use ($emails, $ids) {
                    if ($emails->count()) $q->orWhereIn('email', $emails);
                    if ($ids->count()) $q->orWhereIn('id', $ids);
                })
                ->get());
        }

        $childIds = $childUsers->pluck('id')->filter()->values();

        $childActivity = collect();
        if ($childIds->count() && Schema::hasTable('studybuddy_point_transactions')) {
            $childActivity = collect(DB::table('studybuddy_point_transactions as p')
                ->join('users as u', 'u.id', '=', 'p.user_id')
                ->whereIn('p.user_id', $childIds)
                ->select('p.*', 'u.name as learner_name', 'u.email as learner_email')
                ->latest('p.id')
                ->limit(14)
                ->get());
        }

        return [
            'familyGroup' => $familyGroup,
            'children' => $children,
            'childUsers' => $childUsers,
            'childActivity' => $childActivity,
            'metrics' => [
                'children' => $children->count(),
                'total_points' => (int) $childUsers->sum('cosmic_points'),
                'avg_points' => $childUsers->count() ? (int) round($childUsers->avg('cosmic_points')) : 0,
                'recent_events' => $childActivity->count(),
            ],
        ];
    }

    private function teacherData($user): array
    {
        $groups = collect();
        $members = collect();
        $assignments = collect();
        $studentUsers = collect();
        $studentActivity = collect();

        if (Schema::hasTable('studybuddy_learning_groups')) {
            $groups = collect(DB::table('studybuddy_learning_groups')
                ->where('owner_id', $user->id)
                ->where('type', 'class')
                ->latest('id')
                ->get());
        }

        if ($groups->count() && Schema::hasTable('studybuddy_group_members')) {
            $members = collect(DB::table('studybuddy_group_members')
                ->where('owner_id', $user->id)
                ->whereIn('group_id', $groups->pluck('id'))
                ->orderBy('display_name')
                ->get());
        }

        if ($members->count() && Schema::hasTable('users')) {
            $emails = $members->pluck('email')->filter()->unique()->values();
            $ids = $members->pluck('user_id')->filter()->unique()->values();

            $studentUsers = collect(DB::table('users')
                ->where(function ($q) use ($emails, $ids) {
                    if ($emails->count()) $q->orWhereIn('email', $emails);
                    if ($ids->count()) $q->orWhereIn('id', $ids);
                })
                ->get());
        }

        if ($studentUsers->count() && Schema::hasTable('studybuddy_point_transactions')) {
            $studentActivity = collect(DB::table('studybuddy_point_transactions as p')
                ->join('users as u', 'u.id', '=', 'p.user_id')
                ->whereIn('p.user_id', $studentUsers->pluck('id'))
                ->select('p.*', 'u.name as learner_name', 'u.email as learner_email')
                ->latest('p.id')
                ->limit(18)
                ->get());
        }

        if (Schema::hasTable('studybuddy_assignments')) {
            $assignments = collect(DB::table('studybuddy_assignments')
                ->where('owner_id', $user->id)
                ->latest('id')
                ->limit(14)
                ->get());
        }

        return [
            'groups' => $groups,
            'members' => $members,
            'studentUsers' => $studentUsers,
            'studentActivity' => $studentActivity,
            'assignments' => $assignments,
            'metrics' => [
                'classes' => $groups->count(),
                'students' => $members->count(),
                'assignments' => $assignments->count(),
                'activity' => $studentActivity->count(),
            ],
        ];
    }

    private function learnerData($user, array $profile, Collection $recentPoints, Collection $assignments): array
    {
        $connectCode = $this->ensureConnectCode($user, $profile);

        return [
            'connect_code' => $connectCode,
            'metrics' => [
                'points' => (int) ($user->cosmic_points ?? 0),
                'assignments' => $assignments->where('status', 'assigned')->count(),
                'recent_events' => $recentPoints->count(),
            ],
            'focus' => $profile['current_focus'] ?? $user->learning_stage ?? 'Build confidence',
            'goal' => $profile['learning_goal'] ?? 'Complete one tiny win today',
        ];
    }

    private function ensureConnectCode($user, array $profile): string
    {
        if (!empty($profile['connect_code'])) {
            return strtoupper((string) $profile['connect_code']);
        }

        $profile['connect_code'] = strtoupper(Str::random(8));

        if (Schema::hasColumn('users', 'role_profile')) {
            DB::table('users')->where('id', $user->id)->update([
                'role_profile' => json_encode($profile, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }

        return $profile['connect_code'];
    }

    private function profileCompletion($user, array $profile): int
    {
        $items = [
            filled($user->name ?? null),
            filled($user->email ?? null),
            filled($user->role ?? null),
            filled($user->country ?? null),
            filled($user->learning_stage ?? null),
            filled($profile['headline'] ?? null),
            filled($profile['bio'] ?? null),
            filled($profile['favorite_subjects'] ?? null),
            filled($profile['learning_goal'] ?? null),
            !empty($profile['favorite_app_slugs'] ?? []),
        ];

        return (int) round((collect($items)->filter()->count() / count($items)) * 100);
    }

    private function profileArray($value): array
    {
        if (is_array($value)) return $value;

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
