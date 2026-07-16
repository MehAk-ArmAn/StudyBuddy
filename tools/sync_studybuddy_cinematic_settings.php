<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('site_settings')) {
    echo "site_settings missing, skipped.\n";
    exit;
}

function payload(array $data): array {
    $out = [];

    foreach ($data as $key => $value) {
        if (Schema::hasColumn('site_settings', $key)) {
            $out[$key] = $value;
        }
    }

    if (Schema::hasColumn('site_settings', 'updated_at')) $out['updated_at'] = now();
    if (Schema::hasColumn('site_settings', 'created_at')) $out['created_at'] = now();

    return $out;
}

$settings = [
    'cinematic_eyebrow' => 'StudyBuddy Learning Universe',
    'cinematic_title' => 'Play Beyond Ordinary',
    'cinematic_subtitle' => 'Learn, play, grow, and build your profile inside one animated learning world.',
    'cinematic_primary_label' => 'Enter app universe',
    'cinematic_primary_url' => '/apps',
    'cinematic_secondary_label' => 'Choose your role',
    'cinematic_secondary_url' => '/roles',
    'cinematic_third_label' => 'See community',
    'cinematic_third_url' => '/community',
];

foreach ($settings as $key => $value) {
    DB::table('site_settings')->updateOrInsert(
        ['key' => $key],
        payload([
            'key' => $key,
            'label' => ucwords(str_replace('_', ' ', $key)),
            'value' => $value,
            'type' => 'text',
            'group' => 'Homepage Cinematic',
            'is_enabled' => true,
            'sort_order' => 15,
        ])
    );

    echo "✓ {$key}\n";
}

echo "Cinematic homepage settings ready.\n";
