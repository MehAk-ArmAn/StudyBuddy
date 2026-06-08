<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AssetReference;
use App\Models\Badge;
use App\Models\CmsPage;
use App\Models\ContentBlock;
use App\Models\DashboardWidget;
use App\Models\FooterLink;
use App\Models\FooterSection;
use App\Models\MiniApp;
use App\Models\MobilePreviewItem;
use App\Models\NavigationItem;
use App\Models\Reward;
use App\Models\ShowcasePanel;
use App\Models\SiteSetting;
use App\Support\Cms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'Pages' => CmsPage::query()->count(),
                'Content Blocks' => ContentBlock::query()->count(),
                'Apps' => MiniApp::query()->count(),
                'Rewards' => Reward::query()->count(),
                'Missing Assets' => Cms::missingAssetCount(),
            ],
        ]);
    }

    public function pages(): View
    {
        return view('admin.pages.index', ['pages' => CmsPage::query()->with('sections.blocks')->orderBy('title')->get()]);
    }

    public function editPage(CmsPage $page): View
    {
        return view('admin.pages.edit', ['page' => $page->load('sections.blocks')]);
    }

    public function updatePage(Request $request, CmsPage $page): RedirectResponse
    {
        $data = $request->validate(['blocks' => ['array'], 'blocks.*' => ['nullable', 'string', 'max:5000']]);

        foreach ($data['blocks'] ?? [] as $blockId => $value) {
            ContentBlock::query()->whereKey($blockId)->update(['value' => $value]);
        }

        return back()->with('status', 'Page content saved.');
    }

    public function navigation(): View
    {
        return view('admin.navigation.index', ['items' => NavigationItem::query()->orderBy('sort_order')->get()]);
    }

    public function saveNavigation(Request $request): RedirectResponse
    {
        $data = $request->validate(['items' => ['array'], 'items.*.label' => ['required', 'string', 'max:120'], 'items.*.url' => ['nullable', 'string', 'max:255'], 'items.*.route_name' => ['nullable', 'string', 'max:120'], 'items.*.sort_order' => ['nullable', 'integer', 'min:0'], 'items.*.is_enabled' => ['nullable']]);
        foreach ($data['items'] ?? [] as $id => $item) {
            NavigationItem::query()->whereKey($id)->update($item + ['is_enabled' => array_key_exists('is_enabled', $item)]);
        }
        return back()->with('status', 'Navigation saved.');
    }

    public function footer(): View
    {
        return view('admin.footer.index', ['sections' => FooterSection::query()->with('links')->orderBy('sort_order')->get()]);
    }

    public function saveFooter(Request $request): RedirectResponse
    {
        $data = $request->validate(['links' => ['array'], 'links.*.label' => ['required', 'string', 'max:120'], 'links.*.url' => ['nullable', 'string', 'max:255'], 'links.*.route_name' => ['nullable', 'string', 'max:120'], 'links.*.sort_order' => ['nullable', 'integer', 'min:0'], 'settings' => ['array'], 'settings.*' => ['nullable', 'string', 'max:1000']]);
        foreach ($data['links'] ?? [] as $id => $link) {
            FooterLink::query()->whereKey($id)->update($link);
        }
        foreach ($data['settings'] ?? [] as $key => $value) {
            SiteSetting::query()->updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'footer']);
        }
        return back()->with('status', 'Footer saved.');
    }

    public function apps(): View { return view('admin.apps.index', ['apps' => MiniApp::query()->orderBy('sort_order')->get()]); }
    public function saveApps(Request $request): RedirectResponse
    {
        $data = $request->validate(['apps' => ['array'], 'apps.*.title' => ['required', 'string', 'max:160'], 'apps.*.description' => ['nullable', 'string', 'max:1000'], 'apps.*.image_path' => ['nullable', 'string', 'max:255'], 'apps.*.cta_text' => ['nullable', 'string', 'max:80'], 'apps.*.status' => ['nullable', Rule::in(['live','preview','concept'])], 'apps.*.sort_order' => ['nullable', 'integer', 'min:0']]);
        foreach ($data['apps'] ?? [] as $id => $app) { MiniApp::query()->whereKey($id)->update($app); }
        return back()->with('status', 'Apps saved.');
    }

    public function rewards(): View { return view('admin.rewards.index', ['rewards' => Reward::query()->orderBy('sort_order')->get()]); }
    public function saveRewards(Request $request): RedirectResponse
    {
        $data = $request->validate(['rewards' => ['array'], 'rewards.*.name' => ['required', 'string', 'max:160'], 'rewards.*.category' => ['nullable', 'string', 'max:100'], 'rewards.*.points_required' => ['nullable', 'integer', 'min:0'], 'rewards.*.image_path' => ['nullable', 'string', 'max:255'], 'rewards.*.rarity' => ['nullable', 'string', 'max:60'], 'rewards.*.is_active' => ['nullable']]);
        foreach ($data['rewards'] ?? [] as $id => $reward) { Reward::query()->whereKey($id)->update($reward + ['is_active' => array_key_exists('is_active', $reward)]); }
        return back()->with('status', 'Rewards saved.');
    }

    public function badges(): View { return view('admin.badges.index', ['badges' => Badge::query()->orderBy('sort_order')->get()]); }
    public function saveBadges(Request $request): RedirectResponse
    {
        $data = $request->validate(['badges' => ['array'], 'badges.*.name' => ['required', 'string', 'max:160'], 'badges.*.description' => ['nullable', 'string', 'max:1000'], 'badges.*.image_path' => ['nullable', 'string', 'max:255'], 'badges.*.requirement_text' => ['nullable', 'string', 'max:255'], 'badges.*.is_active' => ['nullable']]);
        foreach ($data['badges'] ?? [] as $id => $badge) { Badge::query()->whereKey($id)->update($badge + ['is_active' => array_key_exists('is_active', $badge)]); }
        return back()->with('status', 'Badges saved.');
    }

    public function dashboards(): View { return view('admin.dashboards.index', ['widgets' => DashboardWidget::query()->orderBy('audience')->orderBy('sort_order')->get()->groupBy('audience')]); }
    public function saveDashboards(Request $request): RedirectResponse
    {
        $data = $request->validate(['widgets' => ['array'], 'widgets.*.title' => ['required', 'string', 'max:160'], 'widgets.*.label' => ['nullable', 'string', 'max:160'], 'widgets.*.description' => ['nullable', 'string', 'max:1000'], 'widgets.*.value' => ['nullable', 'string', 'max:160']]);
        foreach ($data['widgets'] ?? [] as $id => $widget) { DashboardWidget::query()->whereKey($id)->update($widget); }
        return back()->with('status', 'Dashboard content saved.');
    }

    public function showcase(): View { return view('admin.showcase.index', ['panels' => ShowcasePanel::query()->orderBy('sort_order')->get()]); }
    public function saveShowcase(Request $request): RedirectResponse
    {
        $data = $request->validate(['panels' => ['array'], 'panels.*.title' => ['required', 'string', 'max:160'], 'panels.*.description' => ['nullable', 'string', 'max:1000']]);
        foreach ($data['panels'] ?? [] as $id => $panel) { ShowcasePanel::query()->whereKey($id)->update($panel); }
        return back()->with('status', 'Showcase saved.');
    }

    public function mobilePreview(): View { return view('admin.mobile-preview.index', ['items' => MobilePreviewItem::query()->orderBy('group')->orderBy('sort_order')->get()->groupBy('group')]); }
    public function saveMobilePreview(Request $request): RedirectResponse
    {
        $data = $request->validate(['items' => ['array'], 'items.*.title' => ['required', 'string', 'max:160'], 'items.*.description' => ['nullable', 'string', 'max:1000']]);
        foreach ($data['items'] ?? [] as $id => $item) { MobilePreviewItem::query()->whereKey($id)->update($item); }
        return back()->with('status', 'Mobile preview saved.');
    }

    public function assets(): View { return view('admin.assets.index', ['assets' => AssetReference::query()->orderBy('name')->get()]); }

    public function settings(): View { return view('admin.settings.index', ['settings' => SiteSetting::query()->orderBy('group')->orderBy('key')->get()]); }
    public function saveSettings(Request $request): RedirectResponse
    {
        $data = $request->validate(['settings' => ['array'], 'settings.*' => ['nullable', 'string', 'max:2000']]);
        foreach ($data['settings'] ?? [] as $key => $value) { SiteSetting::query()->where('key', $key)->update(['value' => $value]); }
        return back()->with('status', 'Settings saved.');
    }

    public function users(): View { return view('admin.users.index', ['users' => AdminUser::query()->orderBy('name')->get()]); }
    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['required', 'email', 'unique:admin_users,email'], 'password' => ['required', 'string', 'min:10']]);
        AdminUser::query()->create($data + ['is_active' => true]);
        return back()->with('status', 'Admin user created.');
    }
    public function updateUser(Request $request, AdminUser $user): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:160'], 'email' => ['required', 'email', Rule::unique('admin_users', 'email')->ignore($user->id)], 'password' => ['nullable', 'string', 'min:10'], 'is_active' => ['nullable']]);
        $active = array_key_exists('is_active', $data);
        if (! $active && $user->is_active && AdminUser::query()->where('is_active', true)->whereKeyNot($user->id)->count() === 0) {
            return back()->withErrors(['is_active' => 'You cannot disable the last active admin.']);
        }
        $user->fill(['name' => $data['name'], 'email' => $data['email'], 'is_active' => $active]);
        if (filled($data['password'] ?? null)) { $user->password = Hash::make($data['password']); }
        $user->save();
        return back()->with('status', 'Admin user updated.');
    }
}
