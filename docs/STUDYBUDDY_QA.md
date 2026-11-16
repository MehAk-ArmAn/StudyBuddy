# StudyBuddy — rescue & QA record

Written during the production rescue pass. It records what was actually broken,
what changed, and what was verified.

---

## 1. Blockers found

| # | Problem | Evidence |
|---|---|---|
| 1 | Project would not boot from a clean checkout | no `vendor/`, no `.env` |
| 2 | **Admin login impossible on a fresh install** | `StudyBuddyAdminSeeder` existed but was never registered in `DatabaseSeeder`; `migrate --seed` produced 0 users |
| 3 | README credentials did not match any seeder | README said `admin@studybuddy.local`, seeder created `admin@studybuddy.fun` |
| 4 | **No way to create an app** | the only route was `PATCH .../final-platform/apps/{app}`; no create, store, destroy, slug field or reorder |
| 5 | Admin "Apps" was one page rendering every app as a full inline form | no list, search, filter or pagination |
| 6 | Control Room advertised "Add app listings" | the link led to a page that could not add |
| 7 | Two parallel app tables | `studybuddy_mini_app_platforms` (public) and `studybuddy_app_catalog_items` (Content Studio) held duplicate data |
| 8 | **Every public app card showed the same placeholder** | `safeHeroImage()` read only `hero_image`, but all artwork lived in `image_url` |
| 9 | Silent data loss on save | `long_description`, `image_url`, `age_range` were missing from `$fillable` |
| 10 | Admin instructions printed on public pages | e.g. Privacy Policy read *"Replace this starter content with your final legal wording before public launch."* |
| 11 | Invented review scores | seeded app cards carried `badge_text` of `4.8`, `4.7`, `4.6` |
| 12 | Hardcoded demo apps in code | footer "Learning Worlds" column and `StudyBuddyExperienceController::fallbackApps()` |
| 13 | **Zero test coverage** | `phpunit.xml` pointed at a `tests/` directory that did not exist |
| 14 | Source disclosure risk | `.bak_<timestamp>` copies of CSS/JS were served from `public/assets` |
| 15 | Repository cruft | 63 `.bak` files, a stale backup directory, 2 dead route files, a duplicated migration |

Verified **good** and deliberately left alone: `StudyBuddyWebAppPublisher` —
its ZIP handling already defends against path traversal, symlinks, zip bombs and
executable uploads, with an atomic swap and rollback.

---

## 2. What changed

### Foundation
- Registered `StudyBuddyAdminSeeder` in `DatabaseSeeder`.
- Admin account set to `admin@studybuddy.local` / `ChangeMe12345!`, bcrypt-hashed,
  idempotent, and it *adopts* an admin from an earlier build instead of creating
  a second one. Overridable via `STUDYBUDDY_ADMIN_EMAIL` / `_PASSWORD`.
- Removed the duplicated contact-messages migration.
- Archived then deleted 63 `.bak` files and 2 dead route files. Archives:
  `storage/legacy-backups/*.tar.gz`.
- Fixed a PHP 8.5 deprecation in `config/database.php`.

### Apps CMS (new)
- `Admin\StudyBuddyAppController` — list (search / status / visibility filters,
  paginated), create, store, edit, update, destroy, publish toggle, feature
  toggle, reorder.
- `Admin\StudyBuddyAppRequest` — shared validation with plain-language messages.
- `resources/views/admin/apps/{index,form}.blade.php` +
  `public/assets/css/studybuddy-admin-apps.css`.
- Artwork upload with preview, replace and remove; superseded uploads are
  deleted so nothing orphans. Paths stored **root-relative**.
- Slug auto-generated, unique, and renaming one relocates the published web
  build (`StudyBuddyWebAppPublisher::renamePublishedSlug()`).
- Deletion requires typing the app name, and removes the hosted build, the
  retained ZIP and uploaded artwork.

### Browser launcher & store links
- `availableActions()`, `storeLinks()`, `usesUploadedBuild()`,
  `usesExternalBrowserUrl()` on the model drive every public CTA.
- A platform button renders **only** when that platform is configured;
  otherwise the app reads "Coming soon". No dead buttons.
- Uploaded builds are framed by the StudyBuddy launcher; externally hosted apps
  are handed off in a new tab rather than framed, because a third-party site's
  security headers may forbid framing.
- External launch addresses are validated as real `http(s)` URLs.

### Clean catalogue
- Migration `2026_08_20_000100_retire_demo_app_catalogue` removes the eight demo
  apps from both tables, clears invented rating badges and deletes demo web
  builds. It only touches known demo slugs, so real apps are safe.
- Demo apps removed from `DatabaseSeeder`, `StudyBuddyPublishContentSeeder`,
  `PublishReadyContentSeeder`, and the shell seeders.
- `StudyBuddyExperienceController` now reads the unified catalogue; its
  hardcoded `fallbackApps()` is gone.
- Footer "Learning Worlds" column is generated from published apps and hides
  itself when the catalogue is empty.
- Premium empty states on `/apps` and the homepage strip.

### Content
- `StudyBuddyCopySeeder` — idempotent, safe to re-run on production:
  `php artisan db:seed --class=StudyBuddyCopySeeder`.
- Removed every "editable from admin" / "What you can edit" / "Replace this
  starter content" string from public pages.
- Wrote real plain-language Privacy, Data Deletion, Terms, Cookies and
  Disclaimer content in `StudyBuddyInfoPageController`.

> **Legal note:** the policy pages are written to be accurate and readable, not
> to be legal advice. Have a lawyer review them before launch.

---

## 3. Tests added

`tests/` did not exist. It now contains **103 tests / 1087 assertions**.

| File | Covers |
|---|---|
| `tests/Unit/StudyBuddyMiniAppPlatformTest.php` | image fallback, accent colours, initials, role visibility |
| `tests/Feature/Admin/AdminAccountTest.php` | seeding, hashing, idempotency, legacy-admin adoption, sign-in, access control |
| `tests/Feature/Admin/AppsCmsTest.php` | full CRUD, validation, uploads, publish, reorder, delete, search |
| `tests/Feature/Apps/BrowserLauncherTest.php` | browser CTA rules, uploaded vs external builds, store CTAs, path traversal |
| `tests/Feature/Apps/EmptyCatalogueTest.php` | empty catalogue, empty states, no demo data, no CMS leakage |
| `tests/Feature/Brand/BrandIdentityTest.php` | slogan, seeded identity, every icon file + its real pixel size, head metadata, manifest validity, no third-party image hosts |
| `tests/Feature/Brand/PublicCopyTest.php` | renders 17 public pages and fails on developer/placeholder/fake-social-proof wording, alt text, titles, LCP hero |

Each fixed bug has a regression test.

---

## 4. Manual QA performed

All 82 static GET routes were requested as guest and as admin — all 2xx/3xx.

The full "add a new app" scenario was run against the dev server: log in →
Apps (empty) → Add App → save as draft → confirm the DB row → confirm it is
**not** public → edit the description → reload and confirm it persisted →
enable browser play → upload a ZIP → publish → confirm it appears on `/apps` →
confirm "Play in browser" and "Google Play" CTAs → launch and reach the real
app → remove the store URL → confirm that CTA disappears → unpublish → confirm
it is gone publicly → delete → confirm files cleaned up and catalogue back to 0.

Slug renaming with a published build was verified to move the private
`storage/app/studybuddy-web-apps/<slug>/` directory and update `web_play_url`.

---

## 4b. Branding & user-ready pass

### Brand identity
- Added `config/studybuddy.php` as the single source of truth (name, slogan,
  domain, theme colours, icon paths).
- Added `resources/views/partials/brand-head.blade.php`: one head block shared
  by the public site, auth screens and error pages.
- **Generated a complete local icon set** from the canonical dolphin-and-book
  mark: 16/32/48/180/192/512 px, a maskable 512 with a 16% safe zone, and a
  real multi-size `public/favicon.ico`. Previously the favicon was a
  `raw.githubusercontent.com` URL and the manifest pointed at
  `/assets/studybuddy-imgs/brand/logo-icon.png`, **which did not exist** — the
  PWA had no working icons at all.
- Rewrote `public/manifest.webmanifest`: correct name, description, id, scope,
  categories, and three icons that resolve.
- Navbar and footer fell through to a generic control-room glyph because the
  real mark was not in their candidate list; both now lead with the brand logo.
- Admin shell and error screens now carry the same favicon and touch icon.
- Added Open Graph and Twitter metadata (previously none), with the 500px mark
  as the social image rather than a stretched favicon.

### Images
- Downloaded the five page illustrations locally and converted them to WebP:
  the hero went from **1598 KB to 118 KB**, the four path cards from ~1.2–2 MB
  each to ~50 KB. Total page artwork is now ~323 KB.
- **Zero external image references remain** anywhere in the codebase or the
  database (was 9 distinct URLs, 4 of which 404ed).
- Fixed 6 broken images hardcoded in Blade (a missing planet decoration, a
  missing `path-learning.png`, and two GitHub `blob` URLs that redirect).
- Replaced two page heroes that were reusing deleted demo-app artwork.
- Decorative images now use `alt=""` + `aria-hidden`; the hero has a real
  description. Added `width`/`height` to reduce layout shift, `loading="lazy"`
  below the fold, and `fetchpriority="high"` on the LCP hero.

### Copy
- Rewrote the homepage cards, the "why it works" grid and the whole footer.
  Every button had said "Open"; they now name their destination.
- Wrote real Terms, Cookies and Disclaimer content (they previously fell back
  to "This page is part of the StudyBuddy information centre and can be edited
  from the admin-managed database content").
- **Legal pages rendered their heading with nothing underneath**:
  `StudyBuddyInfoPageController::sectionsFor()` read sections but never their
  items. Fixed, so the privacy and deletion content actually shows.
- Auth screens: "Access key" → "Password"; removed "not learning-stage fields".
- Error pages: friendlier copy, brand mark instead of "StudyBuddy system".
- Page titles no longer repeat the brand ("Login to StudyBuddy · StudyBuddy").
- `/platform-roadmap` and `/launch-readiness` leaked an internal build
  checklist ("Admin authorization hardening", "public content is admin
  editable") to the public. The URLs are preserved but now require an admin
  session.

### UI
- **The Apps CMS was being hijacked** by `studybuddy-admin-unified.js`, which
  wrapped its filters into "Editing block 1" and injected a duplicate "Apps
  Admin" header above the real one. Added a `data-admin-skip-unified` opt-out.
- Fixed a contrast bug: the Apps CMS page heading used dark ink on the dark
  admin shell and was almost invisible.
- Fixed a hero rendering bug — the highlight span is `display:block`, so the
  trailing full stop in "Learn. Play. Grow. Your Way." wrapped onto its own
  line and rendered as a stray dot under the heading.
- Raised every tap target to 44px (card links and auth switches were 17–18px).

### Verified with a real browser
Puppeteer + Chrome across **320 / 390 / 768 / 1280 / 1440 px** on 10 pages:
no horizontal scroll, no clipped text, no undersized tap targets, no console
errors, no failed requests.

> One bug was introduced and caught during this pass: a multi-line `@php(...)`
> in the admin head broke Blade parsing and 500'd every admin page. It only
> surfaced when testing with `APP_DEBUG=false`, which is why the production-mode
> sweep is now part of the routine.

## 5. Remaining external blockers

1. **The shared image library is not checked out here.**
   `public/assets/studybuddy-imgs` is a git submodule
   (`MehAk-ArmAn/StudyBuddy-Imgs`). Every asset the site actually needs has
   been copied into `public/assets/studybuddy-brand/`, so nothing depends on it
   any more, but `git submodule update --init` restores the wider library if
   you want more artwork to choose from. Apps with no uploaded icon render a
   distinct generated tile from their emoji and a slug-derived colour pair.
2. **This working copy is not a git repository**, so no branch or commit was
   made. Nothing was committed and no history was altered.
3. **MySQL is not installed in this environment.** Development and tests use
   sqlite; `.env.example` still documents MySQL for production.
4. Laravel 11 on PHP 8.5 emits a `PDO::MYSQL_ATTR_SSL_CA` deprecation from its
   own vendor config. External; the suite still exits 0.
