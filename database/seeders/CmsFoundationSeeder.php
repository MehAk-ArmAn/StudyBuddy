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
use App\Models\MiniApp;
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
            ['key' => 'footer.copyright', 'value' => '', 'type' => 'text'],
            ['key' => 'footer.google_play_label', 'value' => '', 'type' => 'text'],
            ['key' => 'footer.google_play_url', 'value' => '', 'type' => 'url'],
            ['key' => 'footer.app_store_label', 'value' => '', 'type' => 'text'],
            ['key' => 'footer.app_store_url', 'value' => '', 'type' => 'url'],
            ['key' => 'navbar.cta.label', 'value' => '', 'type' => 'text'],
            ['key' => 'navbar.cta.route', 'value' => '', 'type' => 'text'],
            ['key' => 'navbar.cta.url', 'value' => '', 'type' => 'url'],
            ['key' => 'site.tagline', 'value' => '', 'type' => 'text'],
            ['key' => 'site.favicon_path', 'value' => '', 'type' => 'image'],
            ['key' => 'site.meta_title', 'value' => '', 'type' => 'text'],
            ['key' => 'site.meta_description', 'value' => '', 'type' => 'text'],
            ['key' => 'site.contact_email', 'value' => '', 'type' => 'email'],
            ['key' => 'site.social_links', 'value' => '', 'type' => 'json'],
            ['key' => 'admin.create_label', 'value' => 'Create', 'type' => 'text'],
            ['key' => 'admin.search_label', 'value' => 'Search', 'type' => 'text'],
            ['key' => 'admin.actions_label', 'value' => 'Actions', 'type' => 'text'],
            ['key' => 'admin.edit_label', 'value' => 'Edit', 'type' => 'text'],
            ['key' => 'admin.delete_label', 'value' => 'Delete', 'type' => 'text'],
            ['key' => 'admin.save_label', 'value' => 'Save', 'type' => 'text'],
            ['key' => 'admin.manage_label', 'value' => 'Manage', 'type' => 'text'],
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
            ['key' => 'contact', 'path' => '/contact'],
            ['key' => 'about', 'path' => '/about'],
        ] as $legal) {
            LegalPage::query()->updateOrCreate(['key' => $legal['key']], $legal + ['slug' => $legal['key'], 'title' => '', 'body' => '', 'meta_title' => '', 'meta_description' => '', 'is_published' => true]);
        }



        foreach (['math-quest', 'spelling-sprint', 'reading-garden', 'focus-forest', 'planner-city', 'quiz-galaxy', 'shapes-lab', 'flashcard-castle'] as $index => $slug) {
            MiniApp::query()->updateOrCreate(
                ['slug' => $slug],
                ['title' => '', 'short_description' => '', 'description' => '', 'subject' => '', 'age_band' => '', 'category' => '', 'grade_level' => '', 'status' => 'concept', 'sort_order' => $index, 'is_enabled' => true]
            );
        }

        foreach (['student', 'parent', 'teacher'] as $role) {
            DashboardPage::query()->updateOrCreate(['key' => $role.'.dashboard'], ['role' => $role, 'title' => '']);
        }
    }
}
