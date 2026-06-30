<?php

use App\Http\Controllers\DashboardThemeController;
use App\Http\Controllers\SavedQuestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/my-quest', [SavedQuestController::class, 'index'])
        ->name('studybuddy.quests.index');

    Route::post('/my-quest', [SavedQuestController::class, 'store'])
        ->name('studybuddy.quests.store');

    Route::patch('/my-quest/{savedQuest}', [SavedQuestController::class, 'update'])
        ->name('studybuddy.quests.update');

    Route::delete('/my-quest/{savedQuest}', [SavedQuestController::class, 'destroy'])
        ->name('studybuddy.quests.destroy');

    Route::post('/dashboard/theme', [DashboardThemeController::class, 'update'])
        ->name('studybuddy.dashboard.theme.update');
});
