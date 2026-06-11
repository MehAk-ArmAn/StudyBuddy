<?php

use App\Http\Controllers\AdminResourceController;
use App\Http\Controllers\AppPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/showcase', [HomeController::class, 'showcase'])->name('showcase');
Route::redirect('/for-parents', '/parent/dashboard')->name('for-parents');
Route::redirect('/for-teachers', '/teacher/dashboard')->name('for-teachers');
Route::redirect('/pricing', '/rewards')->name('pricing');
Route::redirect('/support', '/showcase')->name('support');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'loginSubmit']);
Route::post('/register', [AuthController::class, 'registerSubmit']);
Route::post('/admin/login', [AuthController::class, 'adminLoginSubmit']);

Route::get('/apps', [AppPageController::class, 'index'])->name('apps.index');
Route::get('/apps/{app}', [AppPageController::class, 'show'])->name('apps.show');
Route::get('/apps/{app}/play', [AppPageController::class, 'play'])->name('apps.play');
Route::get('/rewards', RewardController::class)->name('rewards');

Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
Route::get('/student/apps', [DashboardController::class, 'studentApps'])->name('student.apps');
Route::get('/student/rewards', [DashboardController::class, 'studentRewards'])->name('student.rewards');
Route::get('/parent/dashboard', [DashboardController::class, 'parent'])->name('parent.dashboard');
Route::get('/teacher/dashboard', [DashboardController::class, 'teacher'])->name('teacher.dashboard');

Route::get('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login');
Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
Route::get('/admin/resources/{resource}', [AdminResourceController::class, 'index'])->name('admin.resources.index');

Route::get('/demo/primary', [DashboardController::class, 'student'])->name('demo.primary');
Route::get('/demo/secondary', [DashboardController::class, 'secondary'])->name('demo.secondary');
Route::get('/demo/parent', [DashboardController::class, 'parent'])->name('demo.parent');
Route::get('/demo/teacher', [DashboardController::class, 'teacher'])->name('demo.teacher');
Route::get('/demo/admin', [DashboardController::class, 'admin'])->name('demo.admin');
