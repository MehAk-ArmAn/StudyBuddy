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

    public function updateApp(
        Request $request,
        StudyBuddyMiniAppPlatform $app
    ): RedirectResponse {
        $this->authorizeAdmin();

        // This historical endpoint remains for old bookmarks/forms, but app
        // records have one safe writer: the canonical Apps editor and its
        // shared validation, artwork and browser-publishing workflow.
        return redirect()
            ->route('admin.control-room.apps.edit', $app)
            ->with('status', 'Open the Apps editor to update '.$app->name.'.');
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
