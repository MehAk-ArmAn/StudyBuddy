<?php

use App\Http\Controllers\StudyBuddyAdminHealthController;
use App\Http\Controllers\StudyBuddyAdminHomepageCmsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'admin'])
    ->prefix('admin/control-room')
    ->name('admin.control-room.')
    ->group(function (): void {
        Route::get('/health', [StudyBuddyAdminHealthController::class, 'index'])
            ->name('health');

        Route::get('/homepage-cms', [StudyBuddyAdminHomepageCmsController::class, 'index'])
            ->name('homepage-cms.index');

        Route::patch('/homepage-cms/sections/{section}', [StudyBuddyAdminHomepageCmsController::class, 'updateSection'])
            ->name('homepage-cms.sections.update');

        Route::patch('/homepage-cms/items/{item}', [StudyBuddyAdminHomepageCmsController::class, 'updateItem'])
            ->name('homepage-cms.items.update');
    });
