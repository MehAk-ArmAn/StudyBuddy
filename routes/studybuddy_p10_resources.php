<?php

use App\Http\Controllers\StudyBuddyResourceStudioController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:60,1'])->prefix('resources')->name('studybuddy.p10.')->group(function (): void {
    Route::get('/', [StudyBuddyResourceStudioController::class, 'index'])->name('index');
    Route::post('/notes', [StudyBuddyResourceStudioController::class, 'createNote'])->name('notes.create');
    Route::put('/notes/{id}', [StudyBuddyResourceStudioController::class, 'updateNote'])->whereUuid('id')->name('notes.update');
    Route::delete('/notes/{id}', [StudyBuddyResourceStudioController::class, 'deleteNote'])->whereUuid('id')->name('notes.delete');
    Route::post('/files', [StudyBuddyResourceStudioController::class, 'upload'])->name('files.upload');
    Route::get('/files/{id}/download', [StudyBuddyResourceStudioController::class, 'download'])->whereUuid('id')->name('files.download');
    Route::get('/files/{id}/preview', [StudyBuddyResourceStudioController::class, 'preview'])->whereUuid('id')->name('files.preview');
    Route::delete('/files/{id}', [StudyBuddyResourceStudioController::class, 'deleteFile'])->whereUuid('id')->name('files.delete');
});
