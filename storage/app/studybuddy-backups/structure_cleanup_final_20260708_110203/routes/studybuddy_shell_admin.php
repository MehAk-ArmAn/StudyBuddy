<?php

use App\Http\Controllers\StudyBuddyShellAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])
    ->prefix('admin/studybuddy')
    ->name('studybuddy.admin.')
    ->group(function () {
        Route::get('/shell', [StudyBuddyShellAdminController::class, 'index'])->name('shell.index');
        Route::post('/shell', [StudyBuddyShellAdminController::class, 'update'])->name('shell.update');
    });
