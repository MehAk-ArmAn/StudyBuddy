<?php

use App\Http\Controllers\StudyBuddyW34SubjectsController;
use Illuminate\Support\Facades\Route;

Route::get('/subjects', [StudyBuddyW34SubjectsController::class, 'index'])->name('studybuddy.w34.subjects.index');
Route::get('/subjects/{slug}', [StudyBuddyW34SubjectsController::class, 'show'])->name('studybuddy.w34.subjects.show');
Route::middleware(['auth', 'admin'])->prefix('admin/control-room/apps/{app}/subjects')
    ->name('studybuddy.w34.admin.')->group(function () {
        Route::get('/', [StudyBuddyW34SubjectsController::class, 'edit'])->name('edit');
        Route::post('/', [StudyBuddyW34SubjectsController::class, 'update'])->name('update');
    });
