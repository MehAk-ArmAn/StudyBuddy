<?php

use App\Http\Controllers\StudyBuddyMailingListController;
use Illuminate\Support\Facades\Route;

Route::post(
    '/updates/subscribe',
    [StudyBuddyMailingListController::class, 'subscribe']
)
    ->middleware('throttle:10,1')
    ->name('newsletter.subscribe');

Route::prefix('admin/control-room/mailing-list')
    ->middleware('auth')
    ->name('admin.control-room.mailing-list.')
    ->group(function (): void {
        Route::get(
            '/',
            [StudyBuddyMailingListController::class, 'adminIndex']
        )->name('index');

        Route::get(
            '/export',
            [StudyBuddyMailingListController::class, 'adminExport']
        )->name('export');

        Route::patch(
            '/{subscriber}',
            [StudyBuddyMailingListController::class, 'adminUpdate']
        )->name('update');

        Route::delete(
            '/{subscriber}',
            [StudyBuddyMailingListController::class, 'adminDestroy']
        )->name('destroy');
    });
