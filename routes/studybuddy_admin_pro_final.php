<?php

use App\Http\Controllers\StudyBuddyAdminAccountController;
use App\Http\Controllers\StudyBuddyAdminRoleToolsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function () {
        Route::get('/account', [StudyBuddyAdminAccountController::class, 'edit'])
            ->name('account.edit');

        Route::patch('/account/profile', [StudyBuddyAdminAccountController::class, 'updateProfile'])
            ->name('account.profile');

        Route::patch('/account/password', [StudyBuddyAdminAccountController::class, 'updatePassword'])
            ->name('account.password');

        Route::get('/role-tools', [StudyBuddyAdminRoleToolsController::class, 'index'])
            ->name('role-tools.index');

        Route::get('/pages-legal', fn () => redirect()->route('admin.pages.index'))
            ->name('pages-legal.index');
    });
