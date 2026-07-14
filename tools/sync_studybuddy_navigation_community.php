<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function table_payload(string $table, array $payload): array {
    $out = [];
    foreach ($payload as $key => $value) {
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
    ['label' => 'Learning', 'url' => '/apps?section=learning', 'roles' => ['all']],
    ['label' => 'Parents', 'url' => '/apps?role=parent', 'roles' => ['all']],
    ['label' => 'Teachers', 'url' => '/apps?role=teacher', 'roles' => ['all']],
    ['label' => 'Safety', 'url' => '/apps?section=safety', 'roles' => ['all']],
    ['label' => 'Rewards', 'url' => '/apps?section=rewards', 'roles' => ['all']],
];

if (Schema::hasTable('site_settings')) {
    DB::table('site_settings')->updateOrInsert(
        ['key' => 'shell_navigation_json'],
        table_payload('site_settings', [
            'key' => 'shell_navigation_json',
            'value' => json_encode($nav, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'label' => 'Shell Navigation',
            'type' => 'json',
            'group' => 'Navigation',
            'is_enabled' => true,
            'sort_order' => 10,
        ])
    );

    echo "✓ shell_navigation_json includes Community\n";
}

if (Schema::hasTable('navigation_items')) {
    $items = [
        ['label' => 'Home', 'url' => '/', 'sort_order' => 10],
        ['label' => 'Apps', 'url' => '/apps', 'sort_order' => 20],
        ['label' => 'Community', 'url' => '/community', 'sort_order' => 30],
        ['label' => 'Profile', 'url' => '/profile', 'sort_order' => 90],
    ];

    foreach ($items as $item) {
        DB::table('navigation_items')->updateOrInsert(
            ['label' => $item['label']],
            table_payload('navigation_items', array_merge($item, [
                'is_enabled' => true,
                'group' => 'main',
                'location' => 'main',
                'target' => '_self',
            ]))
        );

        echo "✓ navigation item {$item['label']}\n";
    }
}

if (Schema::hasTable('footer_items')) {
    $items = [
        ['group' => 'Explore', 'label' => 'Apps', 'url' => '/apps', 'sort_order' => 10],
        ['group' => 'Explore', 'label' => 'Community', 'url' => '/community', 'sort_order' => 20],
        ['group' => 'Account', 'label' => 'Profile Studio', 'url' => '/profile', 'sort_order' => 30],
        ['group' => 'Account', 'label' => 'Dashboard', 'url' => '/dashboard', 'sort_order' => 40],
    ];

    foreach ($items as $item) {
        DB::table('footer_items')->updateOrInsert(
            ['label' => $item['label'], 'url' => $item['url']],
            table_payload('footer_items', array_merge($item, [
                'is_enabled' => true,
                'target' => '_self',
            ]))
        );

        echo "✓ footer item {$item['label']}\n";
    }
}
