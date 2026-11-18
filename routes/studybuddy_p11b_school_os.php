<?php

use App\Http\Controllers\Admin\StudyBuddyP11BSchoolOversightController;
use App\Http\Controllers\StudyBuddySchoolBoardController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('schools/{organization}')->name('schools.')->group(function (): void {
    Route::get('/board', [StudyBuddySchoolBoardController::class, 'index'])->name('board');
    Route::post('/board/tasks', [StudyBuddySchoolBoardController::class, 'store'])->middleware('throttle:20,1')->name('board.tasks.store');
    Route::put('/board/tasks/{task}', [StudyBuddySchoolBoardController::class, 'update'])->middleware('throttle:40,1')->name('board.tasks.update');
    Route::delete('/board/tasks/{task}', [StudyBuddySchoolBoardController::class, 'archive'])->middleware('throttle:15,1')->name('board.tasks.archive');
});
Route::middleware(['auth', 'admin'])->get('/admin/control-room/schools/{organization}/overview',
    [StudyBuddyP11BSchoolOversightController::class, 'show'])->name('admin.control-room.schools.overview');
