<?php

use App\Http\Controllers\Admin\SchoolOrganizationReviewController;
use App\Http\Controllers\SchoolOrganizationController;
use Illuminate\Support\Facades\Route;

Route::get('/for-schools', [SchoolOrganizationController::class, 'info'])->name('schools.info');
Route::middleware(['auth'])->group(function () {
    Route::get('/schools/apply', [SchoolOrganizationController::class, 'apply'])->name('schools.apply');
    Route::post('/schools/apply', [SchoolOrganizationController::class, 'store'])->middleware('throttle:2,60')->name('schools.store');
    Route::get('/schools/{organization}/workspace', [SchoolOrganizationController::class, 'workspace'])->name('schools.workspace');
    Route::post('/schools/{organization}/invitations', [SchoolOrganizationController::class, 'invite'])->middleware('throttle:6,60')->name('schools.invitations.store');
    Route::delete('/schools/{organization}/invitations/{invitation}', [SchoolOrganizationController::class, 'revokeInvitation'])->name('schools.invitations.revoke');
    Route::put('/schools/{organization}/members/{member}/role', [SchoolOrganizationController::class, 'changeStaffRole'])->middleware('throttle:10,1')->name('schools.members.role');
    Route::delete('/schools/{organization}/members/{member}', [SchoolOrganizationController::class, 'removeMember'])->name('schools.members.revoke');
    Route::get('/schools/invitations/{token}', [SchoolOrganizationController::class, 'invitation'])->name('schools.invitations.show');
    Route::post('/schools/invitations/{token}/accept', [SchoolOrganizationController::class, 'accept'])->middleware('throttle:10,60')->name('schools.invitations.accept');
});
Route::middleware(['auth', 'admin'])->prefix('admin/control-room')->name('admin.control-room.')->group(function () {
    Route::get('/schools', [SchoolOrganizationReviewController::class, 'index'])->name('schools.index');
    Route::post('/schools/{organization}/review', [SchoolOrganizationReviewController::class, 'update'])->middleware('throttle:20,60')->name('schools.review');
});
