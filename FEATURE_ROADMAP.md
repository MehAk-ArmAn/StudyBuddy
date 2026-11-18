# StudyBuddy — Shared Feature Roadmap

<!-- STUDYBUDDY_2026_MASTER_PLAN_BEGIN -->
## Current execution roadmap (November 2026)

The original P0/P1 list below is **historical backlog** from 17 November; some items have since been implemented and tested locally. The current authoritative status, sequence and role-level acceptance checks are in [`docs/PHASES_AND_RELEASE_GATES.md`](docs/PHASES_AND_RELEASE_GATES.md), with the full product intentions in [`docs/STUDYBUDDY_MASTER_PLAN.md`](docs/STUDYBUDDY_MASTER_PLAN.md).

As of the user-reported logs: Packs 05–09 passed tests and were pushed to `main` (last confirmed `dc3f3bd`), and Pack 10 Resource Studio passed 252/252 local tests but has no confirmed push. **No production release is claimed.** Next work: Head Admin/school RBAC, monitoring, safe community, help/feedback, collaborative teaching, scheduling, integrations, billing, optional voice AI. Direct guarded `main` commits are the current requested workflow; coordinate simultaneous contributors.
<!-- STUDYBUDDY_2026_MASTER_PLAN_END -->


> **Status:** planning checklist · **Updated:** 17 November 2026  
> **Repository:** `MehAk-ArmAn/StudyBuddy` · **Suggested location:** `docs/FEATURE_ROADMAP.md`

This is a **proposed work queue**, not a statement that features are missing or completed. The development team should verify existing code and live behavior before claiming an item is done. Keep completed items checked only after a review/QA pass.

## P0 — Product foundation

- [ ] **Homepage refresh** — premium, cohesive, responsive UI and meaningful microinteractions; preserve working content/routes.
- [ ] **Admin CMS** — protected admin access; edit homepage text, imagery, buttons, links, section order, navigation, footer, and SEO without code changes.
- [ ] **Academic category system** — use the established **28-category** StudyBuddy taxonomy, assigning one primary category per game plus optional topic/stage tags. Only expose populated categories publicly.
- [ ] **Game library & discovery** — accurate game cards, search, subject/category filters, suitable age/stage information, and working play/details links.
- [ ] **Visual themes & interaction polish** — site-wide theming (not just a dropdown), accessible animations, and responsive mobile/desktop states.
- [ ] **Core platform QA** — test navigation, loading/error states, accessibility, security, privacy pages, mobile performance, and existing functionality before each release.

## P1 — Learning experience expansion

- [ ] **Educational game integration** — link existing StudyBuddy games and future titles with accurate metadata, playable routes, and consistent branding.
- [ ] **Student accounts & profiles** *(proposal; requires approval)* — secure sign-in, preferences, and user-specific dashboard, with age-appropriate privacy design.
- [ ] **Learning progress** *(proposal; requires approval)* — accurate scores, milestones/achievements, streaks and progress history; avoid misleading performance claims.
- [ ] **Parent & teacher experience** *(proposal; requires approval)* — clearer discovery by learning goals, age/stage, and honest educational descriptions; avoid exposing private child data.

## P2 — Future enhancements (discuss before implementation)

- [ ] Evaluate useful personalization and recommendations only after there is a clear learning need and an agreed privacy design.
- [ ] Improve portfolio/store-preview integrations only after screenshots and feature claims match the live product.

## Team workflow — one shared repository

- [ ] Assign **one owner** to each feature or pull request; agree on the scope before coding.
- [ ] Create a short-lived branch per task (for example, `feature/game-library-filters`); **do not share one active work branch** across unrelated tasks.
- [ ] Open a pull request into `main` for review; include screenshots/behavior notes and mention any database or environment changes.
- [ ] Require checks/tests and another team member's review before merging where practical.
- [ ] Pull/rebase recent changes, resolve conflicts carefully, and **do not force-push to shared branches or overwrite others' work**.
- [ ] Keep secrets, production credentials and user/student data **out of Git**. Protect existing assets, routes, schema and deployment settings.
- [ ] Update this checklist after merge **and** verification; link the relevant PR/issue next to the completed task.

## Suggested first team sync

- [ ] Confirm which roadmap items already exist in `main` and which are truly outstanding.
- [ ] Decide P0 scope and identify any dependencies between frontend, backend, CMS and database changes.
- [ ] Assign owners, reviewers and priority to the first small batch of tasks.

**Design implementation note:** For React-compatible sections, examine and integrate actual documented React Bits / ReactDrops components where they fit, with license, performance, reduced-motion and accessibility checks. For non-React sections, use an explicitly labelled native adaptation if appropriate—do not claim React components are directly installed in a non-React UI.

**Important:** Nothing in this file authorizes a deployment, database migration, or overwriting an existing contributor's work.
