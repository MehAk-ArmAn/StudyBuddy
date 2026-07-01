<?php

use App\Http\Controllers\StudyBuddyFinalAdminController;
use App\Http\Controllers\StudyBuddyFinalPlatformController;
use Illuminate\Support\Facades\Route;

Route::get('/apps', [StudyBuddyFinalPlatformController::class, 'apps'])->name('pages.apps');
Route::get('/apps/{slug}', [StudyBuddyFinalPlatformController::class, 'appDetail'])
    ->where('slug', '[A-Za-z0-9\-]+')
    ->name('studybuddy.apps.show');

Route::redirect('/app-launchpad', '/apps')->name('studybuddy.final.app-launchpad');
Route::redirect('/app-ecosystem', '/apps')->name('studybuddy.experience.app-ecosystem');
Route::redirect('/launchpad', '/apps');
Route::redirect('/apps-launchpad', '/apps');
Route::redirect('/app-store', '/apps');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/play/{slug}', [StudyBuddyFinalPlatformController::class, 'webPlay'])
        ->where('slug', '[A-Za-z0-9\-]+')
        ->name('studybuddy.final.web-play');

    Route::get('/points-wallet', [StudyBuddyFinalPlatformController::class, 'pointsWallet'])->name('studybuddy.final.points-wallet');
    Route::post('/studybuddy/app-session/complete', [StudyBuddyFinalPlatformController::class, 'completeSession'])
        ->middleware('throttle:10,1')
        ->name('studybuddy.final.session.complete');
});

Route::get('/platform-roadmap', [StudyBuddyFinalPlatformController::class, 'platformRoadmap'])->name('studybuddy.final.roadmap');
Route::get('/launch-readiness', [StudyBuddyFinalPlatformController::class, 'launchReadiness'])->name('studybuddy.final.launch-readiness');

Route::middleware(['admin'])->group(function (): void {
    Route::get('/admin/studybuddy/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('studybuddy.admin.final.index');
    Route::post('/admin/studybuddy/final-platform/settings', [StudyBuddyFinalAdminController::class, 'updateSettings'])->name('studybuddy.admin.final.settings');
    Route::patch('/admin/studybuddy/final-platform/apps/{app}', [StudyBuddyFinalAdminController::class, 'updateApp'])->name('studybuddy.admin.final.apps.update');
    Route::patch('/admin/studybuddy/final-platform/checklist/{item}', [StudyBuddyFinalAdminController::class, 'updateChecklist'])->name('studybuddy.admin.final.checklist.update');
    Route::post('/admin/studybuddy/final-platform/points', [StudyBuddyFinalAdminController::class, 'quickAward'])->name('studybuddy.admin.final.points.award');
});
