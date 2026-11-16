# StudyBuddy Universe V4 — cinematic sitewide visual layer

## What this patch changes

- Adds six optimized, original 3D illustrated learning-universe images and a tiny vector star graphic.
- Loads a consistent CSS/JavaScript experience layer through the existing public and admin Blade layouts.
- Public site: ambient scene backgrounds, soft orbital shapes, hover depth, restrained pointer parallax, scroll progress, scroll-in reveals, native page transitions when supported, animation preference switch, accessible quick World Navigator.
- Home: new *four-world preview* with Maths / Science / Reading / Explore-all tabs; links use the existing `/apps?category=...` catalogue routes. **The worlds are illustrative navigation art, not real games.**
- Schools: illustrative role-perspective tabs for leaders, teachers and families. The existing institution approval and invitation process is unchanged.
- Apps: panoramic catalogue illustration; app detail atmospheric art. Existing CMS records, filters, app states and demo flags are preserved.
- Auth / admin / error / dashboards: gentler animations and visual continuity, without adding distracting overlays to sensitive workflows.
- Honors `prefers-reduced-motion`, keyboard focus, and reduced mobile/data-saving workload.

## Safe installation prerequisites

**This is intended for the previously installed `StudyBuddy_NextChapter_PATCH_ONLY.zip` and `StudyBuddy_Sparkle_Demo_v2_PATCH.zip`.** It was built by layering those patches onto the uploaded November 2026 StudyBuddy source.

If your local files have been modified beyond those patches, inspect conflicts before merging. This patch includes **seven existing Blade files**, so extraction will replace those files with versions from that known baseline plus the v4 visual additions. Existing mechanics are preserved within that baseline, but custom unshared edits could be overwritten.

No database migrations, seeders, routes, controllers, models, configuration, user records, sessions or `.env` values are included or changed.

## Install on local development site

1. Stop `php artisan serve` using Ctrl+C.
2. Save `StudyBuddy_Universe_V4_PATCH.zip` to your Mac's Downloads folder.
3. In VS Code Terminal:

```sh
cd ~/Desktop/StudyBuddy-main
ditto -xk ~/Downloads/StudyBuddy_Universe_V4_PATCH.zip .
php artisan optimize:clear
php artisan view:cache
php artisan serve
```

If `php artisan view:cache` fails, **stop and report the exact error** instead of deploying. Run `php artisan view:clear` after fixing it.

Browse `http://127.0.0.1:8000/`, `/apps`, `/for-schools`, login/registration, dashboard, and admin; check both desktop and mobile widths. The quick navigator uses a floating **Worlds** button; **Effects on/off (for this new visual layer)** is per-browser.

## Undoing this patch

The patch is additive except for the listed Blade templates. To revert, restore those templates from the original source plus Next Chapter and Sparkle patches, then delete the v4 CSS, JS, images, and new partials. **Do not run database rollback or migrations** for this visual patch.

## Status / limitations

Static checks: JavaScript syntax verified, ZIP and asset references checked; a static browser concept preview is supplied separately. Full Laravel runtime render, Blade compilation, link/permission regression tests, device performance tests, security testing and live environment deployment **have not been run here**. The new visuals do not make the system school-certified or impossible to breach.
