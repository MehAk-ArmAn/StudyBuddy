<?php

use App\Http\Controllers\Admin\StudyBuddyP11FRewardsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth','admin'])->prefix('admin/control-room/rewards')
    ->name('admin.control-room.rewards.')->group(function (): void {
        Route::get('/', [StudyBuddyP11FRewardsController::class,'index'])->name('index');
        Route::put('/settings', [StudyBuddyP11FRewardsController::class,'updateSettings'])->middleware('throttle:12,1')->name('settings');
        Route::put('/rules/{rule}', [StudyBuddyP11FRewardsController::class,'updateRule'])->middleware('throttle:12,1')->whereNumber('rule')->name('rules.update');
    });
