# Release gate — change to `main`

- [ ] Confirm branch, clean starting status and scoped diff (`git status --short`, `git diff`).
- [ ] Understand affected route, controller, model, migration, view, test, and permission boundary.
- [ ] Test Happy path **and** unauthorized/cross-user/cross-school path.
- [ ] For rewards: inspect duplicate credits, caps, atomicity and transaction journal.
- [ ] For UI: compare mobile/tablet/desktop, keyboard and light/dark if relevant.
- [ ] For schema: take/verify private backup; run migrations only on deliberate target.
- [ ] Update affected docs and regenerate source-derived maps if paths/routes/models changed.
- [ ] `php artisan view:cache` and `php vendor/bin/phpunit --colors=never` pass (record counts).
- [ ] `python3 tools/docs/check_docs.py` and `git diff --check` pass.
- [ ] `git diff --cached --name-only` has no `.env`, `.sql`, `*.private.md`, `storage/framework/testing` or other generated assets.
- [ ] Commit a scoped change and push to `main` without force; `git status` clean.
- [ ] Document how to verify and roll back; do not claim production deployed based on Git push alone.
