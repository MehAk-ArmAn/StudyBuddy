<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AppController as AdminAppController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CmsResourceController;
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
    Route::get('/pages', [CmsResourceController::class, 'index'])->defaults('resource', 'pages')->name('pages');
    Route::get('/navigation', [CmsResourceController::class, 'index'])->defaults('resource', 'navigation')->name('navigation');
    Route::get('/footer', [CmsResourceController::class, 'index'])->defaults('resource', 'footer-columns')->name('footer');
    Route::get('/apps', [CmsResourceController::class, 'index'])->defaults('resource', 'apps')->name('apps');
    Route::get('/app-features', [CmsResourceController::class, 'index'])->defaults('resource', 'app-features')->name('app-features');
    Route::get('/rewards', [CmsResourceController::class, 'index'])->defaults('resource', 'rewards')->name('rewards');
    Route::get('/dashboards', [CmsResourceController::class, 'index'])->defaults('resource', 'dashboard-content')->name('dashboards');
    Route::get('/legal', [CmsResourceController::class, 'index'])->defaults('resource', 'legal')->name('legal');
    Route::get('/assets', [CmsResourceController::class, 'index'])->defaults('resource', 'assets')->name('assets');
    Route::get('/settings', [CmsResourceController::class, 'index'])->defaults('resource', 'settings')->name('settings');
    Route::get('/users', [CmsResourceController::class, 'index'])->defaults('resource', 'users')->name('users');
    Route::get('/resources/{resource}', [CmsResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}/create', [CmsResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources/{resource}', [CmsResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}/{id}/edit', [CmsResourceController::class, 'edit'])->whereNumber('id')->name('resources.edit');
    Route::put('/resources/{resource}/{id}', [CmsResourceController::class, 'update'])->whereNumber('id')->name('resources.update');
    Route::delete('/resources/{resource}/{id}', [CmsResourceController::class, 'destroy'])->whereNumber('id')->name('resources.destroy');
    Route::match(['get', 'post'], '/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
