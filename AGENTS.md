# StudyBuddy — instructions for AI coding agents and colleagues

**Authoritative starting point:** [DEVELOPERS_START_HERE.md](DEVELOPERS_START_HERE.md). These instructions are additive and do **not** override project-specific security checks.

## Safest way to work

- Inspect the **current** files before generating code; old phase ZIPs may be behind the latest `main`.
- Preserve existing UI/UX, user accounts, migration history and data. Build additive changes and make a source backup before any forced operation (prefer never forcing).
- Keep authorization checks in controllers/services; sidebar visibility isn't authorization.
- Every change to earning balances must be server-authoritative and atomic; no client-supplied completion points.
- Schools are tenant-isolated; scope to organization membership and never expose one school's messages to another.
- Never insert real user secrets or fixture passwords into docs, test output, pull requests or external prompts.
- A `phpunit` green result is necessary but not sufficient. For UI work also compile Blade and test role journeys in a real browser.
- Keep docs and tests updated with changes. Run `python3 tools/docs/check_docs.py` when present.
- Direct-to-`main` is the current maintainer preference, but coordinate simultaneous work and **never** use `git push --force`; use scoped staging and check the diff before push.

## Preflight

```bash
pwd
git branch --show-current
git status --short
php artisan route:list
php vendor/bin/phpunit --colors=never
python3 tools/docs/check_docs.py
```

## Historical warning

The older `CLAUDE.md` may contain historical assumptions and development defaults. For example, a supplied archive had a `package.json` despite a sentence in `CLAUDE.md` saying none existed. Treat it as context, not a live fact; compare with [generated repo inventory](docs/handbook/generated/REPO_MAP.md).
