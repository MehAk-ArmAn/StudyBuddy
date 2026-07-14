<?php

use App\Http\Controllers\StudyBuddySearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/search', [StudyBuddySearchController::class, 'index'])
        ->name('studybuddy.search');

    Route::get('/search/suggest', [StudyBuddySearchController::class, 'suggest'])
        ->name('studybuddy.search.suggest');
});
