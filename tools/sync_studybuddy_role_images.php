<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasTable('site_settings')) {
    echo "site_settings table missing, skipped.\n";
    exit;
}

function settingPayload(array $data): array {
    $out = [];
    foreach ($data as $key => $value) {
        if (Schema::hasColumn('site_settings', $key)) {
            $out[$key] = $value;
        }
    }
    if (Schema::hasColumn('site_settings', 'created_at')) $out['created_at'] = now();
    if (Schema::hasColumn('site_settings', 'updated_at')) $out['updated_at'] = now();
    return $out;
}

$images = [
    'role_image_student' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png',
    'role_image_parent' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png',
    'role_image_teacher' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png',
    'role_image_independent_learner' => 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png',
];

foreach ($images as $key => $value) {
    DB::table('site_settings')->updateOrInsert(
        ['key' => $key],
        settingPayload([
            'key' => $key,
            'label' => ucwords(str_replace('_', ' ', $key)),
            'value' => $value,
            'type' => 'image_url',
            'group' => 'Role Images',
            'is_enabled' => true,
            'sort_order' => 20,
        ])
    );

    echo "✓ {$key}\n";
}

echo "Role image settings synced.\n";
