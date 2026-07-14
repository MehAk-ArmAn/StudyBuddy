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
            'students' => Schema::hasTable('users') ? DB::table('users')->where('role', 'student')->count() : 0,
            'parents' => Schema::hasTable('users') ? DB::table('users')->where('role', 'parent')->count() : 0,
            'teachers' => Schema::hasTable('users') ? DB::table('users')->where('role', 'teacher')->count() : 0,
            'settings' => $count('site_settings'),
            'pages' => $count('pages'),
            'sections' => $count('page_sections') + $count('homepage_sections'),
            'navigation' => $count('navigation_items'),
            'footer' => $count('footer_items'),
            'apps' => $count('studybuddy_mini_app_platforms') ?: $count('study_buddy_mini_app_platforms'),
            'quests' => $count('saved_quests'),
            'groups' => $count('studybuddy_learning_groups'),
            'assignments' => $count('studybuddy_assignments'),
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
            ['title' => 'Pages & Legal', 'subtitle' => 'About, contact, privacy, terms, cookies, guidelines, deletion.', 'url' => url('/admin/control-room/pages-legal'), 'icon' => asset('assets/studybuddy-control/content.svg'), 'class' => 'pink', 'cta' => 'Edit pages'],
            ['title' => 'Content Studio', 'subtitle' => 'Public copy, cards, homepage blocks, CTAs, testimonials.', 'url' => url('/admin/control-room/content-studio'), 'icon' => asset('assets/studybuddy-control/content.svg'), 'class' => 'blue', 'cta' => 'Manage content'],
            ['title' => 'Apps & Platform', 'subtitle' => 'Mini apps, points, readiness, app images, play settings.', 'url' => url('/admin/control-room/final-platform'), 'icon' => asset('assets/studybuddy-control/apps.svg'), 'class' => 'cyan', 'cta' => 'Manage apps'],
            ['title' => 'Role Tools', 'subtitle' => 'Parent children, teacher classes, assignments, role metrics.', 'url' => url('/admin/control-room/role-tools'), 'icon' => asset('assets/studybuddy-control/users.svg'), 'class' => 'purple', 'cta' => 'View role data'],
            ['title' => 'Users & Roles', 'subtitle' => 'Students, parents, teachers, independent learners, admins.', 'url' => url('/admin/control-room/users'), 'icon' => asset('assets/studybuddy-control/users.svg'), 'class' => 'blue', 'cta' => 'Manage users'],
            ['title' => 'Safety Review', 'subtitle' => 'Role checks, verification, child/parent safety workflows.', 'url' => url('/admin/control-room/verifications'), 'icon' => asset('assets/studybuddy-control/safety.svg'), 'class' => 'cyan', 'cta' => 'Review safety'],
            ['title' => 'Site Settings', 'subtitle' => 'Advanced DB-backed setting keys and fallback values.', 'url' => url('/admin/control-room/site-settings'), 'icon' => asset('assets/studybuddy-control/settings.svg'), 'class' => 'pink', 'cta' => 'Open settings'],
            ['title' => 'Admin Account', 'subtitle' => 'Change your admin profile picture, email, and password.', 'url' => url('/admin/control-room/account'), 'icon' => asset('assets/studybuddy-control/settings.svg'), 'class' => 'purple', 'cta' => 'Secure account'],
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
