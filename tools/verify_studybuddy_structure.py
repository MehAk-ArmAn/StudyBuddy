#!/usr/bin/env python3
from pathlib import Path
import re
import subprocess

ROOT = Path.cwd()

def run(cmd):
    try:
        out = subprocess.check_output(cmd, cwd=ROOT, stderr=subprocess.STDOUT, text=True, timeout=45)
        return 0, out
    except subprocess.CalledProcessError as e:
        return e.returncode, e.output
    except Exception as e:
        return 1, str(e)

print('StudyBuddy structure verification')
print('=' * 40)
for path in ['routes/web.php', 'routes/studybuddy.php', 'public/assets/studybuddy-imgs/brand/logo-icon.png']:
    print(('✓' if (ROOT/path).exists() else '✗'), path)

rc, out = run(['php', 'artisan', 'route:list'])
print('\nroute:list:', 'OK' if rc == 0 else 'FAILED')
if rc != 0:
    print(out)

bad = []
for base in ['resources', 'app', 'routes']:
    b = ROOT/base
    if not b.exists():
        continue
    for p in b.rglob('*'):
        if p.suffix not in ['.php', '.blade.php']:
            continue
        text = p.read_text(errors='ignore')
        for pat in ["route('pages.apps'", 'route("pages.apps"', 'studybuddy_phase', '<<<<<<<', '>>>>>>>']:
            if pat in text:
                bad.append((str(p.relative_to(ROOT)), pat))

print('\nPossible leftovers:')
if bad:
    for p, pat in bad[:100]:
        print('!', p, 'contains', pat)
else:
    print('✓ none found')

junk = []
for pat in ['studybuddy_*_patch', 'studybuddy_*_patch.zip', '_*_backup_*']:
    junk.extend(ROOT.glob(pat))
print('\nRoot junk:')
if junk:
    for item in junk[:100]:
        print('!', item.name)
else:
    print('✓ none found')
