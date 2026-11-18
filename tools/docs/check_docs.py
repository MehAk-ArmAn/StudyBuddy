#!/usr/bin/env python3
"""Checks the teammate documentation exists and flags common staleness hazards."""
from pathlib import Path
import re,sys
ROOT=Path(__file__).resolve().parents[2]
needed=['DEVELOPERS_START_HERE.md','AGENTS.md','CONTRIBUTING.md','SECURITY.md','docs/handbook/ARCHITECTURE.md','docs/handbook/FEATURE_MAP.md','docs/handbook/TESTING.md','docs/handbook/SECURITY_BOUNDARIES.md','docs/handbook/REWARDS.md','docs/handbook/SCHOOL_OS.md','docs/handbook/generated/REPO_MAP.md','docs/handbook/generated/ROUTE_INVENTORY.md','docs/handbook/generated/DATA_INVENTORY.md','docs/handbook/generated/CODE_FILES.md']
missing=[p for p in needed if not (ROOT/p).is_file()]
print('Documentation file checks:',len(needed)-len(missing),'/',len(needed))
for m in missing:print('ERROR: missing',m)
warn=[]
claude=ROOT/'CLAUDE.md'
if claude.is_file() and (ROOT/'package.json').is_file() and re.search(r'(?:no|without)\s+`?package\.json`?',claude.read_text(errors='replace'),re.I):
    warn.append('CLAUDE.md says no package.json, but one exists; older notes may be stale')
if (ROOT/'storage/framework/testing').exists():
    warn.append('Do not stage files under storage/framework/testing; tests may generate artifacts there')
for w in warn:print('NOTE:',w)
if missing:sys.exit(1)
print('Documentation structure PASS. Route inventory may still need local PHP verification.')
