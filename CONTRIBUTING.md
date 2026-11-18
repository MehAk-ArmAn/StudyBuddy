# Contributing to StudyBuddy

Start with [Developer handover](DEVELOPERS_START_HERE.md), [module map](docs/handbook/FEATURE_MAP.md), [security boundaries](docs/handbook/SECURITY_BOUNDARIES.md), and [release checklist](docs/handbook/RELEASE_CHECKLIST.md).

## Working agreement

- Scope one logical change per commit; describe **what changed, why, how verified**, and any migration/rollback implications.
- The primary maintainer currently works directly on `main`; other colleagues should coordinate their changes first. Prefer review before merging high-risk features. **Never force-push** or rewrite shared history.
- Stage **only related code/docs**. `git add .` has previously captured a PHPUnit-generated PDF under `storage/framework/testing/`—check the staged file list every time.
- Add/upate a Feature or Unit test for behavior changes, plus manual browser checks for UX.
- New or changed routes require a link from the relevant role's UI and a backend permission check.
- Use existing CSS and semantic tokens before adding one-off overrides; don't delete historical CSS assets blindly.
- Add comments for tenant isolation, ledger invariants, abuse limits, migration safety and unusual workarounds. Avoid obvious comments.

## Daily commands

```bash
php artisan route:list
php artisan view:cache
php vendor/bin/phpunit --colors=never
python3 tools/docs/refresh_repo_maps.py
python3 tools/docs/check_docs.py
git diff --check
git status --short
```

`refresh_repo_maps.py` rewrites generated inventories; use it deliberately and review resulting diffs. Keep credentials and data out of Markdown.

## Commit and push (after checks)

```bash
git add -- docs/ DEVELOPERS_START_HERE.md AGENTS.md CONTRIBUTING.md SECURITY.md routes/README.md app/README.md database/README.md resources/views/README.md tests/README.md tools/docs/
git diff --cached --check
git diff --cached --stat
git commit -m "docs: onboard teammates and map StudyBuddy architecture"
git push origin main
git status
```

The example staging list is for docs-only updates; if this pack also added inline comments, stage those exact files too. Never blindly stage `storage`, SQL backups or `.env` files.
