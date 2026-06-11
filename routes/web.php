<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AppController as AdminAppController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardContentController;
use App\Http\Controllers\Admin\FooterController;
use App\Http\Controllers\Admin\LegalController as AdminLegalController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/apps', [AppPageController::class, 'index'])->name('apps.index');
Route::get('/apps/{app}/play', [AppPageController::class, 'play'])->name('apps.play');
Route::get('/apps/{app}', [AppPageController::class, 'show'])->name('apps.show');
Route::get('/rewards', [PublicPageController::class, 'rewards'])->name('rewards');
Route::get('/privacy-policy', [LegalPageController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms-and-conditions', [LegalPageController::class, 'terms'])->name('legal.terms');
Route::get('/cookie-policy', [LegalPageController::class, 'cookies'])->name('legal.cookies');
Route::get('/data-deletion', [LegalPageController::class, 'dataDeletion'])->name('legal.data-deletion');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::get('/about', [PublicPageController::class, 'about'])->name('about');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'store'])->name('register.store');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', DashboardController::class)->name('dashboard');

Route::get('/student/dashboard', [StudentDashboardController::class, 'dashboard'])->name('student.dashboard');
Route::get('/student/apps', [StudentDashboardController::class, 'apps'])->name('student.apps');
Route::get('/student/rewards', [StudentDashboardController::class, 'rewards'])->name('student.rewards');
Route::get('/student/progress', [StudentDashboardController::class, 'progress'])->name('student.progress');
Route::get('/student/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
Route::get('/student/settings', [StudentDashboardController::class, 'settings'])->name('student.settings');

Route::get('/parent/dashboard', [ParentDashboardController::class, 'dashboard'])->name('parent.dashboard');
Route::get('/parent/children', [ParentDashboardController::class, 'children'])->name('parent.children');
Route::get('/parent/progress', [ParentDashboardController::class, 'progress'])->name('parent.progress');
Route::get('/parent/reports', [ParentDashboardController::class, 'reports'])->name('parent.reports');
Route::get('/parent/settings', [ParentDashboardController::class, 'settings'])->name('parent.settings');

Route::get('/teacher/dashboard', [TeacherDashboardController::class, 'dashboard'])->name('teacher.dashboard');
Route::get('/teacher/classes', [TeacherDashboardController::class, 'classes'])->name('teacher.classes');
Route::get('/teacher/students', [TeacherDashboardController::class, 'students'])->name('teacher.students');
Route::get('/teacher/assignments', [TeacherDashboardController::class, 'assignments'])->name('teacher.assignments');
Route::get('/teacher/reports', [TeacherDashboardController::class, 'reports'])->name('teacher.reports');
Route::get('/teacher/settings', [TeacherDashboardController::class, 'settings'])->name('teacher.settings');

Route::redirect('/demo/primary', '/student/dashboard')->name('demo.primary');
Route::redirect('/demo/secondary', '/student/dashboard')->name('demo.secondary');
Route::redirect('/demo/parent', '/parent/dashboard')->name('demo.parent');
Route::redirect('/demo/teacher', '/teacher/dashboard')->name('demo.teacher');
Route::redirect('/demo/admin', '/admin/dashboard')->name('demo.admin');
Route::redirect('/for-parents', '/parent/dashboard')->name('for-parents');
Route::redirect('/for-teachers', '/teacher/dashboard')->name('for-teachers');
Route::redirect('/pricing', '/rewards')->name('pricing');
Route::redirect('/support', '/contact')->name('support');

Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'authenticate'])->name('admin.login.attempt');
Route::middleware('admin.session')->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pages', [PageController::class, 'pages'])->name('pages');
    Route::get('/navigation', [MenuController::class, 'navigation'])->name('navigation');
    Route::get('/footer', [FooterController::class, 'footer'])->name('footer');
    Route::get('/apps', [AdminAppController::class, 'apps'])->name('apps');
    Route::get('/rewards', [AdminAppController::class, 'rewards'])->name('rewards');
    Route::get('/dashboards', [DashboardContentController::class, 'dashboards'])->name('dashboards');
    Route::get('/legal', [AdminLegalController::class, 'legal'])->name('legal');
    Route::get('/assets', [AssetController::class, 'assets'])->name('assets');
    Route::get('/settings', [SettingsController::class, 'settings'])->name('settings');
    Route::get('/users', [UserController::class, 'users'])->name('users');
    Route::match(['get', 'post'], '/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
