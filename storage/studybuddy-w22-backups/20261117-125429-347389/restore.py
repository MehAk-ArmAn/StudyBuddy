#!/usr/bin/env python3
import json,hashlib,shutil
from pathlib import Path
backup=Path(__file__).resolve().parent
m=json.loads((backup/'manifest.json').read_text())
root=Path(m['root'])
if not (root/'artisan').exists():raise SystemExit('Original Laravel root missing; cannot restore')
for name, item in m['files'].items():
    dest=root/name
    if item['exists']:
        dest.parent.mkdir(parents=True,exist_ok=True)
        shutil.copy2(backup/'originals'/name,dest)
        assert hashlib.sha256(dest.read_bytes()).hexdigest()==item['sha256'],name
    elif dest.is_file():dest.unlink()
print('ROLLBACK PASS: StudyBuddy W22 files and HTML restored byte-for-byte; prior visuals untouched.')
