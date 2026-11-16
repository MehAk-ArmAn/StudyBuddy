<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| StudyBuddy Legacy / Missing Route Aliases
|--------------------------------------------------------------------------
| Keeps old dashboard/view references safe after the route cleanup.
| Every route is guarded with Route::has() so this file will not override
| real routes when they already exist.
*/

$sbAlias = function (string $name, string $uri, string $target) {
    if (! Route::has($name)) {
        Route::get($uri, fn () => redirect($target, 302))->name($name);
    }
};

// Old page aliases.
$sbAlias('pages.apps', '/apps-catalog', '/apps');
$sbAlias('pages.contact-us', '/contact-us', '/');
$sbAlias('pages.support', '/support', '/apps?section=safety');

// Phase 5 experience aliases used by dashboard partials.
$sbAlias('studybuddy.experience.learning-hub', '/learning-hub', '/apps?section=learning');
$sbAlias('studybuddy.experience.learning-paths', '/learning-paths', '/apps?section=learning');
$sbAlias('studybuddy.experience.rewards', '/rewards', '/apps?section=rewards');
$sbAlias('studybuddy.experience.app-ecosystem', '/app-ecosystem', '/apps');
$sbAlias('studybuddy.experience.safety-support', '/safety-support', '/apps?section=safety');
$sbAlias('studybuddy.experience.parents-center', '/parents-center', '/apps?role=parent');
$sbAlias('studybuddy.experience.teacher-studio', '/teacher-studio', '/apps?role=teacher');

// Phase 6/final aliases used by old dashboards and app pages.
$sbAlias('studybuddy.final.points-wallet', '/points-wallet', '/apps?section=rewards');
$sbAlias('studybuddy.final.roadmap', '/platform-roadmap', '/apps?section=roadmap');
$sbAlias('studybuddy.final.launch-readiness', '/launch-readiness', '/admin/control-room/final-platform');

if (! Route::has('studybuddy.final.web-play')) {
    Route::get('/web-play/{app?}', function (?string $app = null) {
        return redirect($app ? '/apps/' . $app : '/apps', 302);
    })->name('studybuddy.final.web-play');
}

if (! Route::has('studybuddy.final.session.complete')) {
    Route::post('/web-play/{app?}/complete', function (?string $app = null) {
        return redirect($app ? '/apps/' . $app : '/apps', 302);
    })->name('studybuddy.final.session.complete');
}
