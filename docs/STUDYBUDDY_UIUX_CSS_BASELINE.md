# StudyBuddy — CSS References & Consolidation Baseline

This inventory was generated from the **local source at installation**. A reference is not proof a stylesheet loads on every route.

- Public Blade layout: **43** distinct referenced assets
- Admin Blade layout: **11** distinct referenced assets
- Neither list was deleted, consolidated, disabled, or replaced by this patch.

## Consolidation plan

1. Keep existing assets and inspect the computed cascade in browser (actual loaded network files and selectors).
2. Map current brand tokens for colors, fonts, spacing, radii, focus and reduced motion.
3. Scope shared components by shell (`public`, `school`, `admin`) while retaining identical design language.
4. Port individual modules one at a time with screenshot and interaction comparison at 390px / 768px / 1440px.
5. Remove a legacy sheet only after all consumers are proven migrated, with full test and rollback.

## Public layout references

- `assets/css/experience.css`
- `assets/css/sb-auth-role-ui.css`
- `assets/css/sb-final-ui-safety-polish.css`
- `assets/css/sb-phase3-quest-vault.css`
- `assets/css/sb-phase4-command-center.css`
- `assets/css/sb-phase5-admin-experience.css`
- `assets/css/sb-phase6-final-platform.css`
- `assets/css/sb-premium-fixes.css`
- `assets/css/sb-structure-publish-polish.css`
- `assets/css/site.css`
- `assets/css/studybuddy-accessibility-hardening.css`
- `assets/css/studybuddy-advanced-shell.css`
- `assets/css/studybuddy-final-no-error-polish.css`
- `assets/css/studybuddy-global-premium-polish.css`
- `assets/css/studybuddy-home-vibe-upgrade.css`
- `assets/css/studybuddy-independent-learner.css`
- `assets/css/studybuddy-living-platform.css`
- `assets/css/studybuddy-mailing-list.css`
- `assets/css/studybuddy-page-consistency.css`
- `assets/css/studybuddy-shell-force-override.css`
- `assets/css/studybuddy-universe-v4.css`
- `assets/css/studybuddy-v10-1-play.css`
- `assets/css/studybuddy-v10-public.css`
- `assets/css/studybuddy-v5-atmosphere.css`
- `assets/css/studybuddy-v6-reactivity.css`
- `assets/css/studybuddy-v7-clarity.css`
- `assets/css/studybuddy-welcome-confetti.css`
- `assets/studybuddy-p09/pulse.css`
- `assets/studybuddy-reactbits/sb-rb-slosh-official.css`
- `assets/studybuddy-w13/w13-style.css`
- `assets/studybuddy-w13/w13.css`
- `assets/studybuddy-w14/parity.css`
- `assets/studybuddy-w19/w19-style.css`
- `assets/studybuddy-w23/consistency.css`
- `assets/studybuddy-w24/studybuddy-gooey.css`
- `assets/studybuddy-w24/universe.css`
- `assets/studybuddy-w25/gooey.css`
- `assets/studybuddy-w25/shell.css`
- `assets/studybuddy-w25/studybuddy-visuals.css`
- `assets/studybuddy-w26/studybuddy-w26.css`
- `assets/studybuddy-w27/stable.css`
- `assets/studybuddy-w29/header.css`
- `assets/studybuddy-w36/effects.css`

## Admin layout references

- `assets/css/admin.css`
- `assets/css/studybuddy-admin-control-room.css`
- `assets/css/studybuddy-admin-simple.css`
- `assets/css/studybuddy-admin-targeted-pages.css`
- `assets/css/studybuddy-universe-v4.css`
- `assets/css/studybuddy-v5-atmosphere.css`
- `assets/css/studybuddy-v6-reactivity.css`
- `assets/studybuddy-w13/w13-style.css`
- `assets/studybuddy-w13/w13.css`
- `assets/studybuddy-w19/w19-style.css`
- `assets/studybuddy-w25/shell.css`

## This patch

- Adds a Head Admin-only Rewards navigation entry using the **same** `StudyBuddyRewardIntegrity::isHead` authorization predicate as the Rewards controller.
- Moves P11F rewards inline styles to the existing `public/assets/studybuddy-p11f/rewards.css` asset, with scoped `--sb-rw-*` tokens.
- Leaves existing home/school/profile layouts and all database records untouched.
- The prior public CSS count may remain a MEDIUM audit finding until safe staged consolidation is complete.
