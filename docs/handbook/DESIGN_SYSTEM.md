# UI/UX consistency contract

The goal is one StudyBuddy experience across public pages, learner profile, school workspaces and Control Room, **without blindly replacing existing pages**.

- Use `resources/views/layouts/app.blade.php` and `resources/views/layouts/admin.blade.php` as entrypoints for shared shells; check route-guarded nav items in controllers too.
- Prefer shared typography/color/spacing conventions from existing assets; do not create another global override layer for each module.
- `docs/STUDYBUDDY_UIUX_CSS_BASELINE.md` records the CSS layering audit; historical report: public 43 unique CSS references and admin 11 unique. Counts are inventory, not proof of a broken experience.
- P11F Rewards page had 14 inline style attributes converted into scoped classes by UIUX Fix 01, and Admin received Head-only Rewards navigation. Verify current views rather than assuming the patch is installed elsewhere.
- Before changing CSS: record screenshots at 390px / 768px / 1440px, light/dark if offered, keyboard focus and mobile overflow; make a small scoped change and compare.
- For child-facing screens: readable contrast, sensible motion, visible progress, no misleading time pressure or infinite-scroll incentives.

**Acceptance:** Screenshots + manual role journey + PHPUnit, not only `view:cache` and static lint.
