<?php

use App\Http\Controllers\StudyBuddyMathSprintController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('learning/math-sprint')->name('studybuddy.w32.')->group(function (): void {
    Route::get('/', [StudyBuddyMathSprintController::class, 'index'])->name('index');
    Route::post('/start', [StudyBuddyMathSprintController::class, 'start'])->middleware('throttle:12,1')->name('start');
    Route::post('/{run}/submit', [StudyBuddyMathSprintController::class, 'submit'])->middleware('throttle:20,1')->whereNumber('run')->name('submit');
    Route::get('/results/{run}', [StudyBuddyMathSprintController::class, 'result'])->whereNumber('run')->name('result');
});
