<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $profile = $this->profileArray($user->role_profile ?? null);

        $settings = $this->settings();

        $apps = Schema::hasTable('studybuddy_mini_app_platforms')
            ? collect(DB::table('studybuddy_mini_app_platforms')
                ->where(function ($query) {
                    if (Schema::hasColumn('studybuddy_mini_app_platforms', 'is_active')) {
                        $query->where('is_active', true);
                    }
                })
                ->orderBy(Schema::hasColumn('studybuddy_mini_app_platforms', 'sort_order') ? 'sort_order' : 'id')
                ->limit(8)
                ->get())
            : collect();

        $favoriteSlugs = collect($profile['favorite_app_slugs'] ?? [])->filter()->values();

        $recommendedApps = $favoriteSlugs->count()
            ? $apps->filter(fn ($app) => $favoriteSlugs->contains($app->slug ?? null))->values()
            : $apps->take(4)->values();

        if ($recommendedApps->isEmpty()) {
            $recommendedApps = $apps->take(4)->values();
        }

        $recentPoints = Schema::hasTable('studybuddy_point_transactions')
            ? collect(DB::table('studybuddy_point_transactions')
                ->where('user_id', $user->id)
                ->latest('id')
                ->limit(5)
                ->get())
            : collect();

        $savedQuests = Schema::hasTable('saved_quests')
            ? collect(DB::table('saved_quests')
                ->where('user_id', $user->id)
                ->latest('id')
                ->limit(4)
                ->get())
            : collect();

        $leaderboard = Schema::hasTable('users')
            ? collect(DB::table('users')
                ->select('id', 'name', 'role', 'cosmic_points', 'avatar_style', 'role_profile', 'created_at')
                ->where('is_admin', false)
                ->orderByDesc('cosmic_points')
                ->limit(12)
                ->get())
            : collect();

        $rank = $leaderboard->search(fn ($item) => (int) $item->id === (int) $user->id);
        $rank = $rank === false ? null : $rank + 1;

        $completion = $this->profileCompletion($user, $profile);

        return view('dashboard.index', [
            'user' => $user,
            'profile' => $profile,
            'settings' => $settings,
            'apps' => $apps,
            'recommendedApps' => $recommendedApps,
            'recentPoints' => $recentPoints,
            'savedQuests' => $savedQuests,
            'leaderboard' => $leaderboard,
            'rank' => $rank,
            'completion' => $completion,
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
        if (!Schema::hasTable('site_settings')) {
            return [];
        }

        return DB::table('site_settings')->pluck('value', 'key')->all();
    }

    private function profileArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
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

        $done = collect($items)->filter()->count();

        return (int) round(($done / count($items)) * 100);
    }
}
