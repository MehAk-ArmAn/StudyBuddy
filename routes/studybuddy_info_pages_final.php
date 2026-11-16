<?php

use App\Http\Controllers\StudyBuddyInfoPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/roles', [StudyBuddyInfoPageController::class, 'roles'])
        ->name('studybuddy.roles');

    foreach ([
        'about' => 'about',
        'privacy-policy' => 'privacy-policy',
        'terms' => 'terms',
        'disclaimer' => 'disclaimer',
        'cookies' => 'cookies',
        'community-guidelines' => 'community-guidelines',
        'copyright' => 'copyright',
        'data-deletion' => 'data-deletion',
    ] as $uri => $slug) {
        Route::get('/'.$uri, [StudyBuddyInfoPageController::class, 'show'])
            ->defaults('slug', $slug)
            ->name('studybuddy.info.'.$slug);
    }
});
