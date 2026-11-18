<?php

use App\Http\Controllers\StudyBuddySchoolMessagingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('schools/{organization}/connect')->name('schools.connect.')->group(function (): void {
    Route::get('/', [StudyBuddySchoolMessagingController::class, 'index'])->name('index');
    Route::post('/channels', [StudyBuddySchoolMessagingController::class, 'createChannel'])->middleware('throttle:6,60')->name('channels.create');
    Route::post('/directs', [StudyBuddySchoolMessagingController::class, 'startDirect'])->middleware('throttle:12,60')->name('directs.start');
    Route::get('/reports', [StudyBuddySchoolMessagingController::class, 'reports'])->name('reports');
    Route::put('/reports/{report}', [StudyBuddySchoolMessagingController::class, 'reviewReport'])->middleware('throttle:20,1')->name('reports.review');
    Route::get('/{conversation}', [StudyBuddySchoolMessagingController::class, 'show'])->name('show');
    Route::post('/{conversation}/messages', [StudyBuddySchoolMessagingController::class, 'send'])->middleware('throttle:20,1')->name('messages.send');
    Route::post('/{conversation}/read', [StudyBuddySchoolMessagingController::class, 'markRead'])->middleware('throttle:40,1')->name('read');
    Route::get('/{conversation}/attachments/{message}', [StudyBuddySchoolMessagingController::class, 'download'])->name('attachments.download');
    Route::post('/{conversation}/messages/{message}/reports', [StudyBuddySchoolMessagingController::class, 'report'])->middleware('throttle:8,60')->name('messages.report');
});
