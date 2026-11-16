<?php

use App\Http\Controllers\StudyBuddyLogoutController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/logout',
    [StudyBuddyLogoutController::class, 'confirm']
)->name('logout.confirm');

Route::post(
    '/logout',
    [StudyBuddyLogoutController::class, 'destroy']
)->name('logout');

Route::get(
    '/goodbye',
    [StudyBuddyLogoutController::class, 'goodbye']
)->name('logout.goodbye');
