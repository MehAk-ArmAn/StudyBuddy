#!/usr/bin/env python3
from pathlib import Path
import json,shutil,hashlib
backup=Path(__file__).resolve().parent; meta=json.loads((backup/'manifest.json').read_text());root=Path(meta['root'])
if not (root/'artisan').is_file():raise SystemExit('Laravel root missing; rollback cancelled')
for rel,digest in meta['files'].items():
 p=root/rel;p.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(backup/'originals'/rel,p)
 assert hashlib.sha256(p.read_bytes()).hexdigest()==digest,rel
for rel,existed in meta['dirs'].items():
 p=root/rel
 if p.exists():shutil.rmtree(p)
 if existed:shutil.copytree(backup/'originals'/rel,p)
print('W24 rollback complete: previous Base44 and Laravel nav restored, W24 source/assets restored to prior state.')
