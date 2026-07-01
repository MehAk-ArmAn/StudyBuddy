<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Throwable;

class StudyBuddyFinalPlatformController extends Controller
{
    public function apps(Request $request)
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $role = $request->query('role', 'all');
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');

        $appsQuery = StudyBuddyMiniAppPlatform::query()->active()->ordered();

        if ($role !== 'all') {
            $appsQuery->forRole($role);
        }

        if ($category !== 'all') {
            $appsQuery->where('category', $category);
        }

        if ($status === 'web') {
            $appsQuery->where('is_web_enabled', true);
        } elseif ($status === 'download') {
            $appsQuery->where('is_download_enabled', true);
        } elseif (in_array($status, ['concept', 'planned', 'beta', 'live', 'paused'], true)) {
            $appsQuery->where('status', $status);
        }

        $apps = $appsQuery->get();
        $allApps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
        $categories = $allApps->pluck('category')->filter()->unique()->values();
        $featured = $allApps->where('is_featured', true)->take(4);

        return view('studybuddy.apps.index', compact('settings', 'apps', 'allApps', 'categories', 'featured', 'role', 'category', 'status'));
    }

    public function appDetail(string $slug)
    {
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        $settings = StudyBuddyPlatformSetting::publicMap();
        $related = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('id', '!=', $app->id)
            ->where(function ($query) use ($app): void {
                $query->where('category', $app->category)
                    ->orWhere('is_featured', true);
            })
            ->ordered()
            ->take(4)
            ->get();

        return view('studybuddy.apps.show', compact('settings', 'app', 'related'));
    }

    public function webPlay(string $slug)
    {
        $app = $this->appsCollection()->firstWhere('slug', $slug);

        abort_if(! $app, 404, 'This StudyBuddy mini-app is not available yet.');
        abort_if(! (bool) ($app->is_web_enabled ?? false), 404, 'Web play is not enabled for this mini-app yet.');

        $settings = StudyBuddyPlatformSetting::publicMap();

        return view('studybuddy.final.web-play', compact('app', 'settings'));
    }

    public function pointsWallet()
    {
        $user = Auth::user();
        $transactions = StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->paginate(20);

        $total = (int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->sum('points');
        $earned = (int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->where('points', '>', 0)->sum('points');
        $spent = abs((int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->where('points', '<', 0)->sum('points'));
        $settings = StudyBuddyPlatformSetting::publicMap();

        return view('studybuddy.final.points-wallet', compact('transactions', 'total', 'earned', 'spent', 'settings'));
    }

    public function platformRoadmap()
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $apps = $this->appsCollection();
        $checks = $this->checks();

        return view('studybuddy.final.platform-roadmap', compact('settings', 'apps', 'checks'));
    }

    public function launchReadiness()
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $checks = $this->checks();
        $total = max($checks->count(), 1);
        $done = $checks->where('status', 'done')->count();
        $score = (int) round(($done / $total) * 100);

        return view('studybuddy.final.launch-readiness', compact('settings', 'checks', 'score', 'done', 'total'));
    }

    public function completeSession(Request $request)
    {
        $data = $request->validate([
            'app_slug' => [
                'required',
                'string',
                'max:120',
                Rule::exists('studybuddy_mini_app_platforms', 'slug')->where('is_active', true),
            ],
        ]);

        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('is_web_enabled', true)
            ->where('slug', $data['app_slug'])
            ->firstOrFail();

        $duplicate = StudyBuddyPointTransaction::query()
            ->where('user_id', $request->user()->id)
            ->where('source_type', 'mini_app_session')
            ->where('source_slug', $app->slug)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->exists();

        if ($duplicate) {
            $message = 'Session already counted recently. Keep learning, then claim again later.';

            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => $message], 429)
                : back()->with('status', $message);
        }

        $points = (int) min(max($app->points_reward, 0), 500);

        $transaction = StudyBuddyPointTransaction::create([
            'user_id' => $request->user()->id,
            'source_type' => 'mini_app_session',
            'source_slug' => $app->slug,
            'title' => $app->name.' session completed',
            'points' => $points,
            'status' => 'earned',
            'meta' => [
                'app_name' => $app->name,
                'completed_from' => 'studybuddy_web_shell',
                'server_controlled_points' => true,
            ],
        ]);

        return $request->expectsJson()
            ? response()->json([
                'ok' => true,
                'points' => $points,
                'transaction_id' => $transaction->id,
                'message' => "{$points} points added to your StudyBuddy wallet.",
            ])
            : back()->with('status', "{$points} points added to your StudyBuddy wallet.");
    }

    protected function appsCollection()
    {
        try {
            if (Schema::hasTable('studybuddy_mini_app_platforms')) {
                $apps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
                if ($apps->isNotEmpty()) {
                    return $apps;
                }
            }
        } catch (Throwable $e) {
            // Fall through to safe preview fallback.
        }

        return collect([
            (object) [
                'slug' => 'focus-forest',
                'name' => 'Focus Forest',
                'category' => 'Focus',
                'tagline' => 'Build focus one session at a time.',
                'description' => 'A safe demo web shell for focus sessions.',
                'long_description' => 'Focus Forest is a safe preview app while the full database-backed catalog loads.',
                'status' => 'beta',
                'icon' => '🌲',
                'accent' => 'emerald',
                'points_reward' => 50,
                'estimated_minutes' => 20,
                'is_web_enabled' => true,
                'is_download_enabled' => false,
                'is_featured' => true,
                'web_play_url' => '/play/focus-forest',
                'ios_url' => null,
                'android_url' => null,
                'windows_url' => null,
                'mac_url' => null,
            ],
        ]);
    }

    protected function checks()
    {
        try {
            if (Schema::hasTable('studybuddy_launch_checklist_items')) {
                return StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get();
            }
        } catch (Throwable $e) {
            // Fall through.
        }

        return collect();
    }
}
