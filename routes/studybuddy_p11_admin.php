<?php

use App\Http\Controllers\StudyBuddyP11TeamController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin/control-room')->name('admin.control-room.')->group(function () {
    Route::get('/team', [StudyBuddyP11TeamController::class, 'index'])->name('team.index');
    Route::post('/team', [StudyBuddyP11TeamController::class, 'create'])->middleware('throttle:5,60')->name('team.create');
    Route::put('/team/{user}', [StudyBuddyP11TeamController::class, 'update'])->middleware('throttle:15,60')->name('team.update');
    Route::delete('/team/{user}', [StudyBuddyP11TeamController::class, 'revoke'])->middleware('throttle:8,60')->name('team.revoke');
});
