# Architecture: what talks to what

**Primary application:** Laravel 11 with Blade views and static assets under `public/assets`. The archived tree also contains `package.json` and `studybuddy-reactbits/`; whether any Node build is required for your specific UI task must be checked in the current checkout. See the generated repo map.

```text
Browser / learner / family / teacher / school / admin
    -> routes/web.php (includes routes/studybuddy*.php modules)
    -> Laravel middleware + controller authorization
    -> services/support classes and Eloquent/DB models
    -> MySQL (local setup shown in project logs), SQLite in-memory for PHPUnit
    -> Blade layouts + assets + protected upload storage
```

**Source-of-truth map:**
- Application catalogue: `StudyBuddyMiniAppPlatform` / `studybuddy_mini_app_platforms`. The retired `studybuddy_app_catalog_items` table is not the publishing authority (see older `CLAUDE.md` for background; verify the current code).
- Browser builds: publisher service validates uploaded ZIPs, holds builds in private storage, streams permitted assets; do not move builds blindly to a public directory.
- User roles: Laravel auth + middleware + P11A authority; do not implement permissions only in Blade.
- School membership: `SchoolOrganization`, `SchoolOrganizationMember` and scoped controllers. P11B/C/D each have their own dedicated routes and schema.
- Reward integrity: `StudyBuddyRewardIntegrity` after server-graded Math Sprint, shared with wallet/transaction journal and achievements; game launch isn't proof of success.
- Views: shared `layouts.app`, `layouts.admin`, role-specific content. UI may use layered CSS; inventory before cleanup.

**A request's review path:** find its named route, controller method, middleware/authorization check, model/data tables, related view and Feature test. The [generated route inventory](generated/ROUTE_INVENTORY.md) reflects the local route list when available.

**Version safety:** This guide intentionally avoids listing all 310 route names from a historic log. Generate routes from the actual checkout with `python3 tools/docs/refresh_repo_maps.py`.
