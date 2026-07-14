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

            'profile_theme' => ['required', 'string', 'max:60'],
            'profile_frame' => ['required', 'string', 'max:60'],
            'profile_badge' => ['required', 'string', 'max:80'],
            'profile_color' => ['required', 'string', 'max:60'],
            'avatar_shape' => ['required', 'string', 'max:60'],
            'profile_mood' => ['nullable', 'string', 'max:80'],

            'favorite_app_slugs' => ['nullable', 'array'],
            'favorite_app_slugs.*' => ['string', 'max:120'],

            'public_profile_enabled' => ['nullable'],
            'show_points' => ['nullable'],
            'show_role' => ['nullable'],
            'show_favorite_apps' => ['nullable'],
        ]);

        $profile = $this->profileArray($user->role_profile ?? null);
        $catalog = $this->customizationCatalog();

        $selected = [
            'profile_theme' => $data['profile_theme'],
            'profile_frame' => $data['profile_frame'],
            'profile_badge' => $data['profile_badge'],
            'profile_color' => $data['profile_color'],
            'avatar_shape' => $data['avatar_shape'],
        ];

        foreach ($selected as $field => $value) {
            if (!isset($catalog[$field][$value])) {
                throw ValidationException::withMessages([
                    $field => 'That customization is not available yet.',
                ]);
            }
        }

        $unlocked = collect($profile['unlocked_profile_items'] ?? [
            'profile_theme:cosmic',
            'profile_frame:none',
            'profile_badge:learning-spark',
            'profile_color:purple',
            'avatar_shape:rounded',
        ])->unique()->values()->all();

        $newUnlocks = [];
        $totalCost = 0;

        foreach ($selected as $field => $value) {
            $unlockKey = "{$field}:{$value}";
            $cost = (int) ($catalog[$field][$value]['cost'] ?? 0);

            if ($cost > 0 && !in_array($unlockKey, $unlocked, true)) {
                $newUnlocks[] = $unlockKey;
                $totalCost += $cost;
            }
        }

        if ($totalCost > (int) ($user->cosmic_points ?? 0)) {
            throw ValidationException::withMessages([
                'profile_theme' => "You need {$totalCost} coins to unlock those customizations. Earn more points first.",
            ]);
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            if (Schema::hasColumn('users', 'profile_photo_path')) {
                $oldPath = $user->profile_photo_path ?? null;

                if ($oldPath && !str_starts_with($oldPath, 'http')) {
                    Storage::disk('public')->delete($oldPath);
                }

                $user->profile_photo_path = $path;
            }
        }

        if ($totalCost > 0) {
            $user->cosmic_points = max(0, (int) ($user->cosmic_points ?? 0) - $totalCost);
            $this->recordPointSpend($user->id, $totalCost, 'Profile customization unlock');
        }

        $profile = array_merge($profile, [
            'headline' => $data['headline'] ?? null,
            'bio' => $data['bio'] ?? null,
            'favorite_subjects' => $data['favorite_subjects'] ?? null,
            'learning_goal' => $data['learning_goal'] ?? null,
            'current_focus' => $data['current_focus'] ?? null,
            'profile_mood' => $data['profile_mood'] ?? null,
            'favorite_app_slugs' => array_values($data['favorite_app_slugs'] ?? []),

            'profile_theme' => $data['profile_theme'],
            'profile_frame' => $data['profile_frame'],
            'profile_badge' => $data['profile_badge'],
            'profile_color' => $data['profile_color'],
            'avatar_shape' => $data['avatar_shape'],

            'unlocked_profile_items' => collect($unlocked)->merge($newUnlocks)->unique()->values()->all(),

            'public_profile_enabled' => $request->boolean('public_profile_enabled'),
            'show_points' => $request->boolean('show_points'),
            'show_role' => $request->boolean('show_role'),
            'show_favorite_apps' => $request->boolean('show_favorite_apps'),
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

        return back()->with('status', $totalCost > 0
            ? "Profile updated and {$totalCost} coins spent on new customizations."
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
            'customizations' => $this->customizationCatalog(),
        ]);
    }

    private function apps(): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('studybuddy_mini_app_platforms')) return collect();

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

        if ($slugs->isEmpty()) return $this->apps()->take(4);

        return $this->apps()
            ->filter(fn ($app) => $slugs->contains($app->slug ?? null))
            ->values();
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

    private function recordPointSpend(int $userId, int $amount, string $label): void
    {
        if (!Schema::hasTable('studybuddy_point_transactions')) return;

        try {
            $payload = [];

            foreach ([
                'user_id' => $userId,
                'points' => -abs($amount),
                'label' => $label,
                'reason' => $label,
                'type' => 'spend',
                'source' => 'profile_customization',
                'meta' => json_encode(['cost' => $amount]),
                'created_at' => now(),
                'updated_at' => now(),
            ] as $column => $value) {
                if (Schema::hasColumn('studybuddy_point_transactions', $column)) {
                    $payload[$column] = $value;
                }
            }

            if ($payload) {
                DB::table('studybuddy_point_transactions')->insert($payload);
            }
        } catch (\Throwable $e) {
            // Points were already deducted from the user. Do not break profile save for optional activity logging.
        }
    }
}
