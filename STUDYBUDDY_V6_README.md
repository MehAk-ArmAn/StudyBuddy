# StudyBuddy V6 — ReCreative UI / Reactivity

<!-- STUDYBUDDY_2026_MASTER_PLAN_BEGIN -->
**Historical V6 patch readme.** Current canonical delivery and visual placement: `docs/STUDYBUDDY_MASTER_PLAN.md` and `docs/REACT_COMPONENT_PLACEMENT_MATRIX.md`. Do not overwrite newer teammate code with these prior visual patch instructions.
<!-- STUDYBUDDY_2026_MASTER_PLAN_END -->


**Scope:** local Laravel/Blade design patch for an existing V5 installation. No database migration, seeding, route, controller, authentication, school-permission, or reward changes. The original catalogue filter logic stays active.

## Features

- Responsive app Discovery stage with custom transparent illustration layers
- Original reactive Canvas 2D topographic contours (24fps cap, visible-only)
- Chromatic button highlights, per-card pointer spotlight/tilt
- Compact Light / Dark / Auto dropdown and stored theme preference (uses V5 theme engine)
- Fixes for washed-out light-mode headline and light/dark card contrast
- Keyboard Escape / outside click menu behavior, focus styling
- Motion-off and `prefers-reduced-motion` support
- Calm school/admin appearance, no fake metrics or extra test apps
- Six original transparent WebP design assets created for StudyBuddy

## Installation

Download `StudyBuddy_V6_REACTIVITY_PATCH.zip` and `Install_StudyBuddy_V6.sh` into `~/Downloads`, then run:

```bash
bash ~/Downloads/Install_StudyBuddy_V6.sh
cd ~/Desktop/StudyBuddy-main
php artisan serve
```

The installer will refuse `APP_ENV=production`, ensure V5 exists, and save a **file-level rollback** on the Desktop. It does not copy your entire project or touch MySQL. If `view:cache` fails, inspect the error before proceeding. The rollout is for local testing, not a verified school deployment.

## Quality notes

- Native adaptations of interaction *concepts* highlighted by ReactDrops. **No ReactDrops library source is bundled**: the official components are React implementations under ReactDrops Component License 1.0, and a genuine integration requires a dedicated React mount/build. This patch works directly in Laravel without dependencies.
- The CDN-provided/other original scripts from V4/V5 remain and might have interaction conflicts; use the browser console and visual QA after installation.
- User settings, security, and school verification need independent testing before any public release.
- Some categories are fictional DEMO entries if previously seeded locally; no further demo seeding occurs.

## Routes to inspect

`/`, `/apps`, `/for-schools`, `/roles`, `/login`, `/register`, individual app pages, dashboard, and admin. Test dark/light/auto at mobile and desktop widths and with Effects Off.
