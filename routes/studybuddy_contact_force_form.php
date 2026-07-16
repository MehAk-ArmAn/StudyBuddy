<?php

use App\Http\Controllers\StudyBuddyContactController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/contact', [StudyBuddyContactController::class, 'show'])
        ->name('studybuddy.contact.show');

    Route::post('/contact', [StudyBuddyContactController::class, 'store'])
        ->name('studybuddy.contact.store');

    Route::redirect('/contact-us', '/contact', 302);
});
