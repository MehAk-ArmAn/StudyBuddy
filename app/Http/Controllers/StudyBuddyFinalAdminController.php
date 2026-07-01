<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyLaunchChecklistItem;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\StudyBuddyPlatformSetting;
use App\Models\StudyBuddyPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StudyBuddyFinalAdminController extends Controller
{
    public function index()
    {
        return view('admin.studybuddy.final-platform.index', [
            'settings' => StudyBuddyPlatformSetting::query()->orderBy('group_name')->orderBy('sort_order')->get(),
            'apps' => StudyBuddyMiniAppPlatform::query()->ordered()->get(),
            'checks' => StudyBuddyLaunchChecklistItem::query()->orderBy('sort_order')->get(),
            'recentPoints' => StudyBuddyPointTransaction::query()->latest()->take(12)->get(),
            'roleOptions' => [
                'student' => 'Student',
                'parent' => 'Parent',
                'teacher' => 'Teacher',
                'independent_learner' => 'Independent Learner',
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'settings' => ['array'],
            'settings.*' => ['nullable', 'string', 'max:5000'],
        ]);

        foreach (($data['settings'] ?? []) as $key => $value) {
            StudyBuddyPlatformSetting::query()->where('setting_key', $key)->update(['setting_value' => $value]);
        }

        return back()->with('status', 'Platform settings saved.');
    }

    public function updateApp(Request $request, StudyBuddyMiniAppPlatform $app)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:160', Rule::unique('studybuddy_mini_app_platforms', 'slug')->ignore($app->id)],
            'category' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:240'],
            'description' => ['nullable', 'string', 'max:4000'],
            'long_description' => ['nullable', 'string', 'max:9000'],
            'hero_heading' => ['nullable', 'string', 'max:220'],
            'preview_text' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['concept', 'planned', 'beta', 'live', 'paused'])],
            'icon' => ['nullable', 'string', 'max:24'],
            'image_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)[^\s]*$/i'],
            'accent' => ['nullable', 'string', 'max:80'],
            'age_range' => ['nullable', 'string', 'max:60'],
            'role_scope' => ['nullable', 'array'],
            'role_scope.*' => [Rule::in(['student', 'parent', 'teacher', 'independent_learner'])],
            'learning_tags_text' => ['nullable', 'string', 'max:2000'],
            'learning_outcomes_text' => ['nullable', 'string', 'max:5000'],
            'how_it_works_text' => ['nullable', 'string', 'max:5000'],
            'screenshot_urls_text' => ['nullable', 'string', 'max:5000'],
            'safety_note' => ['nullable', 'string', 'max:5000'],
            'locked_preview_note' => ['nullable', 'string', 'max:5000'],
            'platform_notes' => ['nullable', 'string', 'max:5000'],
            'detail_cta_label' => ['nullable', 'string', 'max:120'],
            'web_play_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)[^\s]*$/i'],
            'ios_url' => ['nullable', 'string', 'max:500', 'regex:/^https?:\/\/[^\s]+$/i'],
            'android_url' => ['nullable', 'string', 'max:500', 'regex:/^https?:\/\/[^\s]+$/i'],
            'windows_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)[^\s]*$/i'],
            'mac_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)[^\s]*$/i'],
            'support_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/)[^\s]*$/i'],
            'points_reward' => ['required', 'integer', 'min:0', 'max:500'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_web_enabled' => ['nullable', 'boolean'],
            'is_download_enabled' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        $data['learning_tags'] = $this->lines($request->input('learning_tags_text'));
        $data['learning_outcomes'] = $this->lines($request->input('learning_outcomes_text'));
        $data['how_it_works'] = $this->lines($request->input('how_it_works_text'));
        $data['screenshot_urls'] = $this->lines($request->input('screenshot_urls_text'));

        unset($data['learning_tags_text'], $data['learning_outcomes_text'], $data['how_it_works_text'], $data['screenshot_urls_text']);

        foreach (['is_web_enabled', 'is_download_enabled', 'is_featured', 'is_active'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        $data['role_scope'] = array_values($data['role_scope'] ?? []);

        $app->update($data);

        return back()->with('status', "{$app->fresh()->name} updated everywhere.");
    }

    public function updateChecklist(Request $request, StudyBuddyLaunchChecklistItem $item)
    {
        $item->update($request->validate([
            'status' => ['required', Rule::in(['todo', 'doing', 'done', 'blocked'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'description' => ['nullable', 'string', 'max:4000'],
            'owner_label' => ['nullable', 'string', 'max:160'],
        ]));

        return back()->with('status', 'Launch checklist updated.');
    }

    public function quickAward(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:180'],
            'points' => ['required', 'integer', 'min:-500', 'max:500'],
        ]);

        StudyBuddyPointTransaction::create([
            'user_id' => $data['user_id'],
            'source_type' => 'admin_adjustment',
            'source_slug' => 'admin-final-cockpit',
            'title' => $data['title'],
            'points' => $data['points'],
            'status' => $data['points'] >= 0 ? 'earned' : 'adjusted',
            'meta' => ['admin_id' => $request->user()?->id],
        ]);

        return back()->with('status', 'Points adjustment saved.');
    }

    private function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
