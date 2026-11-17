<?php

use App\Http\Controllers\StudyBuddyLearningActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('learning/activity')->name('studybuddy.learning.')->group(function (): void {
    Route::get('/', [StudyBuddyLearningActivityController::class, 'index'])->name('activity');
    Route::post('/start', [StudyBuddyLearningActivityController::class, 'start'])->middleware('throttle:12,1')->name('start');
    Route::post('/{session}/finish', [StudyBuddyLearningActivityController::class, 'finish'])->middleware('throttle:20,1')->whereNumber('session')->name('finish');
    Route::post('/{session}/leave', [StudyBuddyLearningActivityController::class, 'leave'])->whereNumber('session')->name('leave');
    Route::delete('/{session}', [StudyBuddyLearningActivityController::class, 'destroy'])->whereNumber('session')->name('destroy');
});
