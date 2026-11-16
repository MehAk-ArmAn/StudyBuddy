#!/usr/bin/env python3
"""Merge React Bits Pro registries into an EXISTING shadcn config only. No key written."""
from pathlib import Path
import json,sys
project=Path(sys.argv[1]).resolve()
file=project/'components.json'
if not file.exists():
 print('Pro: components.json is absent. No automatic shadcn init on a Laravel site; see docs.');sys.exit(0)
try:data=json.loads(file.read_text())
except Exception as e: raise SystemExit('Pro registry skipped: existing components.json is malformed: '+str(e))
registries=data.setdefault('registries',{})
for key,endpoint in [('@reactbits-starter','starter'),('@reactbits-pro','pro')]:
 registries[key]={'url':f'https://pro.reactbits.dev/api/r/{endpoint}/{{name}}.json','headers':{'Authorization':'Bearer ${REACTBITS_LICENSE_KEY}'}}
file.write_text(json.dumps(data,indent=2)+'\n')
print('Pro: registry URLs and environment-variable header merged (no key stored).')
