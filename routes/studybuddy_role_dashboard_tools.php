<?php

use App\Http\Controllers\StudyBuddyRoleDashboardActionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/dashboard/learner/connect-code', [StudyBuddyRoleDashboardActionController::class, 'regenerateConnectCode'])
        ->name('studybuddy.learner.connect-code.regenerate');

    Route::post('/dashboard/parent/children', [StudyBuddyRoleDashboardActionController::class, 'addChild'])
        ->name('studybuddy.parent.children.store');

    Route::delete('/dashboard/parent/children/{member}', [StudyBuddyRoleDashboardActionController::class, 'removeChild'])
        ->whereNumber('member')
        ->name('studybuddy.parent.children.destroy');

    Route::post('/dashboard/teacher/organization', [StudyBuddyRoleDashboardActionController::class, 'updateTeacherOrganization'])
        ->name('studybuddy.teacher.organization.update');

    Route::post('/dashboard/teacher/classes', [StudyBuddyRoleDashboardActionController::class, 'createClass'])
        ->name('studybuddy.teacher.classes.store');

    Route::post('/dashboard/teacher/classes/{group}/students', [StudyBuddyRoleDashboardActionController::class, 'addStudent'])
        ->whereNumber('group')
        ->name('studybuddy.teacher.students.store');

    Route::post('/dashboard/teacher/assignments', [StudyBuddyRoleDashboardActionController::class, 'createAssignment'])
        ->name('studybuddy.teacher.assignments.store');
});
