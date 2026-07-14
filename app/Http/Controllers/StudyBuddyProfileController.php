<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyBuddyProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profile = $this->profileArray($user->role_profile ?? null);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
            'apps' => $this->apps(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'real_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'country' => ['nullable', 'string', 'max:90'],
            'learning_stage' => ['nullable', 'string', 'max:120'],
            'avatar_style' => ['nullable', 'string', 'max:80'],

            'headline' => ['nullable', 'string', 'max:140'],
            'bio' => ['nullable', 'string', 'max:600'],
            'favorite_subjects' => ['nullable', 'string', 'max:190'],
            'learning_goal' => ['nullable', 'string', 'max:190'],
            'current_focus' => ['nullable', 'string', 'max:190'],
            'profile_theme' => ['nullable', 'string', 'max:60'],
            'profile_mood' => ['nullable', 'string', 'max:80'],
            'favorite_app_slugs' => ['nullable', 'array'],
            'favorite_app_slugs.*' => ['string', 'max:120'],

            'public_profile_enabled' => ['nullable'],
            'show_points' => ['nullable'],
            'show_role' => ['nullable'],
        ]);

        $profile = $this->profileArray($user->role_profile ?? null);

        $profile = array_merge($profile, [
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'favorite_subjects' => $data['favorite_subjects'] ?? null,
            'learning_goal' => $data['learning_goal'] ?? null,
            'current_focus' => $data['current_focus'] ?? null,
            'profile_theme' => $data['profile_theme'] ?? 'cosmic',
            'profile_mood' => $data['profile_mood'] ?? null,
            'favorite_app_slugs' => array_values($data['favorite_app_slugs'] ?? []),
            'public_profile_enabled' => $request->boolean('public_profile_enabled'),
            'show_points' => $request->boolean('show_points'),
            'show_role' => $request->boolean('show_role'),
        ]);

        $payload = [
            'name' => $data['name'],
            'real_name' => $data['real_name'] ?? $data['name'],
            'email' => $data['email'],
            'country' => $data['country'] ?? null,
            'learning_stage' => $data['learning_stage'] ?? null,
            'avatar_style' => $data['avatar_style'] ?? ($user->avatar_style ?? 'dolphin-cadet'),
            'role_profile' => $profile,
            'updated_at' => now(),
        ];

        foreach ($payload as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $value;
            }
        }

        $user->save();

        return back()->with('status', 'Profile updated. Your StudyBuddy space looks fresh.');
    }

    public function show(User $user): View
    {
        $viewer = Auth::user();
        $profile = $this->profileArray($user->role_profile ?? null);
        $isOwner = $viewer && (int) $viewer->id === (int) $user->id;
        $isAdmin = $viewer && (($viewer->is_admin ?? false) || ($viewer->role ?? null) === 'admin');

        if (!($profile['public_profile_enabled'] ?? false) && !$isOwner && !$isAdmin) {
            abort(404);
        }

        return view('profile.public', [
            'profileUser' => $user,
            'profile' => $profile,
            'apps' => $this->appsForProfile($profile),
            'isOwner' => $isOwner,
        ]);
    }

    public function community(): View
    {
        $profiles = collect();

        if (Schema::hasTable('users')) {
            $profiles = User::query()
                ->where('is_admin', false)
                ->latest('id')
                ->limit(120)
                ->get()
                ->filter(function ($user) {
                    $profile = $this->profileArray($user->role_profile ?? null);
                    return (bool) ($profile['public_profile_enabled'] ?? false);
                })
                ->values();
        }

        return view('profile.community', [
            'profiles' => $profiles,
        ]);
    }

    private function apps(): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('studybuddy_mini_app_platforms')) {
            return collect();
        }

        return collect(DB::table('studybuddy_mini_app_platforms')
            ->where(function ($query) {
                if (Schema::hasColumn('studybuddy_mini_app_platforms', 'is_active')) {
                    $query->where('is_active', true);
                }
            })
            ->orderBy(Schema::hasColumn('studybuddy_mini_app_platforms', 'sort_order') ? 'sort_order' : 'id')
            ->get());
    }

    private function appsForProfile(array $profile): \Illuminate\Support\Collection
    {
        $slugs = collect($profile['favorite_app_slugs'] ?? [])->filter()->values();

        if ($slugs->isEmpty()) {
            return $this->apps()->take(4);
        }

        return $this->apps()
            ->filter(fn ($app) => $slugs->contains($app->slug ?? null))
            ->values();
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
}
