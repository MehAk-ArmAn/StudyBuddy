# StudyBuddy P11F — Verified Rewards & Head Admin Governance

## Problem found in existing code (historical)

- `/studybuddy/app-session/complete` accepted just an `app_slug` and issued daily `mini_app_session` points. This proved a public web app existed, **not** that a learner completed it. The button / endpoint was an exploitable reward-minting shortcut.
- The wallet used the sum of `studybuddy_point_transactions`; the dashboard/profile used `users.cosmic_points`. Signup credits and profile style purchases could produce different totals.
- Profile purchases deducted `cosmic_points` without a compensating negative ledger entry, and could race under concurrent requests.
- Math Sprint already checks five answers on the server. Existing W32 awards internal achievements but intentionally has never minted wallet points. It is the defensible first integration.
- W31 launch time is **estimated visible launcher time**, not proof of playing; never reward by heartbeat alone.

## New deterministic, capped reward rules (examples, Head Admin editable)

| Verified game | Correct required | Base points | Small elapsed bonus | Max reward/run |
|---|---:|---:|---:|---:|
| Math Sprint starter | 3 out of 5 | 4 per correct | +2 after 60 sec | 22 |
| Math Sprint explorer | 3 out of 5 | 6 per correct | +2 after 60 sec | 32 |

- At least 15 seconds between actual start and graded submission. This prevents immediate repeated form posts. Not a proof of focused attention.
- Shared global limit **60 verified points per learner per day** (Head Admin may set 1–200). The limit is hard-enforced inside a database transaction with a locked user account.
- Retries cannot earn twice: unique `event_key = w32:run:<id>` and row locking. Compromised browsers are not trusted to supply a score, a point amount, a duration or a completed flag.
- No reward merely for waiting, joining a screen, submitting fewer than 3 correct answers, or completing a generic iframe launcher. The time bonus is small and capped to avoid rewarding long screen sessions.
- Point changes and their spendable balances are written together. Old records are preserved as-is; **no automatic backfill or silent balance correction**.

## Achievements and profile cosmetics

- First Orbit: first verified awarded run.
- Perfect Numbers: score 5/5 on an awarded run, unlocks `profile_badge:math-star` (free earned badge, not a purchase).
- Explorer Master: 5/5 on explorer.
- Steady Explorer: 5 verified awarded runs, unlocks `profile_frame:math-orbit`.
- Hidden Constellation: 10 verified awarded runs. Public badge list is not shown unless the learner independently opts into public profile features.
- Hidden labels are redacted in the shop until earned. The stored source event is kept for audit. No random, cash-equivalent, purchase-or-chance based rewards.

## Head Admin scope

`/admin/control-room/rewards` — Head Admin only, with existing protected Head ID, canonical .fun email and active grant required. Controls:

- Global reward switch and daily user cap;
- Per-verified-game minimum correct, minimum elapsed, points per correct, max per run, small time bonus;
- Audited changes with previous/new values, actor ID and timestamp;
- Stats: credited runs, new minted points, earned badges;
- Links to existing platform CMS/apps/branding/schools/team control modules.

Existing manual adjustment route is changed to **Head only**, with atomic balance updates and an audit record. The dashboard **does not** accept arbitrary PHP, SQL, plugin uploads, shell commands or source-code execution. Code-level changes remain protected by Git + tests + explicit deployment.

## Out of scope / requirements for each additional game

Other mini apps will NOT mint points until there is a separately implemented, verified server completion adapter. Never send `client_points`, `client_score` or `client_seconds` to a generic reward endpoint and trust it. Server must independently validate the game-specific evidence (e.g. saved questions/answers, one-time completion event from trusted backend), check learner eligibility and atomically call a private service. For embedded third-party or external games without verifiable results, award **zero** and display an accurate message.

A learner-visible timer or an HTTP heartbeat is only an engagement estimate. It does not independently prove learning, and should not determine an unlimited reward. Don't add social leaderboards, gambling-style spins, streak penalties, cash-out promises or coercive play incentives for minors.

## Children's rights / legal gate, not a certification

Platform is potentially used by minors in the UAE and internationally, so before public deployment review:

- UAE child digital safety and Federal PDPL requirements, including children's accounts, guardian controls, deletion, notices, minimization and breach-response processes. See https://u.ae/en/information-and-services/social-affairs/children/Childrens-digital-safety and https://u.ae/en/about-the-uae/digital-uae/data/data-protection-laws
- US FTC COPPA rules for under-13 data, including the April 2025 amendments: https://www.ftc.gov/business-guidance/resources/complying-coppa-frequently-asked-questions
- UK Children's Code principles for privacy by default, data minimization and avoiding harmful nudge techniques: https://ico.org.uk/for-organisations/uk-gdpr-guidance-and-resources/childrens-information/childrens-code-guidance-and-resources/age-appropriate-design-a-code-of-practice-for-online-services/code-standards/
- Applicable Google Play Families policies if released to Play: https://support.google.com/googleplay/android-developer/answer/9893335

All rewards should remain non-transferable, not redeemable for cash or prizes, and provide no monetary value. State this in published Terms and child/guardian information. We cannot certify legal compliance without jurisdiction-appropriate legal counsel and verifying actual hosting, ads, analytics, child onboarding and deletion mechanisms. No selling behavioral reward data.

## Migration and Git

`2026_11_18_220000_create_studybuddy_reward_integrity` adds five new tables without deleting old balances. Source package deliberately runs the full Mac PHPUnit suite first, before any real MySQL migration. ACTIVATE_LOCAL requires a fresh SQL backup (not a single old one), then migrates and reruns tests. Direct `main` commit only after QA. Local private SQL dumps and `docs/LOCAL_DEV_ACCOUNTS.private.md` never enter Git.
