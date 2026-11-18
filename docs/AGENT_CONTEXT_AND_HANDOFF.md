# StudyBuddy — handoff for other AI agents and PCL collaborators

**Read these in order:** `README.md` → `docs/STUDYBUDDY_MASTER_PLAN.md` → `docs/PHASES_AND_RELEASE_GATES.md` → `docs/REACT_COMPONENT_PLACEMENT_MATRIX.md` → `docs/ROLE_PERMISSIONS_AND_SAFEGUARDING.md` → `docs/SOURCE_CODE_MAP.md` → relevant original code. Do not infer anything else from a design mockup.

## User's explicit working rules

- **Direct main** for this project: no speculative feature branch or forced merge; coordinate contributors and inspect current `git status`, diff and last commit. Do not auto-push or force-push. Protect newer teammate changes and production data.
- **Pack by pack** with guarded installer, auto rollback on source/test failure, manual browser check, then commit only changed files; never replace repo with an older uploaded ZIP. Prefer copy-paste commands that run in Mac `StudyBuddy-main`.
- **Every feature must work**: no dead buttons, fake charts, invented AI output, fake billing, no screenshot-only integrations. Don't pretend paid React Bits Pro is installed. Original alternatives are fine and must be labeled.
- **Strong coherent design**: cosmic dolphin/subject mascot system, five banner art assets and 44 WebP illustrations; playful learners, sober institution/operations; animations reduced on mobile and staff pages.
- **All roles and connected school systems**: learner, independent learner, guardian, teacher, verified school admin, scoped PCL admin, Head Admin. Trust/policies server-side and school tenant separation.
- **Important previous correction:** old root README guidance about feature branches and "student dashboards proposed" predates newer packs. The new canonical docs override those old status claims where they conflict. Historical patch docs describe their respective versions only.

## Current evidence (18 Nov Mac logs)

P05 through P09 were pushed to GitHub main, latest confirmed `dc3f3bd`; P10 installed locally with **252 tests / 3,689 assertions** and `/resources` routes listed, push not confirmed. The P10 installer banner mistakenly displayed `PACK 09 INSTALLED`; treat it as a label bug, not failed functional tests.

**Admin identity risk:** `.local` and `.fun` admin accounts both existed in reported local DB; user wants one canonical `admin@studybuddy.fun`. Existing records need a transactionally safe reconciliation, not blanket deletion; a literal placeholder was used as a password—rotate immediately using a strong secret, never set a short shared password. Head Admin `nondeletable` refers to application policy and recovery process.

## Next safe step

Before P11 coding: confirm P10 Git status and commit, inspect current admins and foreign-key dependencies, write explicit permission matrix and tests, check current school organization memberships, then implement small changes with staging DB and rollback. Do not install Pro components without paid access and agreement. For full component wishlist, read machine file `docs/REACT_COMPONENT_CATALOG.json` (all exact selected IDs) rather than scanning chat logs.

## Things NOT granted by this planning package

No new routes, migrations, role grants, public student chat, OAuth provider, payment gateway, voice processing, secret creation, admin password change, background monitoring, or production deployment. It creates docs only.
