# StudyBuddy unified Laravel cleanup — forensic source audit

**Input snapshot:** `StudyBuddy-main(6).zip` (17 November 2026). Changes apply to THIS snapshot; another developer's newer files must be re-reviewed, not overwritten.

## Findings from the supplied source

- W33 source and routes are present. **W34 was NOT present** in the uploaded ZIP; it is included as an additive module in this review package. This is not proof of installation on your Mac.
- PHP source scan: **172** files passed `php -l` before repair; JS scan: **86** asset JS files passed `node --check` before repair. No missing literal Blade `@include` targets were found.
- One missing image reference: `assets/studybuddy-imgs/brand/logo-icon.png` was used by parent/teacher dashboards and public profiles. Replaced with the locally present canonical brand mark.
- `/` was a standalone full HTML document injected as `{!! ... !!}` from `public/assets/studybuddy-base44-exact/home.html`, duplicating the public Laravel header/footer and bypassing the shared content layout. Converted into ONE Blade page using `layouts.app`, preserving the W29-specific `data-studybuddy-exact` root attribute only for the homepage.
- The homepage contained **12 'Demo concept' labels** in a hardcoded carousel. Replaced only that shelf with a single existing CMS-backed `home-sparkle-discovery` partial; visible apps come from `StudyBuddyMiniAppPlatform`.
- W34's original directory and editor nested `<main>` inside Laravel layouts and the public title repeated `StudyBuddy`. Repaired those when bringing W34 into the staged code.
- The archive includes **historical W-wave backup folders** in `storage/` and `studybuddy-reactbits/node_modules/`. **Neither is removed from your machine by this patch.** No source or learner media, game ZIPs, DB, uploads, or secrets are repackaged into this ZIP.
- The original Base44 home HTML is retained as a historical **asset file**, but is no longer used as the active page; removing it requires a separate reviewed dependency inventory.

- W34 previously queried the subject table directly from its view and controller. Its subject assignments now have a standard Eloquent model and `StudyBuddyMiniAppPlatform::subjectAssignment()` relation, with eager loading for the directory.

## Structure after cleanup

```text
routes/web.php                        # canonical Laravel entry: includes modular W33/W34 routes
app/Http/Controllers/HomeController.php
resources/views/layouts/app.blade.php  # single public header, main, footer
resources/views/layouts/admin.blade.php # separate authenticated admin shell
resources/views/home.blade.php        # one home page, same W29 Galaxy visuals
resources/views/partials/home-sparkle-discovery.blade.php # real CMS app shelf
resources/views/studybuddy/w33/       # one app showcase/editor
resources/views/studybuddy/w34/       # one subject directory/editor
app/Http/Controllers/StudyBuddyW34SubjectsController.php
config/studybuddy_w34_subjects.php   # 28 editorial learning categories
```

## What was deliberately NOT changed

- Database content; account records; passwords; uploaded screenshots; web build packages.
- W30/W31/W32 private learning records and reward rules.
- Existing route slugs; admin authentication; API contracts; filesystem backups.
- The actual reactbits source or any third-party component licenses.
- Unverified obsolete code/files: large-scale deletes are dangerous in a live shared repo.

## Required checks on the actual Mac

Run the guarded package installer on a feature branch, then verify `php artisan view:cache`, `php artisan route:list` and `php vendor/bin/phpunit --stop-on-failure`; test homepage at desktop/mobile sizes, W29 Galaxy animation, W33 App Store, `/subjects`, subject assignment, screenshots, links and auth.

**Important:** PHP syntax checks and static dependency checks are NOT full browser/integration verification. Laravel dependencies (`vendor/`), a configured test database and real browser were absent from this upload.

## v2 regression fixes — 17 Nov 2026

- The 404/500 shared error screen now resolves optional theme assets through a checked, versioned helper. It never calls `filemtime` on missing files, so isolated public paths and partial deployments still produce the intended HTTP error status.
- W34's Geography negative test now checks the subject *result title* and empty state, not global footer navigation, which may validly show the same game under Learning Worlds.
- The previous failed installation restored source before migrations, hence the missing W34 migration when manually attempted; do not run the migration until the corrected package succeeds.
- Full Laravel tests must run on the user's development machine; no passing result is claimed here.

## v3: Apps CMS image-source contract

- The Apps catalogue's holographic/gallery preview previously used `$app->safeHeroImage()` (prefers detail cover), while listing cards used `$app->cardImage()` (prefers card artwork). For distinct sources, `/apps` contained the cover URL in the gallery JSON, contradicting `AppsCmsTest::test_card_and_detail_artwork_use_separate_helpers_with_safe_fallbacks`.
- Gallery/filter now use `cardImage()` so all catalogue previews consistently use card artwork; the app detail page keeps its cover-first `detailImage()`.
- Fallback remains model-owned, so if card image is absent it can fall back to the hero/cover image, exactly as `cardImage()` specifies.
- Source change is limited to the catalogue template, and no test was disabled or weakened.
- Full Laravel PHPUnit suite and browser checks must still pass in the developer's Mac environment before migration/merge.

## v4: Systemic Blade asset-version compatibility (17 November 2026)

- V3 reached 66 tests, 342 assertions, then failed in `BrowserLauncherTest` because `app-detail.blade.php` used `filemtime()` on an asset not available in a temporary isolated public directory.
- Scanned all live `resources/views` templates and found 142 literal-path `filemtime(public_path(...))` calls across 32 views, plus 3 dynamic-path calls.
- Updated all 33 active Blade templates containing version lookups. A single autoloaded, framework-native `App\Support\StudyBuddyAssetVersion::for()` helper now returns `filemtime` for existing public assets and stable `0` for absent assets; no PHP warnings on missing paths.
- Added `tests/Unit/StudyBuddyAssetVersionTest.php` for missing assets and timestamp preservation. This prevents another template from causing the same test failure due to isolated paths.
- Does NOT remove old media, W-version script files, Laravel routes or backups; future archival requires dependency verification and runtime UI QA.
- All PHPUnit tests (not only the first 66) and browser QA must pass on Mac before deploying, migrating or merging.

## V5 — End single-failure loop; richer GooeyNav

- The V4 full-suite run halted at 89 tests / 420 assertions on a pre-existing empty-catalogue copy expectation. Added `No apps here yet.` within the one intentional empty catalogue panel; removed the redundant hidden filter-empty component for zero apps, while retaining it for populated catalogues.
- The former W29 canvas filter made every GooeyNav particle look violet even though the WebGL code generated multiple colors. V5 removes that filter.
- Reused the original `w24/source/GooeyNav.jsx` for both W24 fallback and the current W25 shell. The existing fragment shader now paints a smoothly blended multicolor pill and preserves distinct particle colors. Animation has a longer 1500ms travel envelope and softer motion spring; focus and reduced-motion behavior remain.
- Existing original bundles under `public/assets/studybuddy-w24/` and `...w25/` updated in place; no duplicate navigation, new CSS loader, route, or backend service introduced.
- Validation changed from `phpunit --stop-on-failure` to full PHPUnit run (`--colors=never`) whose complete stdout is saved in a single file outside the repo before safe source rollback. This identifies *all* remaining runtime failures at once.
- The latest uploaded repo ZIP omits PHP vendor and test databases; full PHPUnit cannot execute inside this chat. Static syntax, source-to-bundle markers, manifest compatibility and rollback simulation are checked locally. Do not merge/deploy until the Mac suite passes.

- **Visible navigation motion:** ordinary same-origin pointer clicks delay page change by 950ms so animated drops travel before the browser unloads. External links, modified clicks, keyboard activation and reduced-motion remain native/immediate. Rapid repeated clicks cancel a previous pending navigation on the same nav host. No Laravel route behavior changed.

## V6 — Mac full-suite regression triage (17 November 2026)

Full Mac PHPUnit run under V5: **213 tests, 2,681 assertions, 3 failures** (source automatically restored). Addressed the three failure causes in the existing Blade pages: register title repeats the site brand, homepage school-role marketing copy exposes backend terminology, and existing eager hero image lacks `class="hero-art"` / `fetchpriority="high"`. No tests were disabled; no backend role names changed. Full Mac PHPUnit verification is pending; do not merge/migrate until it passes.
