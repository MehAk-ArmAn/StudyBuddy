<?php

namespace App\Http\Controllers;

use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyAdminHealthController extends Controller
{
    public function index(): View
    {
        $checks = collect();

        foreach ([
            'users' => 'Users table',
            'site_settings' => 'Site settings table',
            'homepage_sections' => 'Homepage sections table',
            'homepage_section_items' => 'Homepage cards table',
            'pages' => 'Pages table',
            'studybuddy_mini_app_platforms' => 'Apps platform table',
        ] as $table => $label) {
            $exists = Schema::hasTable($table);
            $checks->push([
                'group' => 'Database',
                'label' => $label,
                'status' => $exists ? 'pass' : 'fail',
                'detail' => $exists ? "{$table} is available." : "{$table} is missing. Run php artisan migrate.",
            ]);
        }

        $legacyBuildRoot = public_path('web-apps');
        $legacyBuildEntries = is_dir($legacyBuildRoot) && ! is_link($legacyBuildRoot)
            ? array_values(array_diff(scandir($legacyBuildRoot) ?: [], ['.', '..', '.gitkeep']))
            : [];
        $legacyBuildSafe = ! is_link($legacyBuildRoot) && $legacyBuildEntries === [];

        $checks->push([
            'group' => 'Storage',
            'label' => 'Public web-build exposure',
            'status' => $legacyBuildSafe ? 'pass' : 'fail',
            'detail' => is_link($legacyBuildRoot)
                ? 'The retired public web-build directory must not be a symbolic link.'
                : ($legacyBuildEntries === []
                    ? 'No extracted app builds are exposed under public/web-apps.'
                    : 'Extracted builds remain under public/web-apps and must be moved or removed.'),
        ]);

        foreach ([
            'home' => 'Homepage route',
            'studybuddy.apps' => 'Apps route',
            'studybuddy.roles' => 'Roles route',
            'logout.confirm' => 'Logout confirmation route',
            'admin.control-room.final-platform' => 'Apps admin route',
            'admin.control-room.homepage-cms.index' => 'Homepage CMS route',
        ] as $routeName => $label) {
            $exists = Route::has($routeName);
            $checks->push([
                'group' => 'Routes',
                'label' => $label,
                'status' => $exists ? 'pass' : 'fail',
                'detail' => $exists ? "Named route {$routeName} is registered." : "Named route {$routeName} is missing.",
            ]);
        }

        foreach ([
            storage_path('framework/cache/data') => 'Framework cache directory',
            storage_path('framework/sessions') => 'Session directory',
            storage_path('framework/views') => 'Compiled views directory',
            storage_path('logs') => 'Logs directory',
            storage_path('app/studybuddy-app-packages') => 'Uploaded app packages directory',
            StudyBuddyWebAppPublisher::buildRoot() => 'Private published web apps directory',
        ] as $path => $label) {
            $exists = is_dir($path);
            $isLink = is_link($path);
            $writable = $exists && ! $isLink && is_writable($path);
            $checks->push([
                'group' => 'Storage',
                'label' => $label,
                'status' => $writable ? 'pass' : ($exists && ! $isLink ? 'warn' : 'fail'),
                'detail' => $isLink
                    ? 'Directory must not be a symbolic link.'
                    : ($writable ? 'Directory exists and is writable.' : ($exists ? 'Directory exists but is not writable.' : 'Directory is missing.')),
            ]);
        }

        $zipSupport = class_exists(\ZipArchive::class) || class_exists(\PharData::class);
        $checks->push([
            'group' => 'App launcher',
            'label' => 'ZIP extraction support',
            'status' => $zipSupport ? 'pass' : 'fail',
            'detail' => $zipSupport ? 'ZipArchive or PharData is available.' : 'Enable PHP ZipArchive or Phar support before uploading web apps.',
        ]);

        $summary = [
            'pass' => $checks->where('status', 'pass')->count(),
            'warn' => $checks->where('status', 'warn')->count(),
            'fail' => $checks->where('status', 'fail')->count(),
        ];

        return view('admin.health.index', compact('checks', 'summary'));
    }
}
