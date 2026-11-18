# Architecture decision log

Add entries with date, context, decision, alternatives, consequences, owner/reviewer (if agreed). Do not create fictional ownership.

## 2026-11-18 — Rewards must be server-verified

**Decision:** Game launch/iframe click is not completion proof. Math Sprint completion is graded server-side before points; credits are idempotent/capped/transactional. **Impact:** Other games need their own verified completion integration.

## 2026-11-18 — Protect canonical Head Admin

**Decision:** Head Admin controls authorized settings/rules and role grants. Arbitrary PHP/server-code execution through web admin is not allowed. **Impact:** code releases use Git and tests.

## 2026-11-18 — Preserve StudyBuddy UI during consolidation

**Decision:** Inventory overlapping CSS paths rather than deleting styles en masse. Add scoped components and role-safe navigation. **Impact:** CSS consolidation depends on manual browser QA.

## 2026-11-18 — Source of truth for docs

**Decision:** `DEVELOPERS_START_HERE.md` and `docs/handbook/` are entry points, generated inventories reflect current checkout, phase notes are historical supporting evidence. **Impact:** update docs when routes/models change.
