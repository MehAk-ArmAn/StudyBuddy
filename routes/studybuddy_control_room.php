<?php

use App\Http\Controllers\StudyBuddyControlRoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/control-room', [StudyBuddyControlRoomController::class, 'index'])->name('control-room');
});
