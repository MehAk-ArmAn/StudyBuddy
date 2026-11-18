# Troubleshooting — quick diagnostics

| Symptom | Safest first check |
| --- | --- |
| Missing route / link 404 | `php artisan route:list`, `routes/web.php` includes, role-safe nav link |
| Blade error | `php artisan view:cache`, layout/partial linkage, error output (without user data) |
| Tests pass but UI differs | browser cache/static CSS, layered styles, responsive screenshots |
| Reward balance mismatch | inspect verified reward event, `StudyBuddyPointTransaction` and `users.cosmic_points` in private DB; never blindly overwrite balances |
| Student rewarded on launch | disable claim endpoint; require per-game server-verified result |
| School user sees another school | stop release: inspect organization/membership scopes and route bindings |
| Admin sees missing Rewards link | check current Head identity, sidebar guard and direct controller auth |
| ZIP baseline conflict | do not force overwrite; three-way merge only after reviewing actual files |
| `mysqldump` auth plugin error | developer Mac used MySQL 8.4 client with older server auth; never paste placeholder `YOUR_DB_HOST` as a real hostname |
| Generated file staged by tests | inspect `git diff --cached --name-only`; unstage just the irrelevant file; do not delete user data |
| Team doc contradicts repo | trust current code, update docs, log decision in `DECISIONS.md` |

If still blocked, attach the **last relevant stack trace and Git diff**, redacting tokens, e-mails, user records and private URLs.
