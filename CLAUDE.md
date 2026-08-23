# StudyBuddy — working notes

Laravel 11 + Blade. No JS build step: CSS/JS are plain files in `public/assets`
and are cache-busted with `filemtime()`. There is no `package.json`.

## Setup

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite     # local dev uses sqlite; production uses MySQL
php artisan migrate --seed
php artisan storage:link           # required, or uploaded app artwork 404s
php artisan serve
```

Admin sign-in: `admin@studybuddy.local` / `ChangeMe12345!` (`/admin/login`).
Override per environment with `STUDYBUDDY_ADMIN_EMAIL` / `STUDYBUDDY_ADMIN_PASSWORD`.

## Tests

```bash
./vendor/bin/phpunit          # or: php artisan test
```

Tests run against an in-memory sqlite database (`phpunit.xml`), so they can
never touch dev or production data. PHP 8.5 reports a deprecation from Laravel
11's own `config/database.php` (`PDO::MYSQL_ATTR_SSL_CA`); it is external and
harmless — the suite still exits 0.

## Architecture

- **One source of truth for apps: `studybuddy_mini_app_platforms`**
  (model `StudyBuddyMiniAppPlatform`). The Apps CMS writes it, `/apps`,
  `/apps/{slug}`, `/play/{slug}`, the homepage strip and the footer all read it.
  `studybuddy_app_catalog_items` is a retired duplicate — do not write to it.
- **Apps CMS** — `Admin\StudyBuddyAppController` + `Admin\StudyBuddyAppRequest`,
  views in `resources/views/admin/apps/`, routes under
  `/admin/control-room/apps`. This is the only supported way to add an app.
- **Browser launcher** — `StudyBuddyWebAppPublisher` unpacks admin-uploaded
  static ZIPs into private `storage/app/studybuddy-web-apps/<slug>/` folders;
  active builds are streamed by `/app-builds/*` and drafts only by Admin preview.
  It is security-hardened (path traversal, symlinks, zip bombs, blocked
  executable extensions, atomic swap with rollback). **Do not loosen it or
  put extracted builds back under `public/`.**
- **Publishing rewrites the entry document, never the app's bundles.**
  `flutter build web` writes `<base href="/">`, which makes an untouched build
  ask the site root for `flutter_bootstrap.js`, `main.dart.js`, `canvaskit/`
  and `assets/` — all 404, and the learner sees a white rectangle.
  `normalizeBaseHref()` re-points it at `/app-builds/<slug>/` and injects a
  small bridge script (keeps the base right on other mounts, forces local
  CanvasKit instead of a CDN, posts real readiness on `flutter-first-frame`).
  `main.dart.js` and `flutter_bootstrap.js` are never touched, so one ZIP can be
  published under any slug without rebuilding it.
- Hosted builds run in a **same-origin** iframe (`allow-same-origin` plus
  `allow-scripts`). Flutter cannot start in an opaque origin: `history.pushState`
  throws and CanvasKit's dynamic `import()` fails CORS. The sandbox still
  withholds `allow-top-navigation`, the `allow` list is only what a game needs,
  and the build's own CSP keeps it on our origin apart from
  `https://fonts.gstatic.com` (CanvasKit's text fallback). A hosted build is
  therefore trusted admin-uploaded code with StudyBuddy-origin access — vet what
  is uploaded, and move builds to a separate origin if that ever stops being true.
- Routes are split across `routes/web.php` and several `routes/studybuddy*.php`
  files that `web.php` requires. Register new admin routes in the
  `admin/control-room` group in `routes/studybuddy.php`.
- Three historical admin surfaces still exist (`/admin/*` resources,
  `/admin/control-room/*`, `/admin/studybuddy/*` aliases). Control Room is the
  real one; the others are kept for backwards compatibility.

## Adding an app (no code changes needed)

Admin → Apps → **Add app** → fill in the basics → Availability & launching
(upload a ZIP with `index.html`, or paste an external `https://` launch address,
plus any store links) → **Save as draft** → preview → **Save & publish**.

## Brand identity

`config/studybuddy.php` is the single source of truth for the brand. Site
settings edited in the admin override it; these are the defaults a fresh
install ships with.

- Name: **StudyBuddy**. Slogan: **Learn. Play. Grow. Your Way.** — exact
  wording and punctuation. Do not write variations of it, and do not repeat it
  several times on one screen.
- `StudyBuddy.fun` is the domain. Use it only where a domain belongs (legal or
  contact copy), never as the visual brand name.
- **Identity artwork is local, in `public/assets/studybuddy-brand/`.** The site
  must not lose its favicon because a third-party host is down. The icon set
  (16/32/48/180/192/512 + maskable + `public/favicon.ico`) is generated from
  `studybuddy-logo.png`; regenerate all sizes together if the mark changes.
- `resources/views/partials/brand-head.blade.php` renders the whole head block
  (title, description, favicons, apple-touch, manifest, Open Graph, Twitter).
  Add head metadata there, not in individual layouts.
- Page `@section('title', ...)` must **not** include "StudyBuddy" — the layout
  appends it. `BrandIdentityTest` fails if a title repeats the brand.

## Admin screens with their own layout

`public/assets/js/studybuddy-admin-unified.js` wraps admin forms into generated
"editing blocks". A screen that already has a designed layout opts out by
putting `data-admin-skip-unified` on its root element (the Apps CMS does).
Without it, the script injects a second heading and buries the real actions.

The admin shell is dark: text sitting directly on it needs light colours, while
`.sb-card` contents stay dark-on-light.

## Rules that matter

- **Additive changes only** to `studybuddy_mini_app_platforms` columns, the
  public URLs (`/apps`, `/apps/{slug}`, `/play/{slug}`, `/web-play/{slug}`,
  `/app-builds/...`) and API shapes. Published mobile apps depend on them.
- **Never seed apps.** The catalogue ships empty; real apps are added through
  the admin. A migration (`2026_08_20_000100_retire_demo_app_catalogue`) removes
  the old demo catalogue, so re-adding fixtures to a seeder will fight it.
- **No invented social proof** — no ratings, download counts, testimonials or
  learner numbers unless the database actually holds them.
- **Public copy never mentions the CMS.** No "editable from admin", no
  "managed from the Admin Panel". Visitors do not care how the backend works.
- Add new admin content columns to the model's `$fillable`, or mass assignment
  will silently drop them (this has bitten the project before).
- Store uploaded file paths **root-relative** (`/storage/...`), never
  `Storage::url()`'s absolute URL — that bakes `APP_URL` into the row.
- Public pages must only show a platform button when that platform is actually
  configured. `StudyBuddyMiniAppPlatform::availableActions()` is the helper.
- The launcher must never report an app as running just because the iframe
  fired `load` — a build that failed to boot fires it too. Readiness comes from
  the injected bridge's `studybuddy:app-ready` message or from inspecting the
  frame document; anything else is `Loading app…` then `Couldn't start`.

## UI conventions

- Admin Apps screens use `public/assets/css/studybuddy-admin-apps.css`
  (`.sb-apps`, `.sb-card`, `.sb-field`, `.sb-btn`, `.sb-check`). Tables collapse
  into labelled cards under 760px. Minimum 44px tap targets, visible focus rings.
- Validation messages are written in plain language in `StudyBuddyAppRequest`;
  never let a raw SQL or type error reach a user.
