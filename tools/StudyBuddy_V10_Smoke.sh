#!/usr/bin/env bash
set -euo pipefail
BASE="${1:-http://127.0.0.1:8000}"
for route in / /apps /roles /for-schools; do
 code="$(curl --max-time 12 -s -o /dev/null -w '%{http_code}' "$BASE$route")"
 printf '%-18s HTTP %s\n' "$route" "$code"
 [[ "$code" == 200 || "$code" == 302 ]] || { echo 'Check this route before continuing.' >&2; exit 1; }
done
for asset in /assets/studybuddy-reactbits/reactbits.js /assets/studybuddy-reactbits/reactbits.css /assets/css/studybuddy-v10-public.css /assets/css/studybuddy-v10-1-play.css; do
 code="$(curl --max-time 12 -s -o /dev/null -w '%{http_code}' "$BASE$asset")"
 printf '%-55s HTTP %s\n' "$asset" "$code"
 [[ "$code" == 200 ]] || exit 1
done
echo 'HTTP smoke checks pass. Still manually test keyboard, both themes, CMS/search, and signed-in school/admin permissions.'
