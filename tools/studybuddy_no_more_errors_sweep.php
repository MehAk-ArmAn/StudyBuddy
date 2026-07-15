<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

echo "StudyBuddy Final Sweep\n";
echo "======================\n\n";

$failed = 0;

function checkItem(string $label, bool $ok, string $bad = ''): void {
    global $failed;
    echo ($ok ? "✅ " : "❌ ") . $label . ($ok ? "" : " — {$bad}") . PHP_EOL;
    if (!$ok) $failed++;
}

$routes = collect(Route::getRoutes())->map(fn ($route) => $route->getName())->filter()->values();

foreach ([
    'home',
    'dashboard',
    'profile',
    'studybuddy.apps',
    'studybuddy.community',
    'studybuddy.roles',
    'studybuddy.search',
    'admin.pages.index',
    'admin.control-room.account.edit',
    'admin.control-room.role-tools.index',
] as $routeName) {
    checkItem("Route: {$routeName}", $routes->contains($routeName), 'missing route');
}

foreach ([
    'users',
    'site_settings',
    'pages',
    'navigation_items',
    'footer_items',
    'studybuddy_mini_app_platforms',
    'studybuddy_learning_groups',
    'studybuddy_assignments',
] as $table) {
    checkItem("Table: {$table}", Schema::hasTable($table), 'missing table');
}

if (Schema::hasTable('site_settings')) {
    $nav = DB::table('site_settings')->where('key', 'shell_navigation_json')->value('value') ?? '';
    $footer = DB::table('site_settings')->where('key', 'shell_footer_groups_json')->value('value') ?? '';

    checkItem('Navbar DB JSON exists', trim($nav) !== '', 'shell_navigation_json empty');
    checkItem('Footer DB JSON exists', trim($footer) !== '', 'shell_footer_groups_json empty');
    checkItem('Navbar has Roles', str_contains($nav, '/roles'), 'missing /roles');
    checkItem('Navbar has Community', str_contains($nav, '/community'), 'missing /community');
    checkItem('Normal navbar avoids Dashboard/Profile', !str_contains($nav, '/dashboard') && !str_contains($nav, '/profile'), 'dashboard/profile should only be account dropdown');
}

if (Schema::hasTable('pages')) {
    foreach (['about','contact','privacy-policy','terms','disclaimer','cookies','community-guidelines','copyright','data-deletion','roles'] as $slug) {
        checkItem("DB page: /{$slug}", DB::table('pages')->where('slug', $slug)->exists(), 'missing page row');
    }
}

foreach ([
    'resources/views/admin/resources/form.blade.php',
    'resources/views/admin/resources/index.blade.php',
    'resources/views/home.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/layouts/partials/sb-shell-navbar.blade.php',
    'resources/views/layouts/partials/sb-shell-footer.blade.php',
] as $file) {
    checkItem("File exists: {$file}", file_exists(base_path($file)), 'missing file');
}

echo "\nResult: " . ($failed ? "{$failed} issue(s) found" : "all structural checks passed") . PHP_EOL;
exit($failed ? 1 : 0);
