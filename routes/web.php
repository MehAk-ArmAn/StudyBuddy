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
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordRecoveryController;
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
    Route::get('/forgot-password', [PasswordRecoveryController::class, 'forgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordRecoveryController::class, 'send'])->middleware('throttle:3,10')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordRecoveryController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordRecoveryController::class, 'reset'])->middleware('throttle:5,10')->name('password.update');
    Route::post('/register', [UserAccessController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::put('/dashboard/profile', [DashboardController::class, 'updateProfile'])->name('dashboard.profile.update');
    Route::put('/dashboard/password', [DashboardController::class, 'updatePassword'])->name('dashboard.password.update');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])->middleware('throttle:6,60')->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});


/*
|--------------------------------------------------------------------------
| Legacy admin resources
|--------------------------------------------------------------------------
| Kept for compatibility. The preferred admin home is /admin/control-room.
*/
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:12,5')->name('login.store');
    Route::get('/two-factor', [LoginController::class, 'showTwoFactor'])->name('two-factor.show');
    Route::post('/two-factor', [LoginController::class, 'verifyTwoFactor'])->middleware('throttle:8,10')->name('two-factor.verify');
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

// STUDYBUDDY W33 SHOWCASE + DESKTOP
require __DIR__ . '/studybuddy_w33.php';

// W34 academic subjects: classified apps from the existing CMS, no second catalogue.
require __DIR__ . '/studybuddy_w34.php';

// Final StudyBuddy dashboard/profile routes
require __DIR__ . '/studybuddy_dashboard_profile_final.php';

// P08 optional, private onboarding on existing role_profile (no new tables).
require __DIR__ . '/studybuddy_p08_onboarding.php';

// P09 account-private calendar, notifications and recorded analytics.
require __DIR__ . '/studybuddy_p09_pulse.php';

// P10 account-private documents and uploaded study resources.
require __DIR__ . '/studybuddy_p10_resources.php';

// P11 scoped PCL administration.
require __DIR__ . '/studybuddy_p11_admin.php';

// P11B private School OS tasks and scoped organization oversight.
require __DIR__ . '/studybuddy_p11b_school_os.php';

// P11C safeguarded school staff channels and direct conversations.
require __DIR__ . '/studybuddy_p11c_messaging.php';

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

require __DIR__ . '/studybuddy_mailing_list.php';

// School tenancy and approval are strictly separate from learner accounts.
require __DIR__.'/studybuddy_schools.php';

// STUDYBUDDY W24 FAMILY ROUTES
require __DIR__ . '/studybuddy_family_learners.php';


/* STUDYBUDDY_W30_ACTIVITY_ROUTES */
require __DIR__.'/studybuddy_learning_activity.php';


/* STUDYBUDDY_W31_CONNECTED_PROGRESS */
require __DIR__.'/studybuddy_connected_progress.php';


/* STUDYBUDDY_W32_MATH_SPRINT */
require __DIR__.'/studybuddy_w32_math_sprint.php';

/* STUDYBUDDY_W36_REACT_STUDY_SPACE */
require __DIR__.'/studybuddy_w36.php';
