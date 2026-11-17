#!/usr/bin/env bash
set -euo pipefail
ROOT='/Users/mehakarman/Desktop/StudyBuddy-main'
cp "$ROOT/storage/studybuddy-wave10-before-20261117-082533-059321/resources/views/home.blade.php" "$ROOT/resources/views/home.blade.php"
rm -f "$ROOT/resources/views/partials/studybuddy-wave10-landing.blade.php"
rm -f "$ROOT/public/assets/css/studybuddy-wave10.css"
rm -f "$ROOT/public/assets/js/studybuddy-wave10.js"
cd "$ROOT"
if [ -f vendor/autoload.php ]; then php artisan view:clear; fi
