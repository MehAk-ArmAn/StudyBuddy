<?php

namespace Database\Seeders;

use App\Models\CmsFooterColumn;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsStat;
use App\Models\DashboardPage;
use App\Models\LegalPage;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class CmsFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['key' => 'brand.name', 'value' => '', 'type' => 'text'],
            ['key' => 'brand.logo_path', 'value' => 'assets/studybuddy/logo-icon.png', 'type' => 'image'],
            ['key' => 'footer.text', 'value' => '', 'type' => 'text'],
        ] as $setting) {
            SiteSetting::query()->updateOrCreate(['key' => $setting['key']], $setting);
        }

        $pages = [
            ['key' => 'home', 'path' => '/', 'route_name' => 'home'],
            ['key' => 'apps', 'path' => '/apps', 'route_name' => 'apps.index'],
            ['key' => 'rewards', 'path' => '/rewards', 'route_name' => 'rewards'],
            ['key' => 'about', 'path' => '/about', 'route_name' => 'about'],
            ['key' => 'contact', 'path' => '/contact', 'route_name' => 'contact'],
        ];

        foreach ($pages as $index => $pageData) {
            $page = CmsPage::query()->updateOrCreate(['key' => $pageData['key']], $pageData + ['title' => '', 'sort_order' => $index]);

            CmsSection::query()->updateOrCreate(
                ['key' => $pageData['key'].'.hero'],
                ['cms_page_id' => $page->id, 'type' => 'hero', 'eyebrow' => '', 'title' => '', 'body' => '', 'sort_order' => 0]
            );
        }

        $home = CmsPage::query()->where('key', 'home')->first();
        if ($home) {
            foreach (range(1, 5) as $index) {
                CmsStat::query()->updateOrCreate(
                    ['key' => 'home.stat.'.$index],
                    ['cms_page_id' => $home->id, 'value' => '', 'label' => '', 'helper_text' => '', 'display_type' => $index === 5 ? 'rating' : 'text', 'sort_order' => $index]
                );
            }
        }

        $menu = CmsMenu::query()->updateOrCreate(['key' => 'primary'], ['name' => '', 'is_enabled' => true]);
        foreach ($pages as $index => $pageData) {
            CmsMenuItem::query()->updateOrCreate(
                ['cms_menu_id' => $menu->id, 'sort_order' => $index],
                ['label' => '', 'url' => $pageData['path'], 'route_name' => $pageData['route_name'], 'is_enabled' => true]
            );
        }

        foreach (range(1, 3) as $index) {
            CmsFooterColumn::query()->updateOrCreate(['key' => 'footer.column.'.$index], ['title' => '', 'sort_order' => $index]);
        }

        foreach ([
            ['key' => 'privacy-policy', 'path' => '/privacy-policy'],
            ['key' => 'terms-and-conditions', 'path' => '/terms-and-conditions'],
            ['key' => 'cookie-policy', 'path' => '/cookie-policy'],
            ['key' => 'data-deletion', 'path' => '/data-deletion'],
        ] as $legal) {
            LegalPage::query()->updateOrCreate(['key' => $legal['key']], $legal + ['title' => '', 'body' => '']);
        }

        foreach (['student', 'parent', 'teacher'] as $role) {
            DashboardPage::query()->updateOrCreate(['key' => $role.'.dashboard'], ['role' => $role, 'title' => '']);
        }
    }
}
