# StudyBuddy source map — evidence-scoped architecture index

**Source snapshot:** `StudyBuddy-main(7).zip` uploaded 18 November 2026 (may predate installed Packs 07–10). Later routes/paths are from **pack payloads and user Mac logs**, not from a fresh git clone of newest main. Read `git status` and confirm on Mac before editing.

## Source structure sampled from uploaded archive

| Area | Number of files (in supplied snapshot) | Representative concrete paths |
|---|---:|---|
| app/Http/Controllers | 54 | `app/Http/Controllers/AccountConnectionController.php`, `app/Http/Controllers/Admin/DashboardController.php`, `app/Http/Controllers/Admin/FooterItemController.php`, `app/Http/Controllers/Admin/HomepageSectionController.php` |
| app/Models | 26 | `app/Models/AccountConnection.php`, `app/Models/FooterItem.php`, `app/Models/HomepageSection.php`, `app/Models/HomepageSectionItem.php` |
| app/Support | 4 | `app/Support/AdminLabel.php`, `app/Support/StudyBuddyAssetVersion.php`, `app/Support/StudyBuddyClassProgress.php`, `app/Support/StudyBuddyLearningSnapshot.php` |
| routes | 20 | `routes/console.php`, `routes/studybuddy.php`, `routes/studybuddy_admin_health_cms.php`, `routes/studybuddy_admin_pro_final.php` |
| database/migrations | 35 | `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/0001_01_01_000001_create_cache_table.php`, `database/migrations/0001_01_01_000002_create_jobs_table.php`, `database/migrations/2026_06_16_000000_create_clean_cms_tables.php` |
| database/seeders | 9 | `database/seeders/DatabaseSeeder.php`, `database/seeders/PublishReadyContentSeeder.php`, `database/seeders/StudyBuddyAdminSeeder.php`, `database/seeders/StudyBuddyCopySeeder.php` |
| resources/views/admin | 25 | `resources/views/admin/account/edit.blade.php`, `resources/views/admin/apps/form.blade.php`, `resources/views/admin/apps/index.blade.php`, `resources/views/admin/apps/partials/flash.blade.php` |
| resources/views/dashboard | 19 | `resources/views/dashboard/command-center.blade.php`, `resources/views/dashboard/index.blade.php`, `resources/views/dashboard/my-quest.blade.php`, `resources/views/dashboard/partials/command-center-link.blade.php` |
| resources/views/studybuddy | 27 | `resources/views/studybuddy/admin/shell/index.blade.php`, `resources/views/studybuddy/apps/index.blade.php`, `resources/views/studybuddy/apps/partials/locked-modal.blade.php`, `resources/views/studybuddy/apps/show.blade.php` |
| public/assets | 348 | `public/assets/css/admin.css`, `public/assets/css/auth.css`, `public/assets/css/experience.css`, `public/assets/css/sb-auth-role-ui.css` |
| studybuddy-reactbits | 89 | `studybuddy-reactbits/REACT_BITS_ATTRIBUTION.md`, `studybuddy-reactbits/STUDYBUDDY_ONE_LIVE_STATUS.txt`, `studybuddy-reactbits/STUDYBUDDY_V10_1_COMPONENT_STATUS.txt`, `studybuddy-reactbits/components.json` |
| tests/Feature | 23 | `tests/Feature/Admin/AdminAccountTest.php`, `tests/Feature/Admin/AdminUiRedesignTest.php`, `tests/Feature/Admin/AppsCmsIntegrityTest.php`, `tests/Feature/Admin/AppsCmsTest.php` |
| docs | 11 | `docs/APP_UNIFICATION_AUDIT.md`, `docs/FINAL_PLATFORM_DECISION.md`, `docs/PROJECT_AUDIT_FINAL.md`, `docs/PUBLIC_PAGE_CONSISTENCY_AUDIT.md` |

## Real entrypoints (from supplied snapshot and pack/log evidence)

| Feature | Backend / route source | View / asset source | Evidence boundary |
|---|---|---|---|
| Public apps & CMS | `routes/studybuddy.php`; `app/Http/Controllers/Admin/StudyBuddyAppController.php` | `resources/views/admin/apps/`, `resources/views/studybuddy/final/` | Snapshot; real data only |
| Admin control room | `app/Http/Controllers/StudyBuddyControlRoomController.php` | `resources/views/admin/control-room/index.blade.php` | Snapshot; no multi-admin scoped grants yet evidenced |
| School approvals and workspace | `routes/studybuddy_schools.php`; `app/Http/Controllers/SchoolOrganizationController.php` | `resources/views/studybuddy/schools/workspace.blade.php` | Snapshot; approved-member enforcement requires tests |
| Community | `StudyBuddyProfileController::community` | `resources/views/profile/community.blade.php` | Snapshot; **profile discovery** not yet group chat |
| Role dashboards | `DashboardController.php` | `resources/views/dashboard/index.blade.php`, `partials/` | P07 Mac tests/push |
| Study Space | `routes/studybuddy_w36.php` | `resources/views/studybuddy/w36/study-space.blade.php` | P06 pushed; not an AI backend |
| Onboarding | `routes/studybuddy_p08_onboarding.php` | `resources/views/studybuddy/p08/onboarding.blade.php` | P08 R2 pushed and tests |
| Personal day/calendar | `routes/studybuddy_p09_pulse.php` | `resources/views/studybuddy/p09/pulse.blade.php` | P09 pushed and tests |
| Resource studio | `routes/studybuddy_p10_resources.php` | `resources/views/studybuddy/p10/` (verify exact path in pack) | P10 installed local; push unconfirmed |
| React Pro config | `studybuddy-reactbits/pro-ui/components.json` | `pro-ui/src/` | Alias configured; paid code not licensed/installed |

## Change surface / new feature owner routing

- Admin policy/service: create `app/Policies` or equivalent and fine-grained gates; do not change `is_admin` without guarding all controllers, `/admin/login`, and school staff invites.
- School data: extend existing `SchoolOrganization*` and membership checks rather than making parallel school users tables.
- Community: refactor `StudyBuddyProfileController` discovery only after preserving the existing `/community` and its privacy settings; build separate channel/DM controllers for new writes.
- UI: scope React islands under `studybuddy-reactbits/pro-ui/src/...`, build outputs into namespaced `public/assets/studybuddy-*`, and mount from Blade only when needed.
- Documentation: preserve historical QA/security patch docs; update current phase record and capability boundaries first.

## Risk register from current project history

1. **Password/security:** old local admin seed defaults and two existing accounts; never deploy placeholder password; design migration to canonical Head Admin and MFA.
2. **Git debris:** PHP tests have renamed `.dmg` fixtures in tracked `storage/framework/testing/**` previously. `git add .` has repeatedly committed these; add durable ignore/untrack policy only after review. Until then stage explicit doc paths or exclude fixture directory.
3. **README drift:** prior README states learner dashboards proposed and branch+PR policy, contradicted by later tested packs/main-only practice. This package provides newer overlay notices; historic documentation remains source of earlier decisions only.
4. **P10 label mismatch:** installer output says PACK 09 after 252 tests, despite resource routes and P10 backup. Fix installer label in later code QA, not by replaying migrations.
5. **Tenant risk:** member records, email-based joins, adult/teacher trust, and school separation require dedicated negative tests before school launch.
6. **Pro licensing:** registry aliases alone do not provide Pro components; no fake installed claims.
