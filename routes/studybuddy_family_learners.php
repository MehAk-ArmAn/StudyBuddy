<?php

use App\Http\Controllers\FamilyLearnerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('family/learners')->name('studybuddy.family.')->group(function (): void {
    Route::get('/', [FamilyLearnerController::class, 'index'])->name('learners');
    Route::post('/', [FamilyLearnerController::class, 'store'])->name('learners.store')->middleware('throttle:12,1');
    Route::patch('/{member}', [FamilyLearnerController::class, 'update'])->name('learners.update')->whereNumber('member');
    Route::post('/{member}/moments', [FamilyLearnerController::class, 'moment'])->name('learners.moment')->whereNumber('member')->middleware('throttle:12,1');
    Route::post('/{member}/link', [FamilyLearnerController::class, 'link'])->name('learners.link')->whereNumber('member')->middleware('throttle:6,1');
    Route::delete('/{member}', [FamilyLearnerController::class, 'destroy'])->name('learners.destroy')->whereNumber('member');
});
