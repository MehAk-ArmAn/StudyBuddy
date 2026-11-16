<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class StudyBuddySearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('search.index', [
            'query' => $query,
            'results' => $this->buildResults($query, 60),
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        return response()->json([
            'query' => $query,
            'results' => $this->buildResults($query, 12),
        ]);
    }

    private function buildResults(string $query, int $limit): array
    {
        $query = Str::lower($query);
        $results = collect();

        $static = collect([
            ['title' => 'Home', 'type' => 'Page', 'url' => '/', 'description' => 'Start from the StudyBuddy homepage.', 'icon' => '🏠'],
            ['title' => 'Apps', 'type' => 'App Universe', 'url' => '/apps', 'description' => 'Explore all StudyBuddy learning worlds.', 'icon' => '🎮'],
            ['title' => 'Community', 'type' => 'Community', 'url' => '/community', 'description' => 'Discover public StudyBuddy profiles.', 'icon' => '🌍'],
            ['title' => 'Dashboard', 'type' => 'Account', 'url' => '/dashboard', 'description' => 'Open your learning dashboard.', 'icon' => '✨'],
            ['title' => 'Profile Studio', 'type' => 'Account', 'url' => '/profile', 'description' => 'Customize your profile, avatar, colors, and showcase.', 'icon' => '🪄'],
            ['title' => 'Points Wallet', 'type' => 'Rewards', 'url' => '/points-wallet', 'description' => 'Track points, rewards, and progress.', 'icon' => '⭐'],
            ['title' => 'For Parents', 'type' => 'Role', 'url' => '/apps?role=parent', 'description' => 'Parent-friendly StudyBuddy tools.', 'icon' => '🛡️'],
            ['title' => 'For Teachers', 'type' => 'Role', 'url' => '/apps?role=teacher', 'description' => 'Teacher-friendly classroom tools.', 'icon' => '🏫'],
        ]);

        $results = $results->merge($static);

        if (Schema::hasTable('studybuddy_mini_app_platforms')) {
            $apps = DB::table('studybuddy_mini_app_platforms')
                ->orderBy(Schema::hasColumn('studybuddy_mini_app_platforms', 'sort_order') ? 'sort_order' : 'id')
                ->limit(100)
                ->get();

            foreach ($apps as $app) {
                $results->push([
                    'title' => $app->name ?? 'StudyBuddy App',
                    'type' => 'Learning App',
                    'url' => '/apps/'.($app->slug ?? ''),
                    'description' => $app->tagline ?? $app->description ?? $app->category ?? 'Open this StudyBuddy learning world.',
                    'icon' => $app->icon ?? '🎮',
                    'image' => $this->assetUrl($app->hero_image ?? $app->image_path ?? null),
                ]);
            }
        }

        if (Schema::hasTable('pages')) {
            $pages = DB::table('pages')->limit(80)->get();

            foreach ($pages as $page) {
                $slug = $page->slug ?? null;
                if (!$slug) continue;

                $results->push([
                    'title' => $page->title ?? Str::headline($slug),
                    'type' => 'Page',
                    'url' => '/'.$slug,
                    'description' => $page->excerpt ?? $page->subtitle ?? 'Open this StudyBuddy page.',
                    'icon' => '📄',
                ]);
            }
        }

        if (Schema::hasTable('users')) {
            $users = User::query()
                ->where('is_admin', false)
                ->limit(80)
                ->get();

            foreach ($users as $user) {
                $profile = $this->profileArray($user->role_profile ?? null);

                if (!($profile['public_profile_enabled'] ?? false)) {
                    continue;
                }

                $results->push([
                    'title' => $user->name,
                    'type' => 'Public Profile',
                    'url' => '/u/'.$user->id,
                    'description' => $profile['headline'] ?? 'StudyBuddy community profile.',
                    'icon' => '👤',
                    'image' => $this->profilePhoto($user),
                ]);
            }
        }

        if ($query !== '') {
            $results = $results->filter(function ($item) use ($query) {
                $haystack = Str::lower(($item['title'] ?? '').' '.($item['type'] ?? '').' '.($item['description'] ?? '').' '.($item['url'] ?? ''));
                return str_contains($haystack, $query);
            });
        }

        return $results
            ->unique(fn ($item) => ($item['type'] ?? '').'|'.($item['title'] ?? '').'|'.($item['url'] ?? ''))
            ->take($limit)
            ->values()
            ->all();
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (preg_match('/^https?:\/\//i', $path)) return $path;

        $clean = ltrim($path, '/');

        return file_exists(public_path($clean)) ? asset($clean) : null;
    }

    private function profilePhoto(User $user): ?string
    {
        $path = $user->profile_photo_path ?? null;

        if (!$path) return null;
        if (preg_match('/^https?:\/\//i', $path)) return $path;

        return asset('storage/'.$path);
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
