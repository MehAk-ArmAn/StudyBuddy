#!/bin/bash
set -euo pipefail
PROJECT=/Users/mehakarman/Desktop/StudyBuddy-main
BACKUP=/Users/mehakarman/Desktop/StudyBuddy-main/storage/studybuddy-w14-before-20261117-102307
rm -rf "$PROJECT/studybuddy-reactbits/w13/entry.jsx"
mkdir -p "$(dirname "$PROJECT/studybuddy-reactbits/w13/entry.jsx")"
cp -Rp "$BACKUP/studybuddy-reactbits/w13/entry.jsx" "$PROJECT/studybuddy-reactbits/w13/entry.jsx"
rm -rf "$PROJECT/public/assets/studybuddy-w13"
mkdir -p "$(dirname "$PROJECT/public/assets/studybuddy-w13")"
cp -Rp "$BACKUP/public/assets/studybuddy-w13" "$PROJECT/public/assets/studybuddy-w13"
rm -rf "$PROJECT/public/assets/studybuddy-base44-exact/home.html"
mkdir -p "$(dirname "$PROJECT/public/assets/studybuddy-base44-exact/home.html")"
cp -Rp "$BACKUP/public/assets/studybuddy-base44-exact/home.html" "$PROJECT/public/assets/studybuddy-base44-exact/home.html"
rm -rf "$PROJECT/resources/views/layouts/app.blade.php"
mkdir -p "$(dirname "$PROJECT/resources/views/layouts/app.blade.php")"
cp -Rp "$BACKUP/resources/views/layouts/app.blade.php" "$PROJECT/resources/views/layouts/app.blade.php"
rm -rf "$PROJECT/public/assets/studybuddy-w14"
mkdir -p "$(dirname "$PROJECT/public/assets/studybuddy-w14")"
cp -Rp "$BACKUP/public/assets/studybuddy-w14" "$PROJECT/public/assets/studybuddy-w14"
cd "$PROJECT"
php artisan view:clear >/dev/null 2>&1 || true
echo "W14 removed; W12/W13 restored as before."
