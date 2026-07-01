<?php
use App\Http\Controllers\StudyBuddyCommandCenterController;use Illuminate\Support\Facades\Route;Route::middleware(['auth','verified'])->group(function(){Route::get('/command-center',StudyBuddyCommandCenterController::class)->name('studybuddy.command-center');Route::get('/dashboard/command-center',StudyBuddyCommandCenterController::class)->name('studybuddy.dashboard.command-center');});
