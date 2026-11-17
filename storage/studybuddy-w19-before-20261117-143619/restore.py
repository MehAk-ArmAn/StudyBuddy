#!/usr/bin/env python3
from pathlib import Path
import json, shutil
here=Path(__file__).resolve().parent
m=json.loads((here/'manifest.json').read_text())
root=Path(m['project'])
if not (root/'artisan').is_file():raise SystemExit('Laravel root missing; rollback stopped')
for rel,existed in m['files'].items():
  p=root/rel
  if existed:
    p.parent.mkdir(parents=True,exist_ok=True)
    shutil.copy2(here/'files'/rel,p)
  elif p.is_file():p.unlink()
for rel,existed in m['trees'].items():
  p=root/rel
  if p.is_dir():shutil.rmtree(p)
  elif p.exists():p.unlink()
  if existed:shutil.copytree(here/'trees'/rel,p)
print('W19 RESTORED: exact prior homepage, layouts and original W19 assets (if any).')
