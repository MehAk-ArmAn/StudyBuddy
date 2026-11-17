#!/bin/bash
set -euo pipefail
PROJECT='/Users/mehakarman/Desktop/StudyBuddy-main'
BACKUP='/Users/mehakarman/Desktop/StudyBuddy-main/storage/studybuddy-w12-exact-before-20261117-091048'
rm -rf "$PROJECT/resources/views/home.blade.php"
mkdir -p "$(dirname "$PROJECT/resources/views/home.blade.php")"
cp -Rp "$BACKUP/resources/views/home.blade.php" "$PROJECT/resources/views/home.blade.php"
rm -rf "$PROJECT/public/assets/studybuddy-base44-exact"
cd "$PROJECT"
php artisan view:clear >/dev/null 2>&1 || true
echo "Restored previous StudyBuddy homepage and assets."
