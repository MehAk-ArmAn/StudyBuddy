<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('studybuddy:hello', function () {
    $this->comment('StudyBuddy cosmic learning universe is ready to launch.');
})->purpose('Display a StudyBuddy readiness message');
