<?php

use App\Http\Controllers\StudyBuddyW33ShowcaseController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin/control-room/apps/{app}')
    ->name('studybuddy.w33.admin.')->group(function (): void {
        Route::get('/showcase', [StudyBuddyW33ShowcaseController::class, 'edit'])->name('edit');
        Route::post('/showcase', [StudyBuddyW33ShowcaseController::class, 'update'])->name('update');
        Route::post('/showcase/screenshots', [StudyBuddyW33ShowcaseController::class, 'uploadScreenshots'])->name('screenshots.upload');
        Route::post('/showcase/screenshots/{shot}', [StudyBuddyW33ShowcaseController::class, 'updateScreenshot'])->name('screenshots.update');
        Route::delete('/showcase/screenshots/{shot}', [StudyBuddyW33ShowcaseController::class, 'deleteScreenshot'])->name('screenshots.destroy');
        Route::post('/showcase/desktop/{platform}', [StudyBuddyW33ShowcaseController::class, 'uploadDesktop'])->name('desktop.upload');
        Route::delete('/showcase/desktop/{platform}', [StudyBuddyW33ShowcaseController::class, 'deleteDesktop'])->name('desktop.destroy');
        Route::get('/showcase/desktop-kit', [StudyBuddyW33ShowcaseController::class, 'desktopKit'])->name('desktop.kit');
        Route::get('/showcase/screenshots/{shot}/image', [StudyBuddyW33ShowcaseController::class, 'adminImage'])->name('screenshots.image');
    });

Route::get('/apps/{slug}/showcase/screenshots/{shot}', [StudyBuddyW33ShowcaseController::class, 'publicImage'])
    ->name('studybuddy.w33.screenshot');
Route::get('/apps/{slug}/desktop/{platform}', [StudyBuddyW33ShowcaseController::class, 'downloadDesktop'])
    ->name('studybuddy.w33.desktop.download');
Route::get('/apps/{slug}/install-desktop', [StudyBuddyW33ShowcaseController::class, 'installDesktop'])
    ->name('studybuddy.w33.desktop.install');
