<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function payload(string $table, array $data): array {
    $out = [];
    foreach ($data as $key => $value) {
        if (Schema::hasColumn($table, $key)) {
            $out[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $value;
        }
    }
    if (Schema::hasColumn($table, 'updated_at')) $out['updated_at'] = now();
    if (Schema::hasColumn($table, 'created_at')) $out['created_at'] = now();
    return $out;
}

$nav = [
    ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
    ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
    ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
    ['label' => 'Profile', 'url' => '/profile', 'roles' => ['auth']],
    ['label' => 'Dashboard', 'url' => '/dashboard', 'roles' => ['auth']],
    ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
    ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
    ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
];

$footer = [
    'Explore' => [
        ['label' => 'Apps', 'url' => '/apps'],
        ['label' => 'Community', 'url' => '/community'],
        ['label' => 'Search', 'url' => '/search'],
        ['label' => 'Dashboard', 'url' => '/dashboard'],
    ],
    'Profile' => [
        ['label' => 'Profile Studio', 'url' => '/profile'],
        ['label' => 'Public Profiles', 'url' => '/community'],
        ['label' => 'Points Wallet', 'url' => '/points-wallet'],
    ],
    'Learning Worlds' => [
        ['label' => 'Math Quest', 'url' => '/apps/math-quest'],
        ['label' => 'Reading Garden', 'url' => '/apps/reading-garden'],
        ['label' => 'Focus Forest', 'url' => '/apps/focus-forest'],
        ['label' => 'Quiz Galaxy', 'url' => '/apps/quiz-galaxy'],
    ],
    'Support' => [
        ['label' => 'Parents', 'url' => '/apps?role=parent'],
        ['label' => 'Teachers', 'url' => '/apps?role=teacher'],
        ['label' => 'Privacy', 'url' => '/privacy-policy'],
        ['label' => 'Contact', 'url' => '/contact-us'],
    ],
];

if (Schema::hasTable('site_settings')) {
    DB::table('site_settings')->updateOrInsert(['key' => 'shell_navigation_json'], payload('site_settings', [
        'key' => 'shell_navigation_json',
        'value' => $nav,
        'label' => 'Shell Navigation',
        'type' => 'json',
        'group' => 'Navigation',
        'is_enabled' => true,
        'sort_order' => 10,
    ]));

    DB::table('site_settings')->updateOrInsert(['key' => 'shell_footer_groups_json'], payload('site_settings', [
        'key' => 'shell_footer_groups_json',
        'value' => $footer,
        'label' => 'Footer Groups',
        'type' => 'json',
        'group' => 'Footer',
        'is_enabled' => true,
        'sort_order' => 20,
    ]));

    foreach ([
        'dashboard_heading' => 'Welcome back to your StudyBuddy space.',
        'dashboard_intro' => 'Control your profile, apps, quests, points, and learning preferences from one clean dashboard.',
        'brand_promise' => 'A living learning universe with apps, profiles, points, community showcases, and playful progress.',
    ] as $key => $value) {
        DB::table('site_settings')->updateOrInsert(['key' => $key], payload('site_settings', [
            'key' => $key,
            'value' => $value,
            'label' => ucwords(str_replace('_', ' ', $key)),
            'type' => 'text',
            'group' => 'Platform',
            'is_enabled' => true,
            'sort_order' => 50,
        ]));
    }

    echo "✓ nav/footer/settings synced\n";
}

if (Schema::hasTable('navigation_items')) {
    foreach ([
        ['label' => 'Home', 'url' => '/', 'sort_order' => 10],
        ['label' => 'Apps', 'url' => '/apps', 'sort_order' => 20],
        ['label' => 'Community', 'url' => '/community', 'sort_order' => 30],
        ['label' => 'Search', 'url' => '/search', 'sort_order' => 40],
        ['label' => 'Profile', 'url' => '/profile', 'sort_order' => 90],
    ] as $item) {
        DB::table('navigation_items')->updateOrInsert(['label' => $item['label']], payload('navigation_items', array_merge($item, [
            'is_enabled' => true,
            'group' => 'main',
            'location' => 'main',
        ])));
    }

    echo "✓ navigation_items synced\n";
}

if (Schema::hasTable('footer_items')) {
    foreach ([
        ['group' => 'Explore', 'label' => 'Apps', 'url' => '/apps', 'sort_order' => 10],
        ['group' => 'Explore', 'label' => 'Community', 'url' => '/community', 'sort_order' => 20],
        ['group' => 'Explore', 'label' => 'Search', 'url' => '/search', 'sort_order' => 30],
        ['group' => 'Account', 'label' => 'Dashboard', 'url' => '/dashboard', 'sort_order' => 40],
        ['group' => 'Account', 'label' => 'Profile Studio', 'url' => '/profile', 'sort_order' => 50],
    ] as $item) {
        DB::table('footer_items')->updateOrInsert(['label' => $item['label'], 'url' => $item['url']], payload('footer_items', array_merge($item, [
            'is_enabled' => true,
        ])));
    }

    echo "✓ footer_items synced\n";
}
