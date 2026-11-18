<?php

use App\Http\Controllers\StudyBuddyDailyPulseController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/my-day', [StudyBuddyDailyPulseController::class, 'show'])->name('studybuddy.p09.pulse');
    Route::post('/my-day/plans', [StudyBuddyDailyPulseController::class, 'addPlan'])->name('studybuddy.p09.plans.store');
    Route::delete('/my-day/plans/{id}', [StudyBuddyDailyPulseController::class, 'deletePlan'])->name('studybuddy.p09.plans.destroy');
    Route::post('/my-day/notifications/read', [StudyBuddyDailyPulseController::class, 'markRead'])->name('studybuddy.p09.notifications.read');
    Route::put('/my-day/notifications/preferences', [StudyBuddyDailyPulseController::class, 'preferences'])->name('studybuddy.p09.preferences.update');
});
