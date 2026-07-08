<?php

use App\Http\Controllers\AccountConnectionController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardThemeController;
use App\Http\Controllers\SavedQuestController;
use App\Http\Controllers\StudyBuddyAdminContentController;
use App\Http\Controllers\StudyBuddyCommandCenterController;
use App\Http\Controllers\StudyBuddyControlRoomBridgeController;
use App\Http\Controllers\StudyBuddyControlRoomController;
use App\Http\Controllers\StudyBuddyExperienceController;
use App\Http\Controllers\StudyBuddyFinalAdminController;
use App\Http\Controllers\StudyBuddyFinalPlatformController;
use App\Http\Controllers\StudyBuddyShellAdminController;
use App\Http\Controllers\StudyBuddyVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public app universe
|--------------------------------------------------------------------------
*/
Route::get('/apps', [StudyBuddyFinalPlatformController::class, 'apps'])->name('studybuddy.apps');
Route::get('/apps-catalog', [StudyBuddyFinalPlatformController::class, 'apps'])->name('pages.apps');
Route::get('/apps/{slug}', [StudyBuddyFinalPlatformController::class, 'appDetail'])->name('studybuddy.apps.show');
Route::get('/play/{slug}', [StudyBuddyFinalPlatformController::class, 'webPlay'])->name('studybuddy.final.web-play');
Route::get('/web-play/{app?}', [StudyBuddyFinalPlatformController::class, 'webPlay'])->name('studybuddy.final.web-play.legacy');
Route::post('/web-play/{app?}/complete', [StudyBuddyFinalPlatformController::class, 'completeSession'])->name('studybuddy.final.session.complete.legacy');

Route::redirect('/app-launchpad', '/apps', 301)->name('studybuddy.final.app-launchpad');
Route::redirect('/app-ecosystem', '/apps', 301)->name('studybuddy.experience.app-ecosystem');

/*
|--------------------------------------------------------------------------
| Public experience pages
|--------------------------------------------------------------------------
*/
Route::get('/learning-hub', [StudyBuddyExperienceController::class, 'learningHub'])->name('studybuddy.experience.learning-hub');
Route::get('/learning-paths', [StudyBuddyExperienceController::class, 'learningPaths'])->name('studybuddy.experience.learning-paths');
Route::get('/rewards', [StudyBuddyExperienceController::class, 'rewards'])->name('studybuddy.experience.rewards');
Route::get('/parents-center', [StudyBuddyExperienceController::class, 'parentsCenter'])->name('studybuddy.experience.parents-center');
Route::get('/teacher-studio', [StudyBuddyExperienceController::class, 'teacherStudio'])->name('studybuddy.experience.teacher-studio');
Route::get('/safety-support', [StudyBuddyExperienceController::class, 'safetySupport'])->name('studybuddy.experience.safety-support');
Route::get('/platform-roadmap', [StudyBuddyFinalPlatformController::class, 'platformRoadmap'])->name('studybuddy.final.roadmap');
Route::get('/launch-readiness', [StudyBuddyFinalPlatformController::class, 'launchReadiness'])->name('studybuddy.final.launch-readiness');

// Friendly compatibility redirects for old footer/nav URLs.
Route::redirect('/about', '/', 302);
Route::redirect('/contact', '/contact-us', 302);
Route::redirect('/terms', '/privacy-policy', 302);
Route::redirect('/cookie-policy', '/privacy-policy', 302);
Route::redirect('/data-deletion', '/data-deletion', 302);

/*
|--------------------------------------------------------------------------
| Authenticated learner tools
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function (): void {
    Route::get('/points-wallet', [StudyBuddyFinalPlatformController::class, 'pointsWallet'])->name('studybuddy.final.points-wallet');
    Route::post('/studybuddy/app-session/complete', [StudyBuddyFinalPlatformController::class, 'completeSession'])->name('studybuddy.final.session.complete');

    Route::get('/my-quest', [SavedQuestController::class, 'index'])->name('studybuddy.quests.index');
    Route::post('/my-quest', [SavedQuestController::class, 'store'])->name('studybuddy.quests.store');
    Route::patch('/my-quest/{savedQuest}', [SavedQuestController::class, 'update'])->name('studybuddy.quests.update');
    Route::delete('/my-quest/{savedQuest}', [SavedQuestController::class, 'destroy'])->name('studybuddy.quests.destroy');

    Route::post('/dashboard/theme', [DashboardThemeController::class, 'update'])->name('studybuddy.dashboard.theme.update');
    Route::get('/command-center', StudyBuddyCommandCenterController::class)->name('studybuddy.command-center');
    Route::get('/dashboard/command-center', StudyBuddyCommandCenterController::class)->name('studybuddy.dashboard.command-center');

    Route::get('/verification-center', [StudyBuddyVerificationController::class, 'show'])->name('studybuddy.verification.center');
    Route::post('/verification-center', [StudyBuddyVerificationController::class, 'submit'])->middleware('throttle:4,10')->name('studybuddy.verification.submit');

    Route::post('/connections/parent/request', [AccountConnectionController::class, 'requestParentConnection'])->name('studybuddy.connections.parent.request');
    Route::post('/connections/teacher/request', [AccountConnectionController::class, 'requestTeacherConnection'])->name('studybuddy.connections.teacher.request');
    Route::patch('/connections/{connection}/approve', [AccountConnectionController::class, 'approve'])->name('studybuddy.connections.approve');
    Route::patch('/connections/{connection}/reject', [AccountConnectionController::class, 'reject'])->name('studybuddy.connections.reject');
    Route::patch('/connections/{connection}/revoke', [AccountConnectionController::class, 'revoke'])->name('studybuddy.connections.revoke');
});

/*
|--------------------------------------------------------------------------
| Admin control room
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function (): void {
        Route::get('/', [StudyBuddyControlRoomController::class, 'index'])->name('index');

        Route::get('/shell', [StudyBuddyShellAdminController::class, 'index'])->name('shell');
        Route::post('/shell', [StudyBuddyShellAdminController::class, 'update'])->name('shell.update');

        Route::get('/content-studio', [StudyBuddyAdminContentController::class, 'index'])->name('content-studio');
        Route::patch('/content-studio/pages/{page}', [StudyBuddyAdminContentController::class, 'updatePage'])->name('content.pages.update');
        Route::patch('/content-studio/items/{item}', [StudyBuddyAdminContentController::class, 'updateItem'])->name('content.items.update');
        Route::patch('/content-studio/apps/{app}', [StudyBuddyAdminContentController::class, 'updateApp'])->name('content.apps.update');

        Route::get('/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('final-platform');
        Route::post('/final-platform/settings', [StudyBuddyFinalAdminController::class, 'updateSettings'])->name('final.settings');
        Route::patch('/final-platform/apps/{app}', [StudyBuddyFinalAdminController::class, 'updateApp'])->name('final.apps.update');
        Route::patch('/final-platform/checklist/{item}', [StudyBuddyFinalAdminController::class, 'updateChecklist'])->name('final.checklist.update');
        Route::post('/final-platform/points', [StudyBuddyFinalAdminController::class, 'quickAward'])->name('final.points.award');

        Route::resource('users', AdminUserController::class)->except(['show']);

        Route::get('/verifications', [StudyBuddyVerificationController::class, 'adminIndex'])->name('verifications.index');
        Route::patch('/verifications/{case}', [StudyBuddyVerificationController::class, 'adminUpdate'])->name('verifications.update');

        Route::resource('site-settings', SiteSettingController::class)->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| Legacy admin route names
|--------------------------------------------------------------------------
| Kept so older Blade forms keep working while all real UI links point to
| /admin/control-room/...
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin/studybuddy')
    ->name('studybuddy.admin.')
    ->group(function (): void {
        Route::get('/shell', [StudyBuddyShellAdminController::class, 'index'])->name('shell.index');
        Route::post('/shell', [StudyBuddyShellAdminController::class, 'update'])->name('shell.update');

        Route::get('/content-studio', [StudyBuddyAdminContentController::class, 'index'])->name('content.index');
        Route::patch('/content-pages/{page}', [StudyBuddyAdminContentController::class, 'updatePage'])->name('content.pages.update');
        Route::patch('/content-items/{item}', [StudyBuddyAdminContentController::class, 'updateItem'])->name('content.items.update');
        Route::patch('/apps/{app}', [StudyBuddyAdminContentController::class, 'updateApp'])->name('content.apps.update');

        Route::get('/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('final.index');
        Route::post('/final-platform/settings', [StudyBuddyFinalAdminController::class, 'updateSettings'])->name('final.settings');
        Route::patch('/final-platform/apps/{app}', [StudyBuddyFinalAdminController::class, 'updateApp'])->name('final.apps.update');
        Route::patch('/final-platform/checklist/{item}', [StudyBuddyFinalAdminController::class, 'updateChecklist'])->name('final.checklist.update');
        Route::post('/final-platform/points', [StudyBuddyFinalAdminController::class, 'quickAward'])->name('final.points.award');

        Route::get('/verifications', [StudyBuddyVerificationController::class, 'adminIndex'])->name('verifications.index');
        Route::patch('/verifications/{case}', [StudyBuddyVerificationController::class, 'adminUpdate'])->name('verifications.update');
    });
