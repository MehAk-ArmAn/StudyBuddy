#!/usr/bin/env python3
from pathlib import Path
import json,shutil,hashlib
b=Path(__file__).resolve().parent;m=json.loads((b/'manifest.json').read_text());r=Path(m['root'])
if not (r/'artisan').is_file():raise SystemExit('Laravel root missing, no rollback performed')
for path,sha in m['files'].items():
 f=r/path;f.parent.mkdir(parents=True,exist_ok=True);shutil.copy2(b/'originals'/path,f)
 assert hashlib.sha256(f.read_bytes()).hexdigest()==sha,path
for path,was in m['dirs'].items():
 f=r/path
 if f.exists():shutil.rmtree(f)
 if was:shutil.copytree(b/'originals'/path,f)
print('W25 rollback completed; previous home, layout, source and generated assets restored')
