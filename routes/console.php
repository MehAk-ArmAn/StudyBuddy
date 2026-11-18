<?php

use Illuminate\Support\Facades\Artisan;

require __DIR__.'/studybuddy_p11_console.php';

Artisan::command('studybuddy:hello', function () {
    $this->comment('StudyBuddy cosmic learning universe is ready to launch.');
})->purpose('Display a StudyBuddy readiness message');
