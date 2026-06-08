<?php

namespace App\Support;

use App\Models\AssetReference;
use App\Models\Badge;
use App\Models\CmsPage;
use App\Models\FooterSection;
use App\Models\MiniApp;
use App\Models\NavigationItem;
use App\Models\Reward;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class Cms
{
    public static function setting(string $key, ?string $fallback = null): ?string
    {
        if (! self::tableReady('site_settings')) {
            return $fallback;
        }

        return Cache::remember("cms.setting.$key", 60, fn () => SiteSetting::query()->where('key', $key)->value('value') ?? $fallback);
    }

    public static function text(string $page, string $section, string $key, string $fallback = ''): string
    {
        if (! self::tableReady('pages')) {
            return $fallback;
        }

        return Cache::remember("cms.text.$page.$section.$key", 60, function () use ($page, $section, $key, $fallback) {
            $value = CmsPage::query()
                ->where('key', $page)
                ->join('page_sections', 'pages.id', '=', 'page_sections.page_id')
                ->join('content_blocks', 'page_sections.id', '=', 'content_blocks.page_section_id')
                ->where('page_sections.key', $section)
                ->where('content_blocks.key', $key)
                ->where('content_blocks.is_enabled', true)
                ->value('content_blocks.value');

            return filled($value) ? $value : $fallback;
        });
    }

    public static function navigation(): Collection
    {
        $fallback = collect([
            ['label' => 'Home', 'url' => route('home'), 'route_name' => 'home', 'is_cta' => false],
            ['label' => 'Apps', 'url' => route('apps.index'), 'route_name' => 'apps.index', 'is_cta' => false],
            ['label' => 'Math Quest', 'url' => route('apps.math-quest'), 'route_name' => 'apps.math-quest', 'is_cta' => false],
            ['label' => 'Rewards', 'url' => route('rewards'), 'route_name' => 'rewards', 'is_cta' => false],
            ['label' => 'Dashboards', 'url' => route('demo.primary'), 'route_name' => 'demo.primary', 'is_cta' => false],
            ['label' => 'Showcase', 'url' => route('showcase'), 'route_name' => 'showcase', 'is_cta' => false],
            ['label' => 'Start Learning', 'url' => route('apps.math-quest.play'), 'route_name' => 'apps.math-quest.play', 'is_cta' => true],
        ]);

        if (! self::tableReady('navigation_items')) {
            return $fallback;
        }

        $items = NavigationItem::query()->where('is_enabled', true)->orderBy('sort_order')->get()->map(function (NavigationItem $item) {
            return [
                'label' => $item->label,
                'url' => $item->route_name && Route::has($item->route_name) ? route($item->route_name) : ($item->url ?: '#'),
                'route_name' => $item->route_name,
                'is_cta' => $item->is_cta,
            ];
        });

        return $items->isNotEmpty() ? $items : $fallback;
    }

    public static function footerSections(): Collection
    {
        if (! self::tableReady('footer_sections')) {
            return collect();
        }

        return FooterSection::query()->with(['links' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')])->where('is_enabled', true)->orderBy('sort_order')->get();
    }

    public static function apps(): Collection
    {
        if (! self::tableReady('mini_apps')) {
            return DemoContent::miniApps();
        }

        $apps = MiniApp::query()->orderBy('sort_order')->get();
        return $apps->isNotEmpty() ? $apps : DemoContent::miniApps();
    }

    public static function rewards(): Collection
    {
        if (! self::tableReady('rewards')) {
            return DemoContent::rewards();
        }

        $rewards = Reward::query()->where('is_active', true)->orderBy('sort_order')->get();
        return $rewards->isNotEmpty() ? $rewards : DemoContent::rewards();
    }

    public static function badges(): Collection
    {
        if (! self::tableReady('badges')) {
            return collect();
        }

        return Badge::query()->where('is_active', true)->orderBy('sort_order')->get();
    }

    public static function assetExists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return file_exists(public_path($path));
    }

    public static function missingAssetCount(): int
    {
        if (! self::tableReady('asset_references')) {
            return 0;
        }

        return AssetReference::query()->get()->filter(fn ($asset) => ! self::assetExists($asset->path))->count();
    }

    private static function tableReady(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable) {
            return false;
        }
    }
}
