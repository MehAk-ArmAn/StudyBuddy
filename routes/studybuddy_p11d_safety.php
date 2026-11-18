<?php

use App\Http\Controllers\StudyBuddySchoolSafetyCenterController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('schools/{organization}/safety')->name('schools.safety.')->group(function (): void {
    Route::get('/', [StudyBuddySchoolSafetyCenterController::class, 'index'])->name('index');
    Route::post('/notices', [StudyBuddySchoolSafetyCenterController::class, 'postNotice'])->middleware('throttle:8,1')->name('notices.post');
    Route::post('/notices/{notice}/read', [StudyBuddySchoolSafetyCenterController::class, 'markRead'])->middleware('throttle:30,1')->name('notices.read');
    Route::post('/notices/{notice}/archive', [StudyBuddySchoolSafetyCenterController::class, 'archiveNotice'])->middleware('throttle:10,1')->name('notices.archive');
    Route::post('/preferences', [StudyBuddySchoolSafetyCenterController::class, 'updatePreference'])->middleware('throttle:10,1')->name('preferences');
    Route::post('/reports', [StudyBuddySchoolSafetyCenterController::class, 'reportIssue'])->middleware('throttle:5,1')->name('reports.submit');
    Route::put('/reports/{report}', [StudyBuddySchoolSafetyCenterController::class, 'reviewIssue'])->middleware('throttle:10,1')->name('reports.review');
    Route::get('/export/notices', [StudyBuddySchoolSafetyCenterController::class, 'exportNotices'])->middleware('throttle:3,1')->name('export.notices');
});
