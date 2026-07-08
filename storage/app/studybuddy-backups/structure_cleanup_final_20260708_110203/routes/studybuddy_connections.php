<?php

use App\Http\Controllers\AccountConnectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::post('/connections/parent/request', [AccountConnectionController::class, 'requestParentConnection'])->name('studybuddy.connections.parent.request');
    Route::post('/connections/teacher/request', [AccountConnectionController::class, 'requestTeacherConnection'])->name('studybuddy.connections.teacher.request');
    Route::patch('/connections/{connection}/approve', [AccountConnectionController::class, 'approve'])->name('studybuddy.connections.approve');
    Route::patch('/connections/{connection}/reject', [AccountConnectionController::class, 'reject'])->name('studybuddy.connections.reject');
    Route::patch('/connections/{connection}/revoke', [AccountConnectionController::class, 'revoke'])->name('studybuddy.connections.revoke');
});
