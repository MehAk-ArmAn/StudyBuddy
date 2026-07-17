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


// Route modules that must be registered once before the public fallbacks.
require __DIR__ . '/studybuddy_contact_inbox_final.php';
require __DIR__ . '/studybuddy_admin_health_cms.php';

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

foreach (['for-parents', 'for-teachers', 'about-us', 'privacy-policy', 'data-deletion', 'support'] as $slug) {
    Route::get('/'.$slug, [PageController::class, 'show'])
        ->defaults('slug', $slug)
        ->name('pages.'.$slug);
}

/*
|--------------------------------------------------------------------------
| Public authentication + dashboard
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [UserAccessController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserAccessController::class, 'login'])->name('login.store');
    Route::get('/register', [UserAccessController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserAccessController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::put('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');

    // Email verification is disabled, but stale Laravel/Breeze URLs stay safe.
    Route::get('/email/verify', [UserAccessController::class, 'verificationNotice'])->name('verification.notice');
    Route::post('/email/verification-notification', [UserAccessController::class, 'resendVerification'])->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', fn () => redirect()->route('dashboard')->with('status', 'Email verification is disabled for StudyBuddy.'))
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Legacy admin resources
|--------------------------------------------------------------------------
| Kept for compatibility. The preferred admin home is /admin/control-room.
*/
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

/*
|--------------------------------------------------------------------------
| StudyBuddy modules
|--------------------------------------------------------------------------
| All StudyBuddy-specific routes live in one organized file.
*/
require __DIR__.'/studybuddy.php';

// Final StudyBuddy dashboard/profile routes
require __DIR__ . '/studybuddy_dashboard_profile_final.php';

// Final StudyBuddy search routes
require __DIR__ . '/studybuddy_search_final.php';

// Final StudyBuddy info/legal/roles pages
require __DIR__ . '/studybuddy_info_pages_final.php';

// StudyBuddy role dashboard tools
require __DIR__ . '/studybuddy_role_dashboard_tools.php';

// Final StudyBuddy professional admin tools
require __DIR__ . '/studybuddy_admin_pro_final.php';

// The contact form and admin inbox are registered once near the top of this file.

require __DIR__ . '/studybuddy_logout.php';
