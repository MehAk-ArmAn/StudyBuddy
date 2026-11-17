<?php

use App\Http\Controllers\StudyBuddyConnectedProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('learning/connected')->name('studybuddy.connected.')->group(function (): void {
    Route::get('/insights', [StudyBuddyConnectedProgressController::class, 'insights'])->name('insights');
    Route::post('/start', [StudyBuddyConnectedProgressController::class, 'start'])->middleware('throttle:12,1')->name('start');
    Route::post('/{session}/heartbeat', [StudyBuddyConnectedProgressController::class, 'heartbeat'])->middleware('throttle:12,1')->whereNumber('session')->name('heartbeat');
    Route::post('/{session}/finish', [StudyBuddyConnectedProgressController::class, 'finish'])->middleware('throttle:12,1')->whereNumber('session')->name('finish');
    Route::post('/{session}/leave', [StudyBuddyConnectedProgressController::class, 'leave'])->middleware('throttle:12,1')->whereNumber('session')->name('leave');
});
