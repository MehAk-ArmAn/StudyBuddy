<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FooterItemController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\HomepageSectionItemController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\NavigationItemController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PageSectionController as AdminPageSectionController;
use App\Http\Controllers\Admin\PageSectionItemController as AdminPageSectionItemController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserAccessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [UserAccessController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAccessController::class, 'login'])->name('login.store');
    Route::get('/register', [UserAccessController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserAccessController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [UserAccessController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::put('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');
});

foreach (['apps', 'for-parents', 'for-teachers', 'about-us', 'privacy-policy', 'data-deletion', 'contact-us', 'support'] as $slug) {
    Route::get('/'.$slug, [PageController::class, 'show'])->defaults('slug', $slug)->name('pages.'.$slug);
}

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('admin')->group(function (): void {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('site-settings', SiteSettingController::class)->except(['show']);
        Route::resource('media-assets', MediaAssetController::class)->except(['show']);
        Route::resource('navigation-items', NavigationItemController::class)->except(['show']);
        Route::resource('footer-items', FooterItemController::class)->except(['show']);
        Route::resource('homepage-sections', HomepageSectionController::class)->except(['show']);
        Route::resource('homepage-sections.items', HomepageSectionItemController::class)->parameters(['items' => 'item'])->except(['show']);
        Route::resource('pages', AdminPageController::class)->except(['show']);
        Route::resource('pages.sections', AdminPageSectionController::class)->parameters(['sections' => 'section'])->except(['show']);
        Route::resource('pages.sections.items', AdminPageSectionItemController::class)->parameters(['sections' => 'section', 'items' => 'item'])->except(['show']);
    });
});
