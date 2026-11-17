#!/usr/bin/env python3
import hashlib, json, shutil
from pathlib import Path
bak=Path(__file__).resolve().parent
meta=json.loads((bak/'manifest.json').read_text())
root=Path(meta['root'])
if not (root/'artisan').is_file():raise SystemExit('Laravel project missing; rollback cancelled')
for rel,digest in meta['files'].items():
    out=root/rel
    out.parent.mkdir(parents=True, exist_ok=True)
    shutil.copy2(bak/'files'/rel, out)
    if hashlib.sha256(out.read_bytes()).hexdigest()!=digest:raise SystemExit('Restore checksum failed: '+rel)
out=root/meta['built']
if out.exists():shutil.rmtree(out)
shutil.copytree(bak/'built', out)
print('W20 RESTORED: exact previous W19 config, homepage, layouts, and compiled assets.')
