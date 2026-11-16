# StudyBuddy W4 — Wonderverse Homepage (Blueprint 01 implementation)

## Scope
One existing Laravel 11 project: `~/Desktop/StudyBuddy-main`.
Required baseline: W2 storybook hero + W3 React Bits FlexCarousel installed, with current `resources/views/home.blade.php` and `resources/js/studybuddy-react/entry.jsx`.

### Changes
- Homepage Blade: W4 class, CMS-aware world intro, four informative role cards, clear personalized privacy note, W4 assets.
- Existing W3 React: reduce FlexCarousel arch/bend and distortion for app, role and feature visuals; keep the original React Bits source.
- New homepage-only stylesheet: verified V7 theme hooks `html[data-sb-color-theme=light|dark]`, stable text contrast surfaces, larger typography, responsive layout, CMS feature cards, school section, CTA.
- New JS: only hide old Blade fallback grids after W3 React mounts. Fallback remains if React fails or WebGL/reduced motion disables it.

### Explicitly NOT changed
- Laravel routes, controllers, database, migrations, seeders, school authorization, account permissions, CMS items, dashboard logic, admin tools, app playability, or app status.
- The layout's shared React bridge, W2 logo animation, or any Pro licensed source.

### QA checklist after the script passes
- `php artisan serve` in another terminal; hard refresh `/`.
- Verify original dolphin and CMS hero CTAs.
- Scroll app world carousel: no words bend inside art, active info surface readable.
- Inspect role gallery plus four clear role explanatory cards.
- Inspect personalized goal checklist (browser-only), feature cards from CMS, school and CTA sections.
- Toggle **light/dark**, inspect text/link/outline readability.
- Test narrow 390px, mid 768px and desktop 1440px; keyboard focus; reduced motion.
- Check original `/apps`, `/roles`, `/for-schools`, auth and role-sensitive dashboards without changing access.

### Recovery
Installer makes a timestamped changed-file backup inside `storage/studybuddy-wave4-before-...`, including built React output. It auto-rolls back build/Blade failures. Successful installs can be reverted manually with the backed-up files in that folder.

### Asset art
Existing dolphin and CMS app hero images remain primary. This is intentionally not a new image pack. New page-specific artwork and app data mapping belong to Blueprint 02, with original CMS art and links retained until approved replacements exist.
