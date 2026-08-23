<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function hasCol(string $table, string $col): bool {
    return Schema::hasTable($table) && Schema::hasColumn($table, $col);
}

function updateWhere(string $table, array $where, array $data): void {
    if (!Schema::hasTable($table)) return;

    $safe = [];
    foreach ($data as $key => $value) {
        if (hasCol($table, $key)) $safe[$key] = $value;
    }
    if (hasCol($table, 'updated_at')) $safe['updated_at'] = now();
    if (!$safe) return;

    $query = DB::table($table);
    foreach ($where as $key => $value) {
        if (hasCol($table, $key)) $query->where($key, $value);
    }
    $query->update($safe);
}

function setting(string $key, string $value, string $label, int $sort = 20): void {
    if (!Schema::hasTable('site_settings')) return;

    $data = [
        'key' => $key,
        'label' => $label,
        'value' => $value,
        'type' => 'image_url',
        'group' => 'StudyBuddy Repo Visuals',
        'is_enabled' => true,
        'sort_order' => $sort,
    ];

    $safe = [];
    foreach ($data as $field => $val) {
        if (hasCol('site_settings', $field)) $safe[$field] = $val;
    }

    if (hasCol('site_settings', 'created_at')) $safe['created_at'] = now();
    if (hasCol('site_settings', 'updated_at')) $safe['updated_at'] = now();

    DB::table('site_settings')->updateOrInsert(['key' => $key], $safe);
}

$repo = 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main';

$hero = 'https://github.com/MehAk-ArmAn/StudyBuddy-Imgs/blob/main/hero/hero-dolphin-book.png?raw=true';
$heroRaw = $repo . '/hero/hero-dolphin-book.png';

$learning = $repo . '/homepage-paths/path-learning.png';
$apps = $repo . '/homepage-paths/path-apps.png';
$parents = $repo . '/homepage-paths/path-parents.png';
$teachers = $repo . '/homepage-paths/path-teachers.png';

setting('homepage_hero_image', $hero, 'Homepage Hero Dolphin Book Image', 1);
setting('homepage_hero_image_raw', $heroRaw, 'Homepage Hero Raw Image', 2);
setting('role_image_student', $apps, 'Student Role Image', 10);
setting('role_image_parent', $parents, 'Parent Role Image', 11);
setting('role_image_teacher', $teachers, 'Teacher Role Image', 12);
setting('role_image_independent_learner', $learning, 'Independent Learner Role Image', 13);

$homeMap = [
    'hero' => $hero,
    'apps-universe' => $apps,
    'safe-connections' => $parents,
    'roles' => $teachers,
    'community' => $learning,
    'final-cta' => $hero,
];

foreach ($homeMap as $key => $image) {
    updateWhere('homepage_sections', ['section_key' => $key], [
        'image_path' => $image,
        'background_image_path' => $image,
    ]);
}

$pageMap = [
    'about' => $hero,
    'apps' => $apps,
    'roles' => $teachers,
    'community' => $learning,
    'contact' => $hero,
    'privacy-policy' => $parents,
    'terms' => $parents,
    'disclaimer' => $learning,
    'cookies' => $learning,
    'community-guidelines' => $parents,
    'copyright' => $learning,
    'data-deletion' => $parents,
];

foreach ($pageMap as $slug => $image) {
    updateWhere('pages', ['slug' => $slug], [
        'hero_image_path' => $image,
        'image_path' => $image,
    ]);
}

$appImageMap = [
    'math-quest' => $apps,
    'spelling-sprint' => $learning,
    'reading-garden' => $learning,
    'focus-forest' => $parents,
    'planner-city' => $teachers,
    'quiz-galaxy' => $apps,
    'shape-lab' => $learning,
    'flashcard-forge' => $apps,
];

foreach ($appImageMap as $slug => $image) {
    updateWhere('studybuddy_mini_app_platforms', ['slug' => $slug], [
        'image_path' => $image,
        'hero_image' => $image,
    ]);
}

echo "DONE: StudyBuddy visuals synced from StudyBuddy-Imgs repo.\n";
