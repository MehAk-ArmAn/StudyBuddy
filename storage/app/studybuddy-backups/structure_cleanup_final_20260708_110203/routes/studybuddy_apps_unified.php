<?php

use App\Http\Controllers\StudyBuddyFinalPlatformController;
use Illuminate\Support\Facades\Route;

Route::get('/apps', [StudyBuddyFinalPlatformController::class, 'apps'])->name('studybuddy.apps');
Route::get('/apps-catalog', [StudyBuddyFinalPlatformController::class, 'apps'])->name('pages.apps');
Route::get('/apps/{slug}', [StudyBuddyFinalPlatformController::class, 'appDetail'])->name('studybuddy.apps.show');
Route::redirect('/app-launchpad', '/apps', 301)->name('studybuddy.final.app-launchpad');
Route::redirect('/app-ecosystem', '/apps', 301)->name('studybuddy.experience.app-ecosystem');
Route::get('/play/{slug}', [StudyBuddyFinalPlatformController::class, 'webPlay'])->name('studybuddy.final.web-play');
