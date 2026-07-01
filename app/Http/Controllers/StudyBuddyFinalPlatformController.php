<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StudyBuddyFinalPlatformController extends Controller
{
    public function apps(Request $request): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $role = $request->user()?->normalizedRole();
        $apps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
        $featured = $apps->where('is_featured', true)->take(3);
        $categories = $apps->pluck('category')->filter()->unique()->values();
        $roles = ['student' => 'Students', 'parent' => 'Parents', 'teacher' => 'Teachers', 'independent_learner' => 'Independent Learners'];

        return view('studybuddy.final.apps', compact('settings', 'apps', 'featured', 'categories', 'roles', 'role'));
    }

    public function appDetail(Request $request, string $slug): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        $related = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('id', '!=', $app->id)
            ->where('category', $app->category)
            ->ordered()
            ->take(3)
            ->get();

        if ($related->count() < 3) {
            $extra = StudyBuddyMiniAppPlatform::query()->active()->where('id', '!=', $app->id)->ordered()->take(3 - $related->count())->get();
            $related = $related->merge($extra)->unique('id')->take(3);
        }

        return view('studybuddy.final.app-detail', compact('settings', 'app', 'related'));
    }

    public function appLaunchpadRedirect(): RedirectResponse
    {
        return redirect()->route('studybuddy.apps', status: 301);
    }

    public function webPlay(Request $request, string $slug): View
    {
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        $settings = StudyBuddyPlatformSetting::publicMap();
        $canPlay = Auth::check() && $app->is_web_enabled;

        return view('studybuddy.final.web-play', compact('app', 'settings', 'canPlay'));
    }

    public function pointsWallet(Request $request): View
    {
        $user = $request->user();
        $transactions = StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->take(50)
            ->get();

        $total = (int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->sum('points');
        $earned = (int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->where('points', '>', 0)->sum('points');
        $spent = abs((int) StudyBuddyPointTransaction::query()->where('user_id', $user?->id)->where('points', '<', 0)->sum('points'));
        $settings = StudyBuddyPlatformSetting::publicMap();

        return view('studybuddy.final.points-wallet', compact('transactions', 'total', 'earned', 'spent', 'settings'));
    }

    public function platformRoadmap(): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $apps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
        $checks = StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get();

        return view('studybuddy.final.platform-roadmap', compact('settings', 'apps', 'checks'));
    }

    public function launchReadiness(): View
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $checks = StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get();
        $total = max($checks->count(), 1);
        $done = $checks->where('status', 'done')->count();
        $score = (int) round(($done / $total) * 100);

        return view('studybuddy.final.launch-readiness', compact('settings', 'checks', 'score', 'done', 'total'));
    }

    public function completeSession(Request $request)
    {
        $data = $request->validate([
            'app_slug' => ['required', 'string', 'max:120'],
        ]);

        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('slug', $data['app_slug'])
            ->firstOrFail();

        if (! $app->is_web_enabled) {
            throw ValidationException::withMessages(['app_slug' => 'This app is preview-only right now.']);
        }

        $alreadyEarnedToday = StudyBuddyPointTransaction::query()
            ->where('user_id', $request->user()->id)
            ->where('source_type', 'mini_app_session')
            ->where('source_slug', $app->slug)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyEarnedToday) {
            $message = 'Nice focus. You already earned today\'s demo points for this app.';
            return $request->expectsJson()
                ? response()->json(['ok' => false, 'message' => $message], 429)
                : back()->with('status', $message);
        }

        $points = max(0, min((int) $app->points_reward, 500));

        $transaction = StudyBuddyPointTransaction::create([
            'user_id' => $request->user()->id,
            'source_type' => 'mini_app_session',
            'source_slug' => $app->slug,
            'title' => 'Completed '.$app->name.' demo session',
            'points' => $points,
            'status' => 'earned',
            'meta' => [
                'app_name' => $app->name,
                'completed_from' => 'studybuddy_web_shell',
                'session_key' => Str::uuid()->toString(),
            ],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'points' => $points,
                'transaction_id' => $transaction->id,
                'message' => "{$points} points added to your StudyBuddy wallet.",
            ]);
        }

        return back()->with('status', "{$points} points added to your StudyBuddy wallet.");
    }
}
