<?php

use App\Http\Controllers\StudyBuddyFinalAdminController;
use App\Http\Controllers\StudyBuddyFinalPlatformController;
use Illuminate\Support\Facades\Route;

Route::get('/platform-roadmap', [StudyBuddyFinalPlatformController::class, 'platformRoadmap'])->name('studybuddy.final.roadmap');
Route::get('/launch-readiness', [StudyBuddyFinalPlatformController::class, 'launchReadiness'])->name('studybuddy.final.launch-readiness');

Route::middleware(['auth'])->group(function () {
    Route::get('/points-wallet', [StudyBuddyFinalPlatformController::class, 'pointsWallet'])->name('studybuddy.final.points-wallet');
    Route::post('/studybuddy/app-session/complete', [StudyBuddyFinalPlatformController::class, 'completeSession'])->name('studybuddy.final.session.complete');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/studybuddy/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('studybuddy.admin.final.index');
    Route::post('/admin/studybuddy/final-platform/settings', [StudyBuddyFinalAdminController::class, 'updateSettings'])->name('studybuddy.admin.final.settings');
    Route::patch('/admin/studybuddy/final-platform/apps/{app}', [StudyBuddyFinalAdminController::class, 'updateApp'])->name('studybuddy.admin.final.apps.update');
    Route::patch('/admin/studybuddy/final-platform/checklist/{item}', [StudyBuddyFinalAdminController::class, 'updateChecklist'])->name('studybuddy.admin.final.checklist.update');
    Route::post('/admin/studybuddy/final-platform/points', [StudyBuddyFinalAdminController::class, 'quickAward'])->name('studybuddy.admin.final.points.award');
});
