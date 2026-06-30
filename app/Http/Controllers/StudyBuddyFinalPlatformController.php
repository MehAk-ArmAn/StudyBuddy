<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudyBuddyFinalPlatformController extends Controller
{
    public function appLaunchpad()
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $apps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
        $featured = $apps->where('is_featured', true)->take(3);
        $categories = $apps->pluck('category')->unique()->values();

        return view('studybuddy.final.app-launchpad', compact('settings', 'apps', 'featured', 'categories'));
    }

    public function webPlay(string $slug)
    {
        $app = StudyBuddyMiniAppPlatform::query()->where('slug', $slug)->firstOrFail();
        $settings = StudyBuddyPlatformSetting::publicMap();

        return view('studybuddy.final.web-play', compact('app', 'settings'));
    }

    public function pointsWallet()
    {
        $user = Auth::user();
        $transactions = StudyBuddyPointTransaction::query()
            ->where('user_id', $user?->id)
            ->latest()
            ->take(30)
            ->get();
        $total = (int) $transactions->sum('points');
        $earned = (int) $transactions->where('points', '>', 0)->sum('points');
        $spent = abs((int) $transactions->where('points', '<', 0)->sum('points'));
        $settings = StudyBuddyPlatformSetting::publicMap();

        return view('studybuddy.final.points-wallet', compact('transactions', 'total', 'earned', 'spent', 'settings'));
    }

    public function platformRoadmap()
    {
        $settings = StudyBuddyPlatformSetting::publicMap();
        $apps = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get();
        $checks = StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get();

        return view('studybuddy.final.platform-roadmap', compact('settings', 'apps', 'checks'));
    }

    public function launchReadiness()
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
            'title' => ['nullable', 'string', 'max:180'],
            'points' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $app = StudyBuddyMiniAppPlatform::query()->where('slug', $data['app_slug'])->first();
        $points = (int) ($data['points'] ?? $app?->points_reward ?? 10);
        $title = $data['title'] ?? (($app?->name ?? 'StudyBuddy') . ' session completed');

        $transaction = StudyBuddyPointTransaction::create([
            'user_id' => $request->user()->id,
            'source_type' => 'mini_app_session',
            'source_slug' => $data['app_slug'],
            'title' => $title,
            'points' => $points,
            'status' => 'earned',
            'meta' => [
                'app_name' => $app?->name,
                'completed_from' => 'studybuddy_web_shell',
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
