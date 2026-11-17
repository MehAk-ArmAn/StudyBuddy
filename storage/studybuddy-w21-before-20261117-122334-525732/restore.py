#!/usr/bin/env python3
from pathlib import Path
import json,shutil,hashlib
bak=Path(__file__).resolve().parent
m=json.loads((bak/'manifest.json').read_text());root=Path(m['root'])
if not (root/'artisan').exists():raise SystemExit('Laravel root missing; refusing rollback')
for rel,info in m['files'].items():
    f=root/rel
    if info['exists']:
        f.parent.mkdir(parents=True,exist_ok=True)
        shutil.copy2(bak/'originals'/rel,f)
        assert hashlib.sha256(f.read_bytes()).hexdigest()==info['sha256'],rel
    else:
        if f.exists():f.unlink()
print('W21 rolled back: original homepage, CSS and JS restored byte-for-byte.')
