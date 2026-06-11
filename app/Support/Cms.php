<?php

namespace App\Support;

use App\Models\CmsFooterColumn;
use App\Models\CmsMenu;
use App\Models\CmsButton;
use App\Models\CmsPage;
use App\Models\CmsBlock;
use App\Models\CmsCard;
use App\Models\CmsSection;
use App\Models\CmsStat;
use App\Models\LegalPage;
use App\Models\MiniApp;
use App\Models\MiniAppFeature;
use App\Models\RewardItem;
use App\Models\SiteSetting;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Cms
{
    public static function setting(string $key, string $default = ''): string
    {
        $setting = self::safeFirst('site_settings', fn () => SiteSetting::query()->where('key', $key)->where('is_public', true)->first());

        return (string) ($setting?->value ?? $default);
    }

    public static function page(string $key): ?CmsPage
    {
        return self::safeFirst('cms_pages', fn () => CmsPage::query()->where('key', $key)->where('is_enabled', true)->first());
    }

    public static function legal(string $key): ?LegalPage
    {
        return self::safeFirst('legal_pages', fn () => LegalPage::query()->where('key', $key)->where('is_enabled', true)->where('is_published', true)->first());
    }

    public static function sections(?CmsPage $page): Collection
    {
        if (! $page) {
            return collect();
        }

        return self::safeCollection('cms_sections', fn () => CmsSection::query()
            ->where('cms_page_id', $page->id)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function blocks(?int $sectionId = null): Collection
    {
        return self::safeCollection('cms_blocks', fn () => CmsBlock::query()
            ->when($sectionId, fn ($query) => $query->where('cms_section_id', $sectionId))
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function cards(?int $sectionId = null): Collection
    {
        return self::safeCollection('cms_cards', fn () => CmsCard::query()
            ->when($sectionId, fn ($query) => $query->where('cms_section_id', $sectionId))
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function buttons(?int $sectionId = null): Collection
    {
        return self::safeCollection('cms_buttons', fn () => CmsButton::query()
            ->when($sectionId, fn ($query) => $query->where('cms_section_id', $sectionId))
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function stats(?CmsPage $page): Collection
    {
        if (! $page) {
            return collect();
        }

        return self::safeCollection('cms_stats', fn () => CmsStat::query()
            ->where('cms_page_id', $page->id)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function menu(string $key): Collection
    {
        return self::safeCollection('cms_menus', function () use ($key) {
            $menu = CmsMenu::query()->where('key', $key)->where('is_enabled', true)->first();

            if (! $menu || ! Schema::hasTable('cms_menu_items')) {
                return collect();
            }

            return $menu->items()->where('is_enabled', true)->get();
        });
    }

    public static function footerColumns(): Collection
    {
        return self::safeCollection('cms_footer_columns', fn () => CmsFooterColumn::query()
            ->where('is_enabled', true)
            ->with(['links' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get());
    }

    public static function miniApps(): Collection
    {
        return self::safeCollection('mini_apps', fn () => MiniApp::query()->where('is_enabled', true)->orderBy('sort_order')->orderBy('title')->get());
    }

    public static function miniApp(string $slug): ?MiniApp
    {
        return self::safeFirst('mini_apps', fn () => MiniApp::query()->where('slug', $slug)->where('is_enabled', true)->first());
    }

    public static function appFeatures(?MiniApp $app): Collection
    {
        if (! $app) {
            return collect();
        }

        return self::safeCollection('mini_app_features', fn () => MiniAppFeature::query()
            ->where('mini_app_id', $app->id)
            ->where('is_enabled', true)
            ->orderBy('sort_order')
            ->get());
    }

    public static function rewards(): Collection
    {
        return self::safeCollection('reward_items', fn () => RewardItem::query()->where('is_enabled', true)->orderBy('coins_required')->orderBy('name')->get());
    }

    private static function safeFirst(string $table, callable $query): mixed
    {
        try {
            return Schema::hasTable($table) ? $query() : null;
        } catch (QueryException|Throwable) {
            return null;
        }
    }

    private static function safeCollection(string $table, callable $query): Collection
    {
        try {
            return Schema::hasTable($table) ? $query() : collect();
        } catch (QueryException|Throwable) {
            return collect();
        }
    }
}
