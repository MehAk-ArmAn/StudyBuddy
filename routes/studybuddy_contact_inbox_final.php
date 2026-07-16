<?php

use App\Http\Controllers\StudyBuddyAdminContactMessageController;
use App\Http\Controllers\StudyBuddyContactController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/contact', [StudyBuddyContactController::class, 'show'])
        ->name('studybuddy.contact.show');

    Route::post('/contact', [StudyBuddyContactController::class, 'store'])
        ->name('studybuddy.contact.store');

    Route::redirect('/contact-us', '/contact', 302);
});

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function () {
        Route::get('/messages', [StudyBuddyAdminContactMessageController::class, 'index'])
            ->name('contact-messages.index');

        Route::get('/messages/{message}', [StudyBuddyAdminContactMessageController::class, 'show'])
            ->whereNumber('message')
            ->name('contact-messages.show');

        Route::patch('/messages/{message}', [StudyBuddyAdminContactMessageController::class, 'update'])
            ->whereNumber('message')
            ->name('contact-messages.update');
    });
