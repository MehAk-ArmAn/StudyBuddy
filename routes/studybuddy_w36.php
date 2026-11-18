<?php

use Illuminate\Support\Facades\Route;

// StudyBuddy W36 free original React Study Space. Access requires an authenticated account.
Route::middleware('auth')->get('/study-space', static function () {
    return view('studybuddy.w36.study-space');
})->name('studybuddy.study-space');
