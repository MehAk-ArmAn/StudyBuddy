<?php

use App\Http\Controllers\StudyBuddyOnboardingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get('/getting-started', [StudyBuddyOnboardingController::class, 'show'])
        ->name('studybuddy.onboarding.show');
    Route::post('/getting-started', [StudyBuddyOnboardingController::class, 'save'])
        ->middleware('throttle:25,1')
        ->name('studybuddy.onboarding.save');
});
