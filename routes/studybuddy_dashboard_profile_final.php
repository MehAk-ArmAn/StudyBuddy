<?php

use App\Http\Controllers\StudyBuddyProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/community', [StudyBuddyProfileController::class, 'community'])
        ->name('studybuddy.community');

    Route::get('/u/{user}', [StudyBuddyProfileController::class, 'show'])
        ->whereNumber('user')
        ->name('studybuddy.profile.public');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/profile', [StudyBuddyProfileController::class, 'edit'])
        ->name('profile');

    Route::patch('/profile', [StudyBuddyProfileController::class, 'update'])
        ->name('profile.update');
});
