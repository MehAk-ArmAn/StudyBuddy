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
            $out[$key] = is_array($value)
                ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : $value;
        }
    }

    if (Schema::hasColumn($table, 'updated_at')) $out['updated_at'] = now();
    if (Schema::hasColumn($table, 'created_at')) $out['created_at'] = now();

    return $out;
}

$cleanNav = [
    ['label' => 'Home', 'url' => '/', 'roles' => ['all']],
    ['label' => 'Apps', 'url' => '/apps', 'roles' => ['all']],
    ['label' => 'Roles', 'url' => '/roles', 'roles' => ['all']],
    ['label' => 'Community', 'url' => '/community', 'roles' => ['all']],
    ['label' => 'Search', 'url' => '/search', 'roles' => ['all']],
];

if (Schema::hasTable('site_settings')) {
    DB::table('site_settings')->updateOrInsert(
        ['key' => 'shell_navigation_json'],
        payload('site_settings', [
            'key' => 'shell_navigation_json',
            'label' => 'Shell Navigation',
            'group' => 'Navigation',
            'type' => 'json',
            'value' => $cleanNav,
            'is_enabled' => true,
            'sort_order' => 10,
        ])
    );

    echo "✓ clean shell_navigation_json saved\n";
}

if (Schema::hasTable('navigation_items')) {
    foreach (['Dashboard', 'Profile', 'Parents', 'Teachers'] as $label) {
        if (Schema::hasColumn('navigation_items', 'is_enabled')) {
            DB::table('navigation_items')
                ->where('label', $label)
                ->update(['is_enabled' => false, 'updated_at' => now()]);
        }
    }

    foreach ([
        ['label' => 'Home', 'url' => '/', 'sort_order' => 10],
        ['label' => 'Apps', 'url' => '/apps', 'sort_order' => 20],
        ['label' => 'Roles', 'url' => '/roles', 'sort_order' => 30],
        ['label' => 'Community', 'url' => '/community', 'sort_order' => 40],
        ['label' => 'Search', 'url' => '/search', 'sort_order' => 50],
    ] as $item) {
        DB::table('navigation_items')->updateOrInsert(
            ['label' => $item['label']],
            payload('navigation_items', array_merge($item, [
                'is_enabled' => true,
                'group' => 'main',
                'location' => 'main',
                'target' => '_self',
            ]))
        );

        echo "✓ nav item {$item['label']}\n";
    }
}

echo "DONE: normal navbar cleaned. Dashboard/Profile stay in account dropdown only.\n";
