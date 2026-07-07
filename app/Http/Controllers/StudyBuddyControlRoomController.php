<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyControlRoomController extends Controller
{
    public function index(): View
    {
        $this->ensureAdmin();

        $count = fn (string $table) => Schema::hasTable($table) ? DB::table($table)->count() : 0;
        $stats = [
            'users' => Schema::hasTable('users') ? User::count() : 0,
            'settings' => $count('site_settings'),
            'navigation' => $count('navigation_items'),
            'footer' => $count('footer_items'),
            'apps' => $count('study_buddy_mini_app_platforms') ?: $count('studybuddy_mini_app_platforms'),
            'quests' => $count('saved_quests'),
        ];

        return view('admin.control-room.index', [
            'stats' => $stats,
            'sections' => $this->sections(),
        ]);
    }

    private function sections(): array
    {
        return [
            ['title' => 'Website Shell', 'subtitle' => 'Navbar, footer, logo, brand promise, social links.', 'url' => url('/admin/control-room/shell'), 'icon' => asset('assets/studybuddy-control/shell.svg'), 'class' => 'purple', 'cta' => 'Edit shell'],
            ['title' => 'Content Studio', 'subtitle' => 'Public page content, learning copy, sections, blocks.', 'url' => url('/admin/control-room/content-studio'), 'icon' => asset('assets/studybuddy-control/content.svg'), 'class' => 'blue', 'cta' => 'Manage content'],
            ['title' => 'Apps & Platform', 'subtitle' => 'Mini apps, platform readiness, links, points.', 'url' => url('/admin/control-room/final-platform'), 'icon' => asset('assets/studybuddy-control/apps.svg'), 'class' => 'cyan', 'cta' => 'Manage apps'],
            ['title' => 'Users & Roles', 'subtitle' => 'Students, parents, teachers, independent learners.', 'url' => url('/admin/control-room/users'), 'icon' => asset('assets/studybuddy-control/users.svg'), 'class' => 'purple', 'cta' => 'View users'],
            ['title' => 'Safety Review', 'subtitle' => 'Role checks, verification, child/parent safety.', 'url' => url('/admin/control-room/verifications'), 'icon' => asset('assets/studybuddy-control/safety.svg'), 'class' => 'blue', 'cta' => 'Review safety'],
            ['title' => 'Site Settings', 'subtitle' => 'Advanced setting keys and fallback values.', 'url' => url('/admin/control-room/site-settings'), 'icon' => asset('assets/studybuddy-control/settings.svg'), 'class' => 'cyan', 'cta' => 'Open settings'],
        ];
    }

    private function ensureAdmin(): void
    {
        $user = auth()->user();

        $isAdmin = $user && (
            ($user->is_admin ?? false)
            || ($user->role ?? null) === 'admin'
            || ($user->email ?? null) === 'admin@studybuddy.fun'
        );

        abort_unless($isAdmin, 403);
    }
}
