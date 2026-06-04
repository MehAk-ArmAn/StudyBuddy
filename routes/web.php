<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RewardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/showcase', [HomeController::class, 'showcase'])->name('showcase');
Route::get('/apps', [AppController::class, 'index'])->name('apps.index');
Route::get('/apps/math-quest', [AppController::class, 'mathQuest'])->name('apps.math-quest');
Route::get('/apps/math-quest/play', [AppController::class, 'playMathQuest'])->name('apps.math-quest.play');
Route::get('/demo/primary', [DemoController::class, 'primary'])->name('demo.primary');
Route::get('/demo/secondary', [DemoController::class, 'secondary'])->name('demo.secondary');
Route::get('/demo/parent', [DemoController::class, 'parent'])->name('demo.parent');
Route::get('/demo/teacher', [DemoController::class, 'teacher'])->name('demo.teacher');
Route::get('/rewards', RewardController::class)->name('rewards');
Route::get('/demo/admin', [DemoController::class, 'admin'])->name('demo.admin');
