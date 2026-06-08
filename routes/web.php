<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/showcase', [HomeController::class, 'showcase'])->name('showcase');
Route::redirect('/for-parents', '/demo/parent')->name('for-parents');
Route::redirect('/for-teachers', '/demo/teacher')->name('for-teachers');
Route::redirect('/pricing', '/rewards')->name('pricing');
Route::redirect('/support', '/showcase')->name('support');
Route::get('/apps', [AppController::class, 'index'])->name('apps.index');
Route::get('/apps/math-quest', [AppController::class, 'mathQuest'])->name('apps.math-quest');
Route::get('/apps/math-quest/play', [AppController::class, 'playMathQuest'])->name('apps.math-quest.play');
Route::get('/demo/primary', [DemoController::class, 'primary'])->name('demo.primary');
Route::get('/demo/secondary', [DemoController::class, 'secondary'])->name('demo.secondary');
Route::get('/demo/parent', [DemoController::class, 'parent'])->name('demo.parent');
Route::get('/demo/teacher', [DemoController::class, 'teacher'])->name('demo.teacher');
Route::get('/rewards', RewardController::class)->name('rewards');
Route::get('/demo/admin', [DemoController::class, 'admin'])->name('demo.admin');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');

    Route::middleware('admin.auth')->group(function (): void {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/pages', [AdminController::class, 'pages'])->name('pages');
        Route::get('/pages/{page:key}', [AdminController::class, 'editPage'])->name('pages.edit');
        Route::put('/pages/{page:key}', [AdminController::class, 'updatePage'])->name('pages.update');
        Route::get('/navigation', [AdminController::class, 'navigation'])->name('navigation');
        Route::put('/navigation', [AdminController::class, 'saveNavigation'])->name('navigation.save');
        Route::get('/footer', [AdminController::class, 'footer'])->name('footer');
        Route::put('/footer', [AdminController::class, 'saveFooter'])->name('footer.save');
        Route::get('/apps', [AdminController::class, 'apps'])->name('apps');
        Route::put('/apps', [AdminController::class, 'saveApps'])->name('apps.save');
        Route::get('/rewards', [AdminController::class, 'rewards'])->name('rewards');
        Route::put('/rewards', [AdminController::class, 'saveRewards'])->name('rewards.save');
        Route::get('/badges', [AdminController::class, 'badges'])->name('badges');
        Route::put('/badges', [AdminController::class, 'saveBadges'])->name('badges.save');
        Route::get('/dashboards', [AdminController::class, 'dashboards'])->name('dashboards');
        Route::put('/dashboards', [AdminController::class, 'saveDashboards'])->name('dashboards.save');
        Route::get('/showcase', [AdminController::class, 'showcase'])->name('showcase');
        Route::put('/showcase', [AdminController::class, 'saveShowcase'])->name('showcase.save');
        Route::get('/mobile-preview', [AdminController::class, 'mobilePreview'])->name('mobile-preview');
        Route::put('/mobile-preview', [AdminController::class, 'saveMobilePreview'])->name('mobile-preview.save');
        Route::get('/assets', [AdminController::class, 'assets'])->name('assets');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::put('/settings', [AdminController::class, 'saveSettings'])->name('settings.save');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/logout', [AdminAuthController::class, 'logout']);
    });
});
