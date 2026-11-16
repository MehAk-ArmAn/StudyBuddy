<?php

use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\StudyBuddyAdminContentController;
use App\Http\Controllers\StudyBuddyControlRoomController;
use App\Http\Controllers\StudyBuddyFinalAdminController;
use App\Http\Controllers\StudyBuddyShellAdminController;
use App\Http\Controllers\StudyBuddyVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function () {
        Route::get('/', [StudyBuddyControlRoomController::class, 'index'])->name('index');

        // Website shell: real edit page + save.
        Route::get('/shell', [StudyBuddyShellAdminController::class, 'index'])->name('shell');
        Route::post('/shell', [StudyBuddyShellAdminController::class, 'update'])->name('shell.update');

        // Content studio: real edit page + save endpoints.
        Route::get('/content-studio', [StudyBuddyAdminContentController::class, 'index'])->name('content-studio');
        Route::patch('/content-studio/pages/{page}', [StudyBuddyAdminContentController::class, 'updatePage'])->name('content.pages.update');
        Route::patch('/content-studio/items/{item}', [StudyBuddyAdminContentController::class, 'updateItem'])->name('content.items.update');
        Route::patch('/content-studio/apps/{app}', [StudyBuddyAdminContentController::class, 'updateApp'])->name('content.apps.update');

        // Apps/platform cockpit: real edit page + save endpoints.
        Route::get('/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('final-platform');
        Route::post('/final-platform/settings', [StudyBuddyFinalAdminController::class, 'updateSettings'])->name('final.settings');
        Route::patch('/final-platform/apps/{app}', [StudyBuddyFinalAdminController::class, 'updateApp'])->name('final.apps.update');
        Route::patch('/final-platform/checklist/{item}', [StudyBuddyFinalAdminController::class, 'updateChecklist'])->name('final.checklist.update');
        Route::post('/final-platform/points', [StudyBuddyFinalAdminController::class, 'quickAward'])->name('final.points.award');

        // Users/resources inside Control Room.
        Route::resource('/users', AdminUserController::class)->except(['show'])->names('users');
        Route::resource('/site-settings', SiteSettingController::class)->except(['show'])->names('site-settings');

        // Verification review inside Control Room.
        Route::get('/verifications', [StudyBuddyVerificationController::class, 'adminIndex'])->name('verifications.index');
        Route::patch('/verifications/{case}', [StudyBuddyVerificationController::class, 'adminUpdate'])->name('verifications.update');
    });
