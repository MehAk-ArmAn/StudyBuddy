<?php

use App\Http\Controllers\StudyBuddyExperienceController;
use Illuminate\Support\Facades\Route;

Route::get('/learning-hub', [StudyBuddyExperienceController::class, 'learningHub'])->name('studybuddy.learning-hub');
Route::get('/learning-paths', [StudyBuddyExperienceController::class, 'learningPaths'])->name('studybuddy.learning-paths');
Route::get('/rewards', [StudyBuddyExperienceController::class, 'rewards'])->name('studybuddy.rewards');
Route::get('/parents-center', [StudyBuddyExperienceController::class, 'parentsCenter'])->name('studybuddy.parents-center');
Route::get('/teacher-studio', [StudyBuddyExperienceController::class, 'teacherStudio'])->name('studybuddy.teacher-studio');
Route::get('/safety-support', [StudyBuddyExperienceController::class, 'safetySupport'])->name('studybuddy.safety-support');
Route::get('/app-ecosystem', [StudyBuddyExperienceController::class, 'appEcosystem'])->name('studybuddy.app-ecosystem');
