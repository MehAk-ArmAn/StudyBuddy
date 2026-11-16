#!/usr/bin/env bash
set -Eeuo pipefail
BACKUP="$(cd "$(dirname "$0")" && pwd)"
PROJECT="$(cd "$BACKUP/../.." && pwd)"
while IFS='|' read -r typ rel;do
  if [[ "$typ" == 'EXISTS' ]];then
    mkdir -p "$PROJECT/$(dirname "$rel")";cp -p "$BACKUP/original/$rel" "$PROJECT/$rel"
  else rm -f "$PROJECT/$rel";fi
done < "$BACKUP/manifest.tsv"
if [[ -d "$BACKUP/old-react-build" ]];then
  rm -rf "$PROJECT/public/build/studybuddy-react"
  cp -R "$BACKUP/old-react-build" "$PROJECT/public/build/studybuddy-react"
else rm -rf "$PROJECT/public/build/studybuddy-react";fi
(cd "$PROJECT" && php artisan view:clear >/dev/null) || true
echo "W8 original changed files and React build restored to $PROJECT"
