# StudyBuddy — single delivery plan and acceptance gates

**Canonical planning snapshot:** 18 November 2026. This supersedes the older P0/P1 *planning labels* but does not erase historical decisions. Pack results below come **only from user-provided Mac Terminal logs**. **Production deployment is unverified.**

## Current checkpoint — distinguish the evidence

| Phase / pack | Confirmed local evidence | GitHub main evidence | Important boundary |
|---|---|---|---|
| 05 Responsive foundation | 228 tests, 3,519 assertions | `3f49ae8` pushed | No paid Pro source |
| 06 React Study Space, cursors | 231 tests, 3,528 assertions | `ceefd91` pushed | Session-only notes, **not an AI assistant** |
| 07 Visual adaptive role dashboards | 234 tests, 3,545 assertions (R3) | `927136b` pushed | Not proof of production QA |
| 08 Saved onboarding and original React preview | 240 tests, 3,593 assertions (R2) | `f4d324a` pushed | Revalidate browser role flows |
| 09 My Day, in-app notices and aggregates | 247 tests, 3,645 assertions | `dc3f3bd` pushed | No real push/email delivery |
| 10 Private Resource Studio | 252 tests, 3,689 assertions | **Push NOT confirmed** | PHP/route logs show `/resources`; installer printed mislabeled `PACK 09 INSTALLED` despite P10 backup; browser QA still pending |

**Current engineering baseline:** `main` confirmed through `dc3f3bd`; Pack 10 installed locally per log. **Before any code pack, read `git status`, verify Pack 10 is committed if appropriate, and protect teammate changes.** Do not re-run migrations or seeders merely to install documentation.

## Delivery schedule, one phase at a time

| Order | Delivery | Existing foundation | New scope and main UI refs | Backend and acceptance gate |
|---|---|---|---|---|
| P11 / Admin Foundation | Head Admin + team permissions + school admin governance | `/admin/control-room`; existing Users/Admin/school modules | Sidebar 4/2, Data Table 6, Forms 2, Settings Form 1, Wizard 2/4/5/6 | One canonical `admin@studybuddy.fun` after safe reconciliation, immutable root principal, granular policy checks, separate school tenancy, logs, no short default password; 2FA plan; 403 tests |
| P12 / Control Intelligence | operational and educational metrics | Recorded events, existing stats | Analytics 5/9, Dashboard 11, List 3, Monitoring 5/6/7/8/9 | Real aggregation, queued job instrumentation, status probes, redaction, honest empty states, restricted infra actions |
| P13 / Connected Community & Schools | school channels, DMs, mentions | `/community`, school verified-memberships | Chat 2/5, Comments 4, Sidebar 2; notices | Scoped threads, private attachments, disclosed logged safeguarding, minors protected, no anonymous adult–minor DMs, pagination/read receipts tests |
| P14 / Help, Tickets & Feedback | searchable help desk and triage | existing contact form/message admin | Support 1/2, Forms 10, Feedback 1/2/3/5/6, List 11 | CSRF/rate limits, optional screenshots with user review, respondent privacy, real ticket workflow, aggregate insights only |
| P15 / Collaboration & Review | teacher materials/annotations + staff tasks | P10 resources, P13 school links | Editor 4, Comments 2/6, Kanban 2/4, App Shell 4 | Resource version anchors, authorized mention targets, board status audit, attachments private, simultaneous editing conflict policy |
| P16 / Scheduling | bookable parent–teacher and school meetings | P09 personal plans | Scheduling 1/3/4/7, Forms 9 | Explicit availability rules, slots/time zones, attendee authorization, conflict transactions, cancellation and notifications |
| P17 / Learning Universe | subject-world polish and discovery | 28-category taxonomy, W34 subjects, web game launcher | Arc List, Filtering 1, carousel/animated visual selections | Genuine published game/category records, no dead links, correct LCP and mobile performance |
| P18 / Billing (hold until approved) | subscription and invoices | None verified as live gateway | Billing 1/2/4/5, Wizard 3, Paywall 4/5, Forms 3/12 | Approved product pricing, gateway webhook truth, entitlement source, proration server calculation, invoices and refunds, legal review, **no fake charges** |
| P19 / Integrations (hold until secured) | APIs, OAuth and webhooks | React workspace, Laravel backend | Integrations 1/2/3/4/5, Authentication 2 | Secret hashing/encryption, hashed API tokens, scopes, OAuth PKCE/redirect validation, event retry idempotency, security review |
| P20 / AI & Accessibility (conditional) | voice composer + real tutor | P06 Study Space | AI Chat 8, App Shell 9, optional Waveform Player | Consent, local transcript availability fallback, review-before-send, cost/privacy approval, real provider and moderation; no invented AI replies |
| P21 / Polish + School Pilot Gate | final visual/a11y/testing/release | All previous | Selected free React Bits / ReactDrops artwork and animations, app/mobile shell | Cross-role test matrix, tenant isolation, privacy/counsel review, backups, accessibility, CDN/asset checks, real school pilot sign-off |

## Every phase must pass these gates

1. **Source gate:** User's latest `main` state and uncommitted changes inspected. No forced checkout, no branch creation, no destructive overwrite. For parallel team work, coordinate file ownership and review even if delivering directly to main.
2. **Contract gate:** DB/query/API/schema changes explicit; role permission table and tenancy checked; no migrations automatically invoked by docs/visual installers.
3. **Security gate:** authenticated endpoints, server-side policies, CSRF/rate limits, admin MFA readiness, cross-school object ID tests, safe upload/download controls, no secrets or user data in Git.
4. **Quality gate:** PHP and bundled JS lint, compiled Blade PHP lint, full existing PHPUnit + new role/ownership tests, browser function tests at 390/768/1440, keyboard/contrast/reduced-motion checks.
5. **Product gate:** every button saves a real result; charts bind real records; empty state when no data; no fake live data, billing, transcripts, notifications, or uptime.
6. **Release gate:** Git diff reviewed, source backup, data backup before schema work, rollback plan, manual browser approval, **then** scoped commit/push to main. Live deployment is a separate decision.

## Release evidence template

`Phase / baseline commit / changed paths / DB & permission impact / number of tests + assertions / browser roles tested / open issues / screenshots / rollback / reviewer / merged main commit / deployed? (yes/no)`

Never record "installed" simply because the component was requested or documented. See `COMPONENT_SHIPPING_LEDGER.md`.
