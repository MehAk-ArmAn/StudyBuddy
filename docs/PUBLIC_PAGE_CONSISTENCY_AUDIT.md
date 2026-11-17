# StudyBuddy Public Pages — Consistency & First-Paint Audit

**Basis:** user-uploaded `StudyBuddy-main(6).zip` plus the exact payloads of the **V7 Laravel stabilization** and **GooeyNav alignment fix** that subsequently passed 213/213 PHPUnit tests on the user's Mac. No live database/browser session was accessible from this workspace.

## One shared Laravel public layout

`resources/views/layouts/app.blade.php` owns the shared header, footer, site themes, assets, and the `#main-content` landmark. Public Blade routes continue to extend it. Admin Control Room intentionally uses `layouts.admin` to keep authenticated operations separate; error screens are intentionally standalone error documents. **No second site layout, navbar, footer, or route was introduced.**

## Confirmed root causes and corrections

1. **Navigation shape-shifting after page load:** The Blade fallback navigation was replaced directly by `createRoot(...)`, so React had different markup and dimensions. The **existing compiled React GooeyNav** is now mounted in an initially invisible sibling slot, while the Blade links remain usable. W25's shared JavaScript reveals the interactive nav only after its DOM is measurable; old links become inert/aria-hidden. Layout width is preserved. WebGL failure leaves the original links working. No duplicate *visible* navigation.
2. **W27 legacy header interference:** Older W27 code added `sb27-*` classes and hid fallback links when it detected any Gooey canvas. It now skips W25 headers completely and only handles truly older headers, preventing a CSS conflict and the fallback disappearing too soon.
3. **W26 duplicate content injection:** W26's `addRoute(...)` inserted a *second* illustrated promotional section after DOMContentLoaded on Roles, Schools, Apps, Auth, Dashboard, and other pages. The layout shifts and extra sections conflicted with the page-specific Blade design. That route injection is now disabled; W26's existing homepage gallery remains available. No feature, CMS model or route was removed.
4. **Schools hero contrast:** A global dark-mode rule set Schools text pale while its design remained lavender-white. One final scoped page-consistency CSS file restores dark ink and accessible contrast on that intentionally light area in both theme modes. School approval logic is untouched.
5. **Gallery label overlap:** Blade fallback titles and subtitles were visually jammed together and React captions had insufficient wrapping. Both roles/schools are now styled as separate text blocks, responsive without mid-letter breaking. The original ReactBits galleries and fallback content remain; nothing is replaced by fake cards.
6. **Small content issue:** Removed repeated school-review sentence; no UI workflow logic changed.

## Page-by-page source inspection and resolution

| Page / route | Laravel view/layout | Check or correction |
|---|---|---|
| `/` and `/#story` | `home.blade.php` / public layout | Canonical W25 navbar; keep authored homepage visual sections, no duplicate header |
| `/apps`, `/apps-catalog` | `studybuddy/final/apps.blade.php` | Canonical header; W33 CMS catalogue/ReactBits left intact; no W26 injected banner |
| `/apps/{slug}` | `studybuddy/final/app-detail.blade.php` | Uses same public shell; W33 screenshots/play buttons preserved |
| `/play/{slug}`, `/web-play/{slug}` | `studybuddy/final/web-play.blade.php` | Same shared shell where used; game and browser launcher untouched |
| `/roles` | `studybuddy/info/roles.blade.php` | Existing role cards remain; no injected W26 banner; stable ReactBits captions/fallback |
| `/for-schools` | `studybuddy/schools/info.blade.php` | Light/dark contrast corrected, responsive hero, labels fixed, repeated wording removed, no injected W26 banner |
| `/schools/apply`, invitations, workspace | `studybuddy/schools/*.blade.php` | Shared shell; school forms retain existing authorization and validated inputs |
| `/subjects`, `/subjects/{slug}` | `studybuddy/w34/*.blade.php` | W34 subject URLs remain; no migration rerun |
| `/login`, `/register`, password recovery | `auth/*.blade.php` / public layout | Shared navbar; legacy W26 injected promo removed; auth semantics untouched |
| `/dashboard`, `/my-quest` | `dashboard/*.blade.php` / public layout | Shared navbar, no injected W26 duplicate banner; learner activity untouched |
| `/profile`, `/community`, `/u/{user}` | `profile/*.blade.php` / public layout | Shared page scaffold; privacy rules unchanged |
| `/search` | `search/index.blade.php` / public layout | Shared navigation, no extra W26 promotional hero |
| `/contact` | `studybuddy/contact/*.blade.php` / public layout | Contact handling unchanged, consistent header |
| `/for-parents`, `/for-teachers`, info/legal pages | `studybuddy/info/*.blade.php`, CMS page views | Same public layout; CSS finishing layer scoped to avoid editorial regressions |
| `/admin/*` | `layouts/admin.blade.php` / admin views | Intentionally separate secure admin shell, unchanged |
| Error pages (404/500) | `resources/views/errors/*` | Standalone safe error document intentionally separate, unchanged |

## Test coverage and limits

- Source-level view map and asset references inspected; 121 Blade views in uploaded snapshot.
- JavaScript modified only in already-existing W25/W26/W27 public assets.
- New stylesheet applies after historical W-series CSS to stabilize specificity without deleting assets with unknown dependencies.
- PHP syntax, JavaScript syntax, stylesheet parse checks, manifest SHA guard, and failure rollback simulation are provided.
- **Full PHPUnit must still pass on the user's Mac** (the upload did not include Composer `vendor` dependencies); `INSTALL.command` runs the **entire suite**, records every failure, and restores all changed source files on failure.
- Visual confirmation still required for the real app in Chrome/Safari (this environment cannot navigate the local Mac's `localhost` and browser automation is restricted). This is not a claim that every browser bug is resolved.

## Verify manually on the Mac

Go through **Home → Apps → Roles → For Schools → Subjects → Search → Register → Dashboard**. Confirm the header is identical from first paint, no duplicate promotional hero appears mid-load, school hero always has readable text, galleries show separated titles, mobile menu is accessible, and all existing account actions remain functional. Also test 390px, 768px, 1024px and desktop widths and reduced-motion mode. Do not merge the branch until these checks pass. No database migrations are included in this patch.
