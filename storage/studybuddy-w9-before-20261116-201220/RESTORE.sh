#!/usr/bin/env bash
set -Eeuo pipefail
BACKUP="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT="$(cd "$BACKUP/../.." && pwd)"
while IFS='|' read -r state rel;do
  if [[ "$state" == EXISTS ]];then mkdir -p "$PROJECT/$(dirname "$rel")";cp -p "$BACKUP/original/$rel" "$PROJECT/$rel"
  else rm -f "$PROJECT/$rel";fi
done < "$BACKUP/manifest.tsv"
(cd "$PROJECT" && php artisan view:clear >/dev/null) || true
echo 'Restored the W9 changed files.'
