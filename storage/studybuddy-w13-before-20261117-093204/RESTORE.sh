#!/bin/bash
set -euo pipefail
PROJECT='/Users/mehakarman/Desktop/StudyBuddy-main'
BACKUP='/Users/mehakarman/Desktop/StudyBuddy-main/storage/studybuddy-w13-before-20261117-093204'
rm -rf "$PROJECT/resources/views/layouts/admin.blade.php"
mkdir -p "$(dirname "$PROJECT/resources/views/layouts/admin.blade.php")"
cp -Rp "$BACKUP/resources/views/layouts/admin.blade.php" "$PROJECT/resources/views/layouts/admin.blade.php"
rm -rf "$PROJECT/resources/views/layouts/app.blade.php"
mkdir -p "$(dirname "$PROJECT/resources/views/layouts/app.blade.php")"
cp -Rp "$BACKUP/resources/views/layouts/app.blade.php" "$PROJECT/resources/views/layouts/app.blade.php"
rm -rf "$PROJECT/public/assets/studybuddy-base44-exact/home.html"
mkdir -p "$(dirname "$PROJECT/public/assets/studybuddy-base44-exact/home.html")"
cp -Rp "$BACKUP/public/assets/studybuddy-base44-exact/home.html" "$PROJECT/public/assets/studybuddy-base44-exact/home.html"
rm -rf "$PROJECT/public/assets/studybuddy-w13"
rm -rf "$PROJECT/studybuddy-reactbits/w13"
cd "$PROJECT"
php artisan view:clear >/dev/null 2>&1 || true
echo "W13 rolled back; W12 restored exactly."
