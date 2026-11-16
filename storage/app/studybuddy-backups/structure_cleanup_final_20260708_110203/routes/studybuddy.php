<?php

/*
|--------------------------------------------------------------------------
| StudyBuddy Feature Route Registry
|--------------------------------------------------------------------------
| This is the only StudyBuddy include used by web.php. Individual feature
| files remain separate for sanity, but web.php stays clean.
*/

foreach ([
    'studybuddy_apps_unified.php',
    'studybuddy_phase3.php',
    'studybuddy_phase4.php',
    'studybuddy_phase5_admin_experience.php',
    'studybuddy_phase6_final.php',
    'studybuddy_connections.php',
    'studybuddy_verification.php',
    'studybuddy_shell_admin.php',
    'studybuddy_shell_redirects.php',
    'studybuddy_control_room_links.php',
] as $routeFile) {
    if (file_exists(__DIR__.'/'.$routeFile)) {
        require __DIR__.'/'.$routeFile;
    }
}
