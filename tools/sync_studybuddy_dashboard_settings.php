<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('site_settings')) {
    echo "site_settings table not found, skipped.\n";
    exit;
}

$settings = [
    'dashboard_heading' => 'Welcome back to your StudyBuddy space.',
    'dashboard_intro' => 'Control your profile, apps, quests, points, and learning preferences from one clean dashboard.',
    'community_heading' => 'Discover StudyBuddy learners.',
    'community_intro' => 'Public profiles showcase learning goals, favorite app worlds, and progress style.',
];

foreach ($settings as $key => $value) {
    $payload = ['value' => $value];

    foreach ([
        'group' => 'Dashboard',
        'type' => 'text',
        'label' => ucwords(str_replace('_', ' ', $key)),
        'is_enabled' => true,
        'updated_at' => now(),
    ] as $column => $columnValue) {
        if (Schema::hasColumn('site_settings', $column)) {
            $payload[$column] = $columnValue;
        }
    }

    if (Schema::hasColumn('site_settings', 'created_at')) {
        $payload['created_at'] = now();
    }

    DB::table('site_settings')->updateOrInsert(['key' => $key], $payload);
    echo "✓ {$key}\n";
}

echo "Dashboard settings ready.\n";
