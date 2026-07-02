<?php

use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\StudyBuddyControlRoomBridgeController;
use App\Http\Controllers\StudyBuddyControlRoomController;
use App\Http\Controllers\StudyBuddyFinalAdminController;
use App\Http\Controllers\StudyBuddyShellAdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function () {
        Route::get('/', [StudyBuddyControlRoomController::class, 'index'])->name('index');

        Route::get('/shell', [StudyBuddyShellAdminController::class, 'index'])->name('shell');
        Route::post('/shell', [StudyBuddyShellAdminController::class, 'update'])->name('shell.update');

        Route::get('/final-platform', [StudyBuddyFinalAdminController::class, 'index'])->name('final-platform');

        Route::get('/content-studio', [StudyBuddyControlRoomBridgeController::class, 'contentStudio'])->name('content-studio');
        Route::get('/users', [StudyBuddyControlRoomBridgeController::class, 'users'])->name('users');
        Route::get('/verifications', [StudyBuddyControlRoomBridgeController::class, 'verifications'])->name('verifications');

        Route::get('/site-settings', [SiteSettingController::class, 'index'])->name('site-settings.index');
        Route::get('/site-settings/create', [SiteSettingController::class, 'create'])->name('site-settings.create');
        Route::post('/site-settings', [SiteSettingController::class, 'store'])->name('site-settings.store');
        Route::get('/site-settings/{siteSetting}/edit', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('/site-settings/{siteSetting}', [SiteSettingController::class, 'update'])->name('site-settings.update');
        Route::delete('/site-settings/{siteSetting}', [SiteSettingController::class, 'destroy'])->name('site-settings.destroy');
    });
