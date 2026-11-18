# StudyBuddy — start here (new teammate guide)

> **Purpose:** One reliable entry point into the repository. Read this before you edit an existing system. This handover was prepared on **18 November 2026** from a repository archive and later local installation/test logs. **The actual checked-out source and running environment take priority** over historical statements. See [Current state](docs/handbook/CURRENT_STATE.md).

## Your first 20 minutes

1. Read [System architecture](docs/handbook/ARCHITECTURE.md), [Feature ownership map](docs/handbook/FEATURE_MAP.md), and [Security/permissions](docs/handbook/SECURITY_BOUNDARIES.md).
2. Open [Setup](docs/handbook/LOCAL_SETUP.md), confirm `.env` is **private**, and use the existing environment's database—not production data.
3. Run `php vendor/bin/phpunit --colors=never`; the suite uses in-memory SQLite according to `phpunit.xml`, but confirm the current configuration before running.
4. Inspect routes with `php artisan route:list` and the generated [Route inventory](docs/handbook/generated/ROUTE_INVENTORY.md).
5. For changes to a module, read the related `docs/STUDYBUDDY_*.md` phase note and its Feature test before editing.
6. Review [Release checklist](docs/handbook/RELEASE_CHECKLIST.md) before committing to `main`.

## Where do I go?

| Need | Start here |
| --- | --- |
| Site entry, route registration | `routes/web.php` and `routes/README.md` |
| Content and game catalogue | `app/Models/StudyBuddyMiniAppPlatform.php`, `app/Http/Controllers/Admin/`, `docs/handbook/FEATURE_MAP.md` |
| Shell / visual consistency | `resources/views/layouts/`, `public/assets/`, `docs/handbook/DESIGN_SYSTEM.md` |
| Schools / workspaces | `app/Models/SchoolOrganization*.php`, `routes/studybuddy_schools.php`, phase 11B/11C/11D docs |
| School messages and notices | P11C & P11D controllers; see [School OS](docs/handbook/SCHOOL_OS.md) |
| Points / badges | `app/Support/StudyBuddyRewardIntegrity.php`, P11F tests; see [Rewards](docs/handbook/REWARDS.md) |
| Head Admin / PCL roles | P11A security authority, `app/Http/Middleware/EnsureAdmin.php`, [Permissions](docs/handbook/PERMISSIONS.md) |
| New developer, tooling, conventions | `CONTRIBUTING.md`, `AGENTS.md`, `docs/handbook/DOCS_POLICY.md` |
| What actually exists today? | `docs/handbook/generated/REPO_MAP.md`, `ROUTE_INVENTORY.md`, `DATA_INVENTORY.md` |

## Rules that prevent accidental damage

- **Do not** commit `.env`, local password files, database SQL dumps, user uploads, exported student data or generated testing fixtures.
- **Do not** assume Head Admin may execute arbitrary PHP/code through the website. Changes to code go through Git + tests.
- **Do not** treat an iframe/game-launch click as verified achievement. Points originate only from server-validated activity and atomic ledger writes.
- **Do not** bypass school membership, active status or organization scope in any query.
- **Do not** replace the current UI with an older ZIP's files without comparing hashes/diffs; P11F had a real baseline conflict.
- **Do not** claim that the entire platform is production-certified just because PHPUnit passes. Browser, role, privacy and security QA are separate.

## Not all 'phases' mean deployed

History lives in [Release & handoff timeline](docs/handbook/RELEASE_HISTORY.md). The 11E staff calendar was discussed but **not confirmed installed** in supplied terminal logs. Do not reference it as live. The UI/UX consistency review found 43 distinct public CSS paths; its first integration fix was pushed but visual browser QA remains necessary.

## For the next colleague

Use the [handoff template](docs/handbook/HANDOFF_TEMPLATE.md). Leave instructions that explain the **reason for non-obvious logic**, what was tested, what remains unverified, and a rollback path. In-code comments should explain *why* and boundaries—not narrate each line.
