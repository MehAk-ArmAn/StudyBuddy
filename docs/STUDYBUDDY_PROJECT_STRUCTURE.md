# StudyBuddy Project Structure

<!-- STUDYBUDDY_2026_MASTER_PLAN_BEGIN -->
## Current project map (18 November 2026)

This original structure document captures an earlier consolidation intent. The **actual** repo snapshot and later phases include multiple `routes/studybuddy_*.php` modules. Do **not** delete/merge these unilaterally because they may be live; see [`SOURCE_CODE_MAP.md`](SOURCE_CODE_MAP.md). New features should use deliberate route grouping while preserving existing named routes. Existing `public/assets/studybuddy-p07/banners/`, `/icons/` and `/library/` are the visual-asset pack; `/my-day`, `/getting-started`, `/study-space`, and `/resources` have documented later-phase implementations. Planned PCL + school admin governance, chat, billing and integrations are **not** thereby installed.
<!-- STUDYBUDDY_2026_MASTER_PLAN_END -->


StudyBuddy now follows a cleaner Laravel-style structure inspired by the organized BangTan project:

```text
app/                  Application code: controllers, middleware, models
bootstrap/            Laravel bootstrapping and generated cache
config/               Configuration files
database/             Migrations and seeders
public/               Public entry point and browser assets
public/assets/studybuddy-imgs/  Git submodule / external asset library
resources/views/      Blade views grouped by feature
routes/web.php        Core Laravel web routes
routes/studybuddy.php All StudyBuddy feature routes in one organized registry
storage/              Runtime logs/cache/uploads, not source code
vendor/               Composer dependencies, not edited manually
```

## Route rule
Do not create new `routes/studybuddy_phase*.php` patch files anymore. Add StudyBuddy routes inside `routes/studybuddy.php`, grouped by feature.

## Asset rule
Use the GitHub image library under:

```text
public/assets/studybuddy-imgs/
```

Brand logo path:

```text
assets/studybuddy-imgs/brand/logo-icon.png
```

Main tagline:

```text
Learn. Play. Grow. Your Way.
```

## Admin rule
The main admin experience is:

```text
/admin/control-room
```

Legacy `/admin/studybuddy/...` names are kept only so older forms do not break.
