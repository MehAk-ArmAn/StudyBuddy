#!/usr/bin/env bash
set -u

cd /Users/mehakarman/Desktop/StudyBuddy || exit 1

pages=(
"/"
"/apps"
"/roles"
"/community"
"/search?q=math"
"/about"
"/contact"
"/privacy-policy"
"/terms"
"/disclaimer"
"/cookies"
"/community-guidelines"
"/copyright"
"/data-deletion"
)

echo "Rendered content audit"
echo "======================"

for page in "${pages[@]}"; do
  safe=$(echo "$page" | tr '/?=&' '____')
  code=$(curl -L --max-redirs 5 --max-time 15 -s -o "/tmp/studybuddy-rendered-$safe.html" -w "%{http_code}" "http://127.0.0.1:8000$page")
  echo "$code $page"
done

echo ""
echo "Trace scan:"
grep -E -i "Lorem ipsum|dummy|placeholder content|placeholder copy|template content|template copy|uuuu|test 1 test" /tmp/studybuddy-rendered-*.html \
  && exit 1

echo "✅ No visible template traces found in rendered public pages."
