<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$checks = [];

function ok(string $label, bool $pass, string $fail = ''): void {
    echo ($pass ? "✅ " : "❌ ") . $label . ($pass ? "" : " — {$fail}") . PHP_EOL;
}

$routeNames = collect(Route::getRoutes())->map(fn ($route) => $route->getName())->filter()->values();

foreach ([
    'home',
    'dashboard',
    'profile',
    'studybuddy.apps',
    'studybuddy.community',
    'studybuddy.roles',
    'studybuddy.search',
    'admin.control-room.account.edit',
    'admin.control-room.role-tools.index',
    'admin.pages.index',
] as $routeName) {
    ok("Route {$routeName}", $routeNames->contains($routeName), 'missing');
}

foreach ([
    'users',
    'site_settings',
    'pages',
    'page_sections',
    'navigation_items',
    'footer_items',
    'studybuddy_mini_app_platforms',
    'studybuddy_learning_groups',
    'studybuddy_assignments',
] as $table) {
    ok("Table {$table}", Schema::hasTable($table), 'missing');
}

if (Schema::hasTable('users')) {
    $admin = DB::table('users')->where('email', 'admin@studybuddy.fun')->first();
    ok('Default admin account exists', (bool) $admin, 'admin@studybuddy.fun missing');

    if ($admin) {
        ok('Default admin has admin flag/role', (bool) ($admin->is_admin ?? false) || ($admin->role ?? null) === 'admin', 'not admin');
    }
}

if (Schema::hasTable('site_settings')) {
    $nav = DB::table('site_settings')->where('key', 'shell_navigation_json')->value('value') ?? '';
    ok('Navbar JSON stored in DB', trim($nav) !== '', 'empty shell_navigation_json');
    ok('Navbar avoids Dashboard/Profile normal links', !str_contains($nav, '/dashboard') && !str_contains($nav, '/profile'), 'Dashboard/Profile should be account dropdown only');
    ok('Navbar includes Roles', str_contains($nav, '/roles'), 'missing /roles');
    ok('Navbar includes Community', str_contains($nav, '/community'), 'missing /community');
}

foreach (['about','contact','privacy-policy','terms','disclaimer','cookies','community-guidelines','copyright','data-deletion','roles'] as $slug) {
    if (Schema::hasTable('pages')) {
        ok("DB page /{$slug}", DB::table('pages')->where('slug', $slug)->exists(), 'missing from pages table');
    }
}

echo PHP_EOL . "Run browser checks after this:" . PHP_EOL;
echo "php artisan route:list | grep -E \"admin/control-room|roles|about|profile|dashboard|apps|community|search\"" . PHP_EOL;
