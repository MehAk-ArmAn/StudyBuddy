<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/learning-hub', '/apps?section=learning', 302);
Route::redirect('/learning-paths', '/apps?section=learning', 302);
Route::redirect('/parents-center', '/apps?role=parent', 302);
Route::redirect('/teacher-studio', '/apps?role=teacher', 302);
Route::redirect('/safety-support', '/apps?section=safety', 302);
Route::redirect('/rewards', '/apps?section=rewards', 302);
Route::redirect('/app-ecosystem', '/apps', 302);
Route::redirect('/app-launchpad', '/apps', 302);
Route::redirect('/platform-roadmap', '/apps?section=roadmap', 302);
Route::redirect('/about', '/', 302);
Route::redirect('/contact', '/', 302);
Route::redirect('/privacy-policy', '/', 302);
Route::redirect('/terms', '/', 302);
Route::redirect('/cookie-policy', '/', 302);
Route::redirect('/data-deletion', '/', 302);
