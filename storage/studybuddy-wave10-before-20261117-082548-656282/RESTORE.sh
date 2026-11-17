#!/usr/bin/env bash
set -euo pipefail
ROOT='/Users/mehakarman/Desktop/StudyBuddy-main'
cp "$ROOT/storage/studybuddy-wave10-before-20261117-082548-656282/resources/views/dashboard/index.blade.php" "$ROOT/resources/views/dashboard/index.blade.php"
cp "$ROOT/storage/studybuddy-wave10-before-20261117-082548-656282/resources/views/studybuddy/final/apps.blade.php" "$ROOT/resources/views/studybuddy/final/apps.blade.php"
rm -f "$ROOT/resources/views/dashboard/partials/studybuddy-wave10-quick-actions.blade.php"
rm -f "$ROOT/public/assets/css/studybuddy-wave10-features.css"
cd "$ROOT"
if [ -f vendor/autoload.php ]; then php artisan view:clear; fi
