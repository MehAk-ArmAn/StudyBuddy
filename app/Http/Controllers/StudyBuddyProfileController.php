<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
            'customizations' => $this->customizationCatalog(),
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
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],

            'headline' => ['nullable', 'string', 'max:140'],
            'bio' => ['nullable', 'string', 'max:600'],
            'favorite_subjects' => ['nullable', 'string', 'max:190'],
            'learning_goal' => ['nullable', 'string', 'max:190'],
            'current_focus' => ['nullable', 'string', 'max:190'],
            'profile_mood' => ['nullable', 'string', 'max:80'],

            'profile_theme' => ['nullable', 'string', 'max:60'],
            'profile_frame' => ['nullable', 'string', 'max:60'],
            'profile_badge' => ['nullable', 'string', 'max:80'],
            'profile_color' => ['nullable', 'string', 'max:60'],
            'avatar_shape' => ['nullable', 'string', 'max:60'],

            'favorite_app_slugs' => ['nullable', 'array'],
            'favorite_app_slugs.*' => ['string', 'max:120'],

            'public_profile_enabled' => ['nullable'],
            'show_points' => ['nullable'],
            'show_role' => ['nullable'],
            'show_favorite_apps' => ['nullable'],
        ]);

        $catalog = $this->customizationCatalog();
        $profile = $this->profileArray($user->role_profile ?? null);

        $selected = [
            'profile_theme' => $data['profile_theme'] ?? ($profile['profile_theme'] ?? 'cosmic'),
            'profile_frame' => $data['profile_frame'] ?? ($profile['profile_frame'] ?? 'none'),
            'profile_badge' => $data['profile_badge'] ?? ($profile['profile_badge'] ?? 'learning-spark'),
            'profile_color' => $data['profile_color'] ?? ($profile['profile_color'] ?? 'purple'),
            'avatar_shape' => $data['avatar_shape'] ?? ($profile['avatar_shape'] ?? 'rounded'),
        ];

        foreach ($selected as $field => $value) {
            if (!isset($catalog[$field][$value])) {
                throw ValidationException::withMessages([$field => 'That profile style is not available yet.']);
            }
        }

        $unlocked = collect($profile['unlocked_profile_items'] ?? [
            'profile_theme:cosmic',
            'profile_theme:ocean',
            'profile_theme:forest',
            'profile_frame:none',
            'profile_badge:learning-spark',
            'profile_color:purple',
            'profile_color:cyan',
            'avatar_shape:rounded',
            'avatar_shape:circle',
        ])->unique()->values();

        $newUnlocks = [];
        $totalCost = 0;

        foreach ($selected as $field => $value) {
            $key = $field.':'.$value;
            $cost = (int) ($catalog[$field][$value]['cost'] ?? 0);

            if ($cost > 0 && !$unlocked->contains($key)) {
                $newUnlocks[] = $key;
                $totalCost += $cost;
            }
        }

        if ($totalCost > (int) ($user->cosmic_points ?? 0)) {
            throw ValidationException::withMessages([
                'profile_theme' => "You need {$totalCost} coins to unlock those profile styles.",
            ]);
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            if (Schema::hasColumn('users', 'profile_photo_path')) {
                $old = $user->profile_photo_path ?? null;
                if ($old && !preg_match('/^https?:\/\//i', $old)) {
                    Storage::disk('public')->delete($old);
                }
                $user->profile_photo_path = $path;
            }
        }

        if ($totalCost > 0 && Schema::hasColumn('users', 'cosmic_points')) {
            $user->cosmic_points = max(0, (int) ($user->cosmic_points ?? 0) - $totalCost);
        }

        $profile = array_merge($profile, [
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'favorite_subjects' => $data['favorite_subjects'] ?? null,
            'learning_goal' => $data['learning_goal'] ?? null,
            'current_focus' => $data['current_focus'] ?? null,
            'profile_mood' => $data['profile_mood'] ?? null,
            'favorite_app_slugs' => array_values($data['favorite_app_slugs'] ?? []),
            'profile_theme' => $selected['profile_theme'],
            'profile_frame' => $selected['profile_frame'],
            'profile_badge' => $selected['profile_badge'],
            'profile_color' => $selected['profile_color'],
            'avatar_shape' => $selected['avatar_shape'],
            'unlocked_profile_items' => $unlocked->merge($newUnlocks)->unique()->values()->all(),
            'public_profile_enabled' => $request->boolean('public_profile_enabled'),
            'show_points' => $request->boolean('show_points'),
            'show_role' => $request->boolean('show_role'),
            'show_favorite_apps' => $request->boolean('show_favorite_apps', true),
        ]);

        foreach ([
            'name' => $data['name'],
            'real_name' => $data['real_name'] ?? $data['name'],
            'email' => $data['email'],
            'country' => $data['country'] ?? null,
            'learning_stage' => $data['learning_stage'] ?? null,
            'avatar_style' => $data['avatar_style'] ?? ($user->avatar_style ?? 'dolphin-cadet'),
            'role_profile' => $profile,
        ] as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user->{$column} = $value;
            }
        }

        $user->save();

        return back()->with('status', $totalCost > 0
            ? "Profile updated. {$totalCost} coins spent on new showcase styles."
            : 'Profile updated. Your showcase looks fresh.'
        );
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
            'customizations' => $this->customizationCatalog(),
        ]);
    }

    public function community(): View
    {
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

        return view('profile.community', [
            'profiles' => $profiles,
            'customizations' => $this->customizationCatalog(),
        ]);
    }

    private function apps(): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('studybuddy_mini_app_platforms')) return collect();

        return collect(DB::table('studybuddy_mini_app_platforms')
            ->orderBy(Schema::hasColumn('studybuddy_mini_app_platforms', 'sort_order') ? 'sort_order' : 'id')
            ->get());
    }

    private function appsForProfile(array $profile): \Illuminate\Support\Collection
    {
        $slugs = collect($profile['favorite_app_slugs'] ?? [])->filter()->values();
        $apps = $this->apps();

        if ($slugs->isEmpty()) return $apps->take(4);

        return $apps->filter(fn ($app) => $slugs->contains($app->slug ?? null))->values();
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

    private function customizationCatalog(): array
    {
        return [
            'profile_theme' => [
                'cosmic' => ['label' => 'Cosmic Purple', 'cost' => 0],
                'ocean' => ['label' => 'Ocean Blue', 'cost' => 0],
                'forest' => ['label' => 'Focus Forest', 'cost' => 0],
                'sunrise' => ['label' => 'Planner Sunrise', 'cost' => 150],
                'rose' => ['label' => 'Spelling Rose', 'cost' => 250],
                'neon' => ['label' => 'Neon Galaxy', 'cost' => 400],
            ],
            'profile_frame' => [
                'none' => ['label' => 'Clean', 'cost' => 0],
                'glow' => ['label' => 'Soft Glow', 'cost' => 80],
                'crystal' => ['label' => 'Crystal Frame', 'cost' => 180],
                'royal' => ['label' => 'Royal Frame', 'cost' => 300],
                'galaxy' => ['label' => 'Galaxy Ring', 'cost' => 450],
            ],
            'profile_badge' => [
                'learning-spark' => ['label' => 'Learning Spark', 'cost' => 0],
                'focus-friend' => ['label' => 'Focus Friend', 'cost' => 75],
                'quiz-star' => ['label' => 'Quiz Star', 'cost' => 120],
                'reading-bloom' => ['label' => 'Reading Bloom', 'cost' => 150],
                'cosmic-legend' => ['label' => 'Cosmic Legend', 'cost' => 500],
            ],
            'profile_color' => [
                'purple' => ['label' => 'Purple', 'cost' => 0],
                'cyan' => ['label' => 'Cyan', 'cost' => 0],
                'green' => ['label' => 'Green', 'cost' => 60],
                'gold' => ['label' => 'Gold', 'cost' => 160],
                'pink' => ['label' => 'Pink', 'cost' => 220],
            ],
            'avatar_shape' => [
                'rounded' => ['label' => 'Rounded', 'cost' => 0],
                'circle' => ['label' => 'Circle', 'cost' => 0],
                'squircle' => ['label' => 'Squircle', 'cost' => 90],
                'diamond' => ['label' => 'Diamond', 'cost' => 180],
            ],
        ];
    }
}
