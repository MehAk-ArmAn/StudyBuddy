# StudyBuddy V5 · Atmosphere / Theme / Discovery

<!-- STUDYBUDDY_2026_MASTER_PLAN_BEGIN -->
**Historical V5 patch readme.** For current StudyBuddy feature status, permissions, full UI wishlist and execution, read `docs/STUDYBUDDY_MASTER_PLAN.md`, `docs/REACT_COMPONENT_PLACEMENT_MATRIX.md` and `docs/PHASES_AND_RELEASE_GATES.md`. Do not replay this older installer over a newer checkout.
<!-- STUDYBUDDY_2026_MASTER_PLAN_END -->


## Fits
Existing StudyBuddy-main source with Next Chapter, Sparkle Demo V2, Universe V4 installed. This is a **visual/UI patch only**. It does not update PHP controllers, routes, migrations, permissions, database contents, app statuses or seeder logic.

## Install on Mac
1. Stop `php artisan serve` with Ctrl+C.
2. Place StudyBuddy_V5_ATMOSPHERE_PATCH.zip in ~/Downloads.
3. In the already working local project:

```sh
cd ~/Desktop/StudyBuddy-main
ditto -xk ~/Downloads/StudyBuddy_V5_ATMOSPHERE_PATCH.zip .
php artisan optimize:clear
php artisan serve
```

4. Open http://127.0.0.1:8000/apps and try Light / Dark / Auto on the navigation bar, the world-filter chips, Surprise me, and a catalogue card. Also inspect Home, For Schools, Sign-in, Dashboard and Admin. Hard refresh the browser (Cmd+Shift+R) if an old cache persists.

No `migrate`, `db:seed`, `migrate:fresh`, Git commands or Composer update are needed. If a Blade error occurs, stop and send the Laravel error; don't reset the database.

## What changes
- Shared public / admin layouts load a nonblocking 3-mode light/dark/system theme and the V5 stylesheet/script.
- Navbar and admin top bar show the visual appearance selector; standalone admin login / friendly error screens also support theme.
- Sitewide overlays are now separate, transparent ornament PNGs instead of one giant composited image.
- Apps catalogue gets floating layered objects, stronger information hierarchy, theme-aware app cards and clickable subject chips synchronized with original `data-app-category` filter.
- Surprise me spotlights one currently visible catalogue card; it never triggers downloads, plays games, creates rewards or invents content.
- Learning world heading light-mode contrast repaired. School/admin effects remain restrained.

## Limits and testing
- Color theme is a **per-browser preference** in localStorage, not a user setting or security mechanism.
- Respect OS reduced motion and prior Universe Effects Off.
- No new React libraries; effects use vanilla CSS/JS. ReactDrops is design inspiration, not copied component source.
- Static syntax/asset checks were performed in packaging environment; **full Laravel runtime, mobile Safari, network/email, accessibility and school security testing must happen on a configured staging site**.
- This ZIP includes no `.env` and contains no MySQL data, passwords or credentials.

## Quick reversibility
The optional separate StudyBuddy_V5_ROLLBACK_LAYOUTS.zip restores prior shared templates and CSS/JS references; it does not delete the new standalone V5 assets.
