#!/usr/bin/env python3
"""Read-only repo introspection except for the four generated handbook files.

No network, DB queries, user records, private files, or credentials.
Run from anywhere: python3 tools/docs/refresh_repo_maps.py
"""
from __future__ import annotations
import json
import re
import subprocess
from collections import Counter
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT / 'docs' / 'handbook' / 'generated'
PRIVATE_MARKERS = ('.env', '.private.', '.sql', '/storage/', '/vendor/', '/node_modules/', '.sqlite', 'secret', '/.git/')

def safe(s: object) -> str:
    return str(s).replace('|', r'\|').replace('`', "'").replace('\n',' ').replace('\r',' ')

def okpath(path: Path) -> bool:
    if not path.is_file() or path.is_symlink(): return False
    rel = '/'+str(path.relative_to(ROOT)).replace('\\','/')
    return not any(x in rel.lower() for x in PRIVATE_MARKERS)

def files_in(*dirs: str, suffixes=('.php',)) -> list[Path]:
    arr=[]
    for d in dirs:
        base=ROOT/d
        if base.exists():
            arr.extend(p for p in base.rglob('*') if p.suffix in suffixes and okpath(p))
    return sorted(set(arr))

def rel(p: Path) -> str: return p.relative_to(ROOT).as_posix()

def out(name: str, title: str, content: str):
    OUT.mkdir(parents=True, exist_ok=True)
    (OUT/name).write_text(f'# {title}\n\n> Automatically generated from the **current local source tree**. Do not edit by hand: run `python3 tools/docs/refresh_repo_maps.py`. No user data, credentials or database rows are read.\n\n'+content, encoding='utf-8')

def git_head():
    try:
        p=subprocess.run(['git','-C',str(ROOT),'rev-parse','--short','HEAD'],capture_output=True,text=True,timeout=8)
        return p.stdout.strip() if p.returncode == 0 else 'unavailable'
    except Exception: return 'unavailable'

def repo_map():
    folders=['app/Http/Controllers','app/Http/Middleware','app/Http/Requests','app/Models','app/Services','app/Support','routes','database/migrations','database/seeders','resources/views','public/assets','tests/Feature','tests/Unit','config','tools']
    rows=[]
    for d in folders:
        base=ROOT/d
        count=sum(1 for p in base.rglob('*') if okpath(p)) if base.is_dir() else 0
        rows.append(f'| `{d}/` | {count} files (filtered) |')
    body=f'**Git HEAD at map generation:** `{safe(git_head())}` (not necessarily the final docs commit).\n\n## Directory index\n\n| Path | Count |\n| --- | ---: |\n'+'\n'.join(rows)
    body+='\n\n## Important files present\n\n'
    keys=['composer.json','package.json','phpunit.xml','CLAUDE.md','AGENTS.md','routes/web.php','app/Support/StudyBuddyRewardIntegrity.php','app/Http/Controllers/StudyBuddyMathSprintController.php','app/Http/Controllers/StudyBuddySchoolMessagingController.php','app/Http/Controllers/StudyBuddySchoolSafetyCenterController.php','resources/views/layouts/app.blade.php','resources/views/layouts/admin.blade.php']
    for k in keys:body+=f'- `{k}`: '+('present' if (ROOT/k).is_file() else '**not present**')+'\n'
    body+='\n**Caution:** Presence is not proof that a feature is deployed. Review permissions, tests and runtime.\n'
    out('REPO_MAP.md','Repository map',body)

def code_index():
    arr=files_in('app','routes','database/migrations','database/seeders','tests',suffixes=('.php',))
    rows=[]
    for p in arr:
        t=p.read_text(encoding='utf-8',errors='replace')
        # Class+method names are helpful for locating code, but do not claim the class is stable API.
        cls=re.search(r'\b(?:class|trait|interface|enum)\s+([A-Za-z_][A-Za-z0-9_]*)\b',t)
        methods=re.findall(r'\bpublic\s+(?:static\s+)?function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(',t)
        summary='class '+cls.group(1) if cls else ('route registrations' if rel(p).startswith('routes/') else 'PHP file')
        if methods:summary+='; public methods: '+', '.join(methods[:7])+('…' if len(methods)>7 else '')
        rows.append('| `'+safe(rel(p))+'` | '+safe(summary)+' |')
    out('CODE_FILES.md','PHP code and test file index', f'**Indexed files:** {len(arr)}. Method lists are names only, not behavioral guarantees.\n\n| File | Class / entrypoints |\n| --- | --- |\n'+'\n'.join(rows)+'\n')

def data_index():
    models=files_in('app/Models')
    migrations=files_in('database/migrations')
    rows=[]
    for p in migrations:
        s=p.read_text(encoding='utf-8',errors='replace')
        names=sorted(set(re.findall(r"Schema::(?:create|table|dropIfExists)\s*\(\s*['\"]([^'\"]+)",s)))
        rows.append('| `'+safe(rel(p))+'` | '+(', '.join('`'+safe(x)+'`' for x in names) if names else 'see migration source')+' |')
    txt=f'**Models:** {len(models)} · **Migration files:** {len(migrations)}. The following is **source inventory**, not current DB schema state.\n\n## Models\n\n'
    txt+='\n'.join('- `'+safe(rel(x))+'`' for x in models)
    txt+='\n\n## Migration files and referenced tables\n\n| Migration | Table names matched in code |\n| --- | --- |\n'+'\n'.join(rows)+'\n\nRun `php artisan migrate:status` to confirm the actual target database. Never infer live user data from migration files.\n'
    out('DATA_INVENTORY.md','Data models and migrations',txt)

def route_index():
    command=['php','artisan','route:list','--json']
    routes=None; reason=''
    if (ROOT/'artisan').is_file():
        try:
            cp=subprocess.run(command,cwd=ROOT,stdout=subprocess.PIPE,stderr=subprocess.PIPE,text=True,timeout=35)
            if cp.returncode==0: routes=json.loads(cp.stdout)
            else: reason='Laravel route command exited non-zero; inspect PHP/Laravel availability locally.'
        except (FileNotFoundError, subprocess.TimeoutExpired,ValueError): reason='Laravel route JSON was unavailable; try `php artisan route:list` manually.'
    else: reason='artisan missing from this checkout.'
    if isinstance(routes,list):
        rows=[]
        for r in routes:
            method=safe(r.get('method',''))
            uri=safe(r.get('uri',''))
            name=safe(r.get('name') or '—')
            action=safe(r.get('action',''))
            middleware=r.get('middleware',[])
            if isinstance(middleware,list): middleware=', '.join(map(str,middleware))
            rows.append(f'| `{method}` | `{uri}` | `{name}` | `{action}` | {safe(middleware)} |')
        out('ROUTE_INVENTORY.md','Registered routes',f'**Actual Laravel route registrations:** {len(routes)}. This command examines routes; it does not assert each UI page is operational.\n\n| Method | URI | Name | Action | Middleware |\n| --- | --- | --- | --- | --- |\n'+'\n'.join(rows)+'\n')
    else:
        routefiles=files_in('routes')
        out('ROUTE_INVENTORY.md','Route inventory (requires local Laravel run)',f'**Not verified**: {reason}\n\nRun `php artisan route:list --json` after Composer dependencies are installed. These are the route source files (not a substitute for registered route list):\n\n'+'\n'.join('- `'+safe(rel(p))+'`' for p in routefiles)+'\n')

if __name__=='__main__':
    if not (ROOT/'composer.json').is_file():
        raise SystemExit('STOP: run this file inside a StudyBuddy Laravel checkout')
    repo_map(); code_index(); data_index(); route_index()
    print('Generated 4 repository inventories in', OUT)
