#!/bin/bash
set -euo pipefail
python3 - "$0" <<'PYSCRIPT'
import json,pathlib,shutil,sys
backup=pathlib.Path(sys.argv[1]).resolve().parent
root=backup.parent.parent
manifest=json.loads((backup/'backup-manifest.json').read_text())
for relative in manifest['files']:
    dest=root/relative
    if relative in manifest['existing']:
        original=backup/relative
        if not original.is_file():raise SystemExit('Backup missing: '+relative)
        dest.parent.mkdir(parents=True,exist_ok=True)
        shutil.copy2(original,dest)
    else:
        dest.unlink(missing_ok=True)
print('W16 original files restored from',backup)
PYSCRIPT
cd "$(dirname "$(dirname "$(dirname "$0")")")"
php artisan view:clear >/dev/null || true
