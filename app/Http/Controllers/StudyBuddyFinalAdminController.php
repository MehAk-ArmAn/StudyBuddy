<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyBuddyFinalAdminController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        $settings = StudyBuddyPlatformSetting::query()->orderBy('group_name')->orderBy('sort_order')->get();
        $apps = StudyBuddyMiniAppPlatform::query()->ordered()->get();
        $checks = StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get();
        $recentPoints = StudyBuddyPointTransaction::query()->latest()->take(12)->get();

        return view('admin.studybuddy.final-platform.index', compact('settings', 'apps', 'checks', 'recentPoints'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'settings' => ['array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach (($data['settings'] ?? []) as $key => $value) {
            StudyBuddyPlatformSetting::query()->where('setting_key', $key)->update(['setting_value' => $value]);
        }

        return back()->with('status', 'Platform settings saved.');
    }

    public function updateApp(Request $request, StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:240'],
            'description' => ['nullable', 'string', 'max:4000'],
            'preview_text' => ['nullable', 'string', 'max:1200'],
            'safety_note' => ['nullable', 'string', 'max:1200'],
            'status' => ['required', 'in:concept,planned,beta,live,paused'],
            'icon' => ['nullable', 'string', 'max:24'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'web_play_url' => ['nullable', 'string', 'max:500'],
            'ios_url' => ['nullable', 'string', 'max:500'],
            'android_url' => ['nullable', 'string', 'max:500'],
            'windows_url' => ['nullable', 'string', 'max:500'],
            'mac_url' => ['nullable', 'string', 'max:500'],
            'points_reward' => ['required', 'integer', 'min:0', 'max:500'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'age_min' => ['nullable', 'integer', 'min:3', 'max:120'],
            'age_max' => ['nullable', 'integer', 'min:3', 'max:120'],
            'audience_roles' => ['nullable', 'array'],
            'audience_roles.*' => ['string', 'in:student,parent,teacher,independent_learner'],
            'is_web_enabled' => ['nullable', 'boolean'],
            'is_download_enabled' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_web_enabled'] = $request->boolean('is_web_enabled');
        $data['is_download_enabled'] = $request->boolean('is_download_enabled');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');
        $data['audience_roles'] = $data['audience_roles'] ?? ['student', 'parent', 'teacher', 'independent_learner'];

        $app->update($data);

        return back()->with('status', "{$app->name} updated.");
    }

    public function updateChecklist(Request $request, StudyBuddyLaunchChecklistItem $item): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'status' => ['required', 'in:todo,doing,done,blocked'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'description' => ['nullable', 'string', 'max:4000'],
            'owner_label' => ['nullable', 'string', 'max:160'],
        ]);

        $item->update($data);
        return back()->with('status', 'Launch checklist updated.');
    }

    public function quickAward(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:180'],
            'points' => ['required', 'integer', 'min:-9999', 'max:9999'],
        ]);

        StudyBuddyPointTransaction::create([
            'user_id' => $data['user_id'],
            'source_type' => 'admin_adjustment',
            'source_slug' => 'admin-final-cockpit',
            'title' => $data['title'],
            'points' => $data['points'],
            'status' => $data['points'] >= 0 ? 'earned' : 'adjusted',
        ]);

        return back()->with('status', 'Points adjustment saved.');
    }

    private function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (! $user || ! (bool) $user->is_admin) {
            abort(403, 'Only StudyBuddy admins can access this area.');
        }
    }
}
