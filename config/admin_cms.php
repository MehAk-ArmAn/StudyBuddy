<?php

use App\Models\AdminUser;
use App\Models\AssetReference;
use App\Models\CmsFooterColumn;
use App\Models\CmsFooterLink;
use App\Models\CmsMenuItem;
use App\Models\CmsPage;
use App\Models\CmsSection;
use App\Models\CmsStat;
use App\Models\DashboardWidget;
use App\Models\LegalPage;
use App\Models\MiniApp;
use App\Models\MiniAppFeature;
use App\Models\RewardCategory;
use App\Models\RewardItem;
use App\Models\SiteSetting;
use App\Models\User;

return [
    'resources' => [
        'pages' => [
            'label' => 'Pages', 'model' => CmsPage::class, 'search' => ['key', 'path', 'title'], 'order' => ['sort_order', 'key'],
            'columns' => ['key', 'path', 'title', 'is_enabled'],
            'fields' => [
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'route_name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'path' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'meta_description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'page-sections' => [
            'label' => 'Page Sections', 'model' => CmsSection::class, 'search' => ['key', 'title', 'body'], 'order' => ['sort_order', 'key'],
            'columns' => ['key', 'type', 'title', 'is_enabled'],
            'fields' => [
                'cms_page_id' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'exists:cms_pages,id']],
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'type' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'eyebrow' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'body' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'media_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'homepage-stats' => [
            'label' => 'Homepage Stats', 'model' => CmsStat::class, 'search' => ['key', 'value', 'label'], 'order' => ['sort_order', 'key'],
            'columns' => ['key', 'value', 'label', 'display_type', 'is_enabled'],
            'fields' => [
                'cms_page_id' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'exists:cms_pages,id']],
                'cms_section_id' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'exists:cms_sections,id']],
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'value' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'helper_text' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'display_type' => ['type' => 'select', 'options' => ['text' => 'Text', 'rating' => 'Rating'], 'rules' => ['required', 'in:text,rating']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'navigation' => [
            'label' => 'Navigation', 'model' => CmsMenuItem::class, 'search' => ['label', 'url', 'route_name'], 'order' => ['sort_order', 'label'],
            'columns' => ['label', 'url', 'route_name', 'is_enabled'],
            'fields' => [
                'cms_menu_id' => ['type' => 'number', 'rules' => ['required', 'integer', 'exists:cms_menus,id']],
                'label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'url' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'route_name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'opens_new_tab' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'footer-columns' => [
            'label' => 'Footer Columns', 'model' => CmsFooterColumn::class, 'search' => ['key', 'title'], 'order' => ['sort_order', 'key'],
            'columns' => ['key', 'title', 'is_enabled'],
            'fields' => [
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'footer-links' => [
            'label' => 'Footer Links', 'model' => CmsFooterLink::class, 'search' => ['label', 'url', 'route_name'], 'order' => ['sort_order', 'label'],
            'columns' => ['label', 'url', 'route_name', 'is_enabled'],
            'fields' => [
                'cms_footer_column_id' => ['type' => 'number', 'rules' => ['required', 'integer', 'exists:cms_footer_columns,id']],
                'label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'url' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'route_name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'apps' => [
            'label' => 'Apps', 'model' => MiniApp::class, 'search' => ['title', 'slug', 'short_description'], 'order' => ['sort_order', 'slug'],
            'columns' => ['slug', 'title', 'status', 'is_featured', 'is_enabled'],
            'fields' => [
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'slug' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'short_description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'category' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'age_band' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'grade_level' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'icon_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'image_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'status' => ['type' => 'select', 'options' => ['concept' => 'Concept', 'live' => 'Live', 'preview' => 'Preview'], 'rules' => ['required', 'in:concept,live,preview']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'is_featured' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'start_button_label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'download_button_label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'web_embed_url' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'web_embed_empty_message' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'web_embed_code_placeholder' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'google_play_url' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'app_store_url' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'seo_title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'seo_description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
            ],
        ],
        'app-features' => [
            'label' => 'App Features', 'model' => MiniAppFeature::class, 'search' => ['title', 'body'], 'order' => ['sort_order', 'title'],
            'columns' => ['mini_app_id', 'title', 'is_enabled', 'sort_order'],
            'fields' => [
                'mini_app_id' => ['type' => 'number', 'rules' => ['required', 'integer', 'exists:mini_apps,id']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'body' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'image_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'rewards' => [
            'label' => 'Rewards', 'model' => RewardItem::class, 'search' => ['name', 'slug', 'description'], 'order' => ['sort_order', 'coins_required'],
            'columns' => ['name', 'slug', 'coins_required', 'rarity', 'is_enabled'],
            'fields' => [
                'reward_category_id' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'exists:reward_categories,id']],
                'name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'slug' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'coins_required' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
                'rarity' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'image_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'reward-categories' => [
            'label' => 'Reward Categories', 'model' => RewardCategory::class, 'search' => ['name', 'slug'], 'order' => ['name'],
            'columns' => ['name', 'slug', 'is_enabled'],
            'fields' => [
                'name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'slug' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
            ],
        ],
        'dashboard-content' => [
            'label' => 'Dashboard Content', 'model' => DashboardWidget::class, 'search' => ['key', 'title', 'label', 'description'], 'order' => ['sort_order', 'key'],
            'columns' => ['audience_role', 'key', 'title', 'label', 'is_enabled'],
            'fields' => [
                'dashboard_page_id' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'exists:dashboard_pages,id']],
                'audience_role' => ['type' => 'select', 'options' => ['student' => 'Student', 'parent' => 'Parent', 'teacher' => 'Teacher'], 'rules' => ['required', 'in:student,parent,teacher']],
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'label' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'value' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'icon_path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'type' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
                'sort_order' => ['type' => 'number', 'rules' => ['nullable', 'integer', 'min:0']],
            ],
        ],
        'legal' => [
            'label' => 'Legal Pages', 'model' => LegalPage::class, 'search' => ['key', 'slug', 'title', 'body'], 'order' => ['slug'],
            'columns' => ['key', 'slug', 'title', 'is_published'],
            'fields' => [
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'slug' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'path' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'body' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'meta_title' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'meta_description' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'is_published' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
            ],
        ],
        'assets' => [
            'label' => 'Assets', 'model' => AssetReference::class, 'search' => ['key', 'name', 'path', 'alt_text', 'asset_group'], 'order' => ['asset_group', 'name'],
            'columns' => ['key', 'name', 'asset_type', 'asset_group', 'is_enabled'],
            'fields' => [
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'asset_type' => ['type' => 'select', 'options' => ['image' => 'Image', 'video' => 'Video', 'icon' => 'Icon', 'document' => 'Document'], 'rules' => ['required', 'in:image,video,icon,document']],
                'path' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:2048']],
                'alt_text' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'asset_group' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'used_on' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'is_enabled' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
            ],
        ],
        'settings' => [
            'label' => 'Settings', 'model' => SiteSetting::class, 'search' => ['key', 'value'], 'order' => ['key'],
            'columns' => ['key', 'type', 'value', 'is_public'],
            'fields' => [
                'key' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'value' => ['type' => 'textarea', 'rules' => ['nullable', 'string']],
                'type' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'is_public' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
            ],
        ],
        'users' => [
            'label' => 'Users', 'model' => User::class, 'search' => ['name', 'email', 'role'], 'order' => ['name'],
            'columns' => ['name', 'email', 'role'],
            'fields' => [
                'name' => ['type' => 'text', 'rules' => ['required', 'string', 'max:255']],
                'email' => ['type' => 'text', 'rules' => ['required', 'email', 'max:255']],
                'role' => ['type' => 'select', 'options' => ['student' => 'Student', 'parent' => 'Parent', 'teacher' => 'Teacher'], 'rules' => ['required', 'in:student,parent,teacher']],
            ],
        ],
        'admin-users' => [
            'label' => 'Admin Users', 'model' => AdminUser::class, 'search' => ['name', 'email'], 'order' => ['name'],
            'columns' => ['name', 'email', 'is_active'],
            'fields' => [
                'name' => ['type' => 'text', 'rules' => ['nullable', 'string', 'max:255']],
                'email' => ['type' => 'text', 'rules' => ['required', 'email', 'max:255']],
                'password' => ['type' => 'password', 'rules' => ['required', 'string', 'min:8'], 'store_only' => true],
                'is_active' => ['type' => 'boolean', 'rules' => ['nullable', 'boolean']],
            ],
        ],
    ],
];
