#!/usr/bin/env bash
set -euo pipefail
ROOT='/Users/mehakarman/Desktop/StudyBuddy-main'
cp "$ROOT/storage/studybuddy-wave11-before-20261117-084727-347086/resources/views/home.blade.php" "$ROOT/resources/views/home.blade.php"
rm -f "$ROOT/resources/views/partials/studybuddy-wave11-landing.blade.php"
rm -f "$ROOT/public/assets/css/studybuddy-wave11.css"
rm -f "$ROOT/public/assets/js/studybuddy-wave11.js"
cd "$ROOT"
if [ -f vendor/autoload.php ]; then php artisan view:clear; fi
echo "RESTORED: W11 files rolled back. Your existing Laravel app is unchanged."
