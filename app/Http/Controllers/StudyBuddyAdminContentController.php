<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyAppCatalogItem;
use App\Models\StudyBuddyContentItem;
use App\Models\StudyBuddyContentPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Throwable;

class StudyBuddyAdminContentController extends Controller
{
    public function index(): View
    {
        $this->authorizeStudio();

        $pages = StudyBuddyContentPage::orderBy('sort_order')->orderBy('title')->get();
        $items = StudyBuddyContentItem::orderBy('page_slug')->orderBy('sort_order')->get();
        $apps = StudyBuddyAppCatalogItem::orderBy('sort_order')->orderBy('title')->get();

        return view('admin.studybuddy.content-studio.index', compact('pages', 'items', 'apps'));
    }

    public function updatePage(Request $request, StudyBuddyContentPage $page): RedirectResponse
    {
        $this->authorizeStudio();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'hero_badge' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'string', 'max:255'],
            'primary_cta_label' => ['nullable', 'string', 'max:255'],
            'primary_cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta_label' => ['nullable', 'string', 'max:255'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'content_blocks_json' => ['nullable', 'string'],
            'is_published' => ['nullable'],
        ]);

        $page->fill(collect($data)->except(['content_blocks_json', 'is_published'])->toArray());
        $page->is_published = $request->boolean('is_published');
        $page->content_blocks = $this->decodeJson($request->input('content_blocks_json'), $page->content_blocks ?? []);
        $page->save();

        return back()->with('status', "Updated {$page->title}.");
    }

    public function updateItem(Request $request, StudyBuddyContentItem $item): RedirectResponse
    {
        $this->authorizeStudio();

        $data = $request->validate([
            'page_slug' => ['nullable', 'string', 'max:255'],
            'item_type' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:20'],
            'badge' => ['nullable', 'string', 'max:255'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'button_label' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'extra_json' => ['nullable', 'string'],
            'is_active' => ['nullable'],
        ]);

        $item->fill(collect($data)->except(['extra_json', 'is_active'])->toArray());
        $item->is_active = $request->boolean('is_active');
        $item->extra = $this->decodeJson($request->input('extra_json'), $item->extra ?? []);
        $item->save();

        return back()->with('status', "Updated {$item->title}.");
    }

    public function updateApp(Request $request, StudyBuddyAppCatalogItem $app): RedirectResponse
    {
        $this->authorizeStudio();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:20'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'web_play_url' => ['nullable', 'string', 'max:255'],
            'ios_url' => ['nullable', 'string', 'max:255'],
            'android_url' => ['nullable', 'string', 'max:255'],
            'windows_url' => ['nullable', 'string', 'max:255'],
            'points_reward' => ['nullable', 'integer', 'min:0'],
            'launch_status' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'available_web' => ['nullable'],
            'available_ios' => ['nullable'],
            'available_android' => ['nullable'],
            'available_windows' => ['nullable'],
            'is_active' => ['nullable'],
            'extra_json' => ['nullable', 'string'],
        ]);

        $app->fill(collect($data)->except([
            'available_web', 'available_ios', 'available_android', 'available_windows', 'is_active', 'extra_json'
        ])->toArray());

        $app->available_web = $request->boolean('available_web');
        $app->available_ios = $request->boolean('available_ios');
        $app->available_android = $request->boolean('available_android');
        $app->available_windows = $request->boolean('available_windows');
        $app->is_active = $request->boolean('is_active');
        $app->extra = $this->decodeJson($request->input('extra_json'), $app->extra ?? []);
        $app->save();

        return back()->with('status', "Updated {$app->title}.");
    }

    protected function decodeJson(?string $json, array $fallback): array
    {
        if (! $json) {
            return $fallback;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    protected function authorizeStudio(): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        $isAdmin = (bool) ($user->is_admin ?? false);

        if ($user->id === 1 || $isAdmin) {
            return;
        }

        abort(403, 'Only StudyBuddy admins can edit platform content.');
    }
}
