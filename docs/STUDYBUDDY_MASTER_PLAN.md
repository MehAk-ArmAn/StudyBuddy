# StudyBuddy — product, experience and implementation master plan

**Date:** 2026-11-18 · **Repository:** `MehAk-ArmAn/StudyBuddy` · **Brand:** *Learn. Play. Grow. Your Way.*

This is the canonical **shared intent** for every future ChatGPT/Claude/Cursor/code-contributor session. It captures user decisions and assistant proposals, **distinguishing those states from working code**. Do not treat a roadmap item as deployed. See `PHASES_AND_RELEASE_GATES.md` for verified-by-log checkpoint.

## North-star experience

One learning ecosystem linking genuine educational minigames, role-tailored dashboards, classes, parents, teachers, approved schools, platform administrators, a safe community, and tools for learning, resources, support and operations. UI should feel playful **for learners** and calm/professional **for staff and admins**, within one consistent brand and set of navigation/layout rules.

### Users and their home destinations

| Actor | Canonical current/future home | Essential product jobs | Not allowed |
|---|---|---|---|
| Guest | `/`, `/apps`, `/roles`, `/for-schools`, `/community` | Browse public profiles and real published games; support/feedback as permitted | Protected member content, DMs, child data |
| Student | `/dashboard`, `/my-quest`, `/study-space`, `/getting-started`, `/my-day`, `/resources` | Games, verified assigned work, private resources, age-appropriate relationships | Unverified adult DMs; other children's data |
| Independent learner | `/dashboard`, `/resources`, `/my-day` | Self-directed goals, private notes and progress | School role escalation |
| Parent/guardian | `/dashboard`, `/my-day`, permitted school channels | Approved child links, authorized updates, meeting bookings | Other families' information or a child's private credentials |
| Teacher | `/dashboard`, school workspace and classes | Verified class roster, assignments, review, feedback | Data of other schools or unrelated children |
| School manager | `/schools/{organization}/workspace` | School onboarding, staff invitations, settings, class spaces, membership and scheduling | Another tenant's records or PCL admin capabilities |
| PCL team admin | `/admin/control-room` | Task-specific content, support, finance, operations or school approval | Root account controls without Head Admin grant |
| Head Admin | `/admin/control-room` | Global platform governance, PCL role grants, school verification, audit/break-glass | Covert monitoring, impossible-to-recover identity lifecycle |

## Information architecture — the user-visible navigation

**Public:** Home / Apps / Subjects / Learning Worlds / For Schools / Community / Help / About/Policies. Preserve current CMS content, external app launch behavior, real app links, and working account forms; new nav destinations must not show as fully live until routed and tested.

**Learner:** Overview / Explore & Play / My Quest / My Day / My Resources / Study Space / Community / Settings. Use icon rail only when useful; mobile has one clear primary nav and a More sheet.

**Teacher:** Classes / Assign Work / Progress / Resource Reviews / School Team / Meetings / Notifications / Settings. Only approved school membership unlocks school actions.

**Parent:** Connected Learners / Approved Updates / Messages (if enabled) / Parent–Teacher Meetings / My Day / Account. Child authorization and school tenancy checks apply to every view.

**School Admin:** Overview / Members & Staffing / Classes / School Resource Hub / Channels / Scheduling / Reports / Feedback / School Settings. Admin disclosures show oversight and retention policies.

**PCL Control Room:** Overview / Schools / Users & Role Grants / Apps & CMS / Moderation & Safeguarding / Support & Feedback / Tasks / Analytics / Security & Audit / Operations / Integrations / Billing (when live) / Settings. Render only what each staff role can do, and enforce every permission server-side.

## Core experiences and truthful scope

- **Learning:** 28-category educational taxonomy; games shown only when truly published and playable; no fake certificates, official curricular approval, test data posing as users, or unearned metrics.
- **Visual brand:** existing 5 wide cosmic/bold banner images, 44 archived WebP illustrations, subject mascots, blue/violet/cyan accents, animated atmosphere only where appropriate. Heavy canvas effects never obscure forms/tables or impair device performance.
- **Unified communication:** `/community` currently represents public member discovery, **not** unrestricted live group chat. Plan role-allowed DMs, school-managed class channels, safe @mentions, notifications and private attachments. School leaders manage local channels; any PCL safety reviews must be declared, justified and auditable, never covert.
- **Personal productivity:** saved onboarding (P08), My Day/private plans and in-app notices (P09), resource notes/private uploads (P10). Do not conflate these with real school calendar booking, cloud AI, email/push or cross-user resource sharing.
- **Admin governance:** one canonical Head Admin identity `admin@studybuddy.fun` **after safe consolidation**, separately assigned team/admin capabilities; school approval processes; creator-only test accounts clearly tagged and excluded from production analytics. A regular DB administrator can ultimately modify anything: "nondeletable" means protection against **normal UI/API** delete/demote paths plus recovery logs and off-app backups.
- **Business modules:** plans, invoices, payment recovery, entitlements, OAuth and webhooks are selected future capabilities; keep them behind feature flags until real approved providers, compliance checks, and QA exist.
- **Support:** searchable help articles, ticket submissions, feedback widgets with optional reviewed screenshots, star ratings/reasons, 0–10 recommendation surveys, staff triage, analytics. Never fabricate support leaderboards or sentiment certainty.

## Cross-team planning (suggested owner specialties, not security role grants)

| Collaborator mentioned in PCL planning | Historical focus | Example delivery, confirm actual access individually |
|---|---|---|
| Mehak | Creative / product | Design system, UX copy, visual asset curation, QA of role flows |
| Sahil | Backend | Laravel auth/roles, school data isolation, persistence and migrations |
| Mobeen | API / integrations | Provider boundaries, webhook signatures, API documentation and reliability |
| Hamdan | Gameplay | Game catalogue accuracy, web launcher QA, child-friendly learning loops |

No names, job titles or chat planning confer Head Admin power. The Head Admin approves specific accounts, scopes, emergency access and audits.

## Design principles — one system, not 100 stacked components

1. **Functional content before visual wrappers**. Each component has exactly one accountable purpose and one backing data contract; avoid overlapping card stacks, duplicate navs, and excessive visual noise.
2. **Age/role-appropriate mode**. Mascot and playful rewards only where appropriate; school and PCL screens prioritize tables, legibility, filters, and quick actions.
3. **Reuse a 12-column responsive layout** with dedicated mobile patterns. The app rail + secondary panel is primarily **staff/admin**; learner mobile should not have a desktop flyout rail.
4. **Tokens:** surface/space/indigo/purple/cyan with accessible Light/Dark/Auto. Keep existing CSS tokens and class names where possible; introduce new tokens only by additive, namespaced integration.
5. **Motion governance:** reduced-motion honors OS setting; WebGL/Canvas lazy loaded; cursor effects opt-in and desktop-only; no new globally injected scripts for a single screen.
6. **Real visibility:** data origin, timestamp, filters, scope, empty states and whether a metric is measured or estimated must be explicit.
7. **Preserve existing code/records:** never rebuild production DB or replace live CMS, routes, user assets, school records or teammates' edits for an interface refresh.

## Follow the source docs

- `SCREEN_BY_SCREEN_COMPONENT_BLUEPRINT.md`: exact screen layout and placement, including routes and responsive behavior.
- `REACT_COMPONENT_PLACEMENT_MATRIX.md`: exhaustive selected blocks/effects and where they should go.
- `ROLE_PERMISSIONS_AND_SAFEGUARDING.md`: trust, role matrix, Head Admin, school privacy and communications.
- `ADMIN_COMMUNITY_SCHOOLS_ARCHITECTURE.md`: workflows and feature contracts.
- `PHASES_AND_RELEASE_GATES.md`: confirmed phases and next acceptance gates.
- `SOURCE_CODE_MAP.md`: actual available source map and current/historical boundaries.
- `INTEGRATION_BILLING_SUPPORT_PLAN.md`: future high-risk backends and dependencies.
- `REACT_LICENSE_AND_INSTALLATION.md`: official registry vs legal original implementations.
- `COMPONENT_SHIPPING_LEDGER.md`: never call a Pro block installed based only on this inventory.
- `AGENT_CONTEXT_AND_HANDOFF.md`: concise instructions for every new chat and contributor.
- `DECISIONS_AND_OPEN_QUESTIONS.md`: decisions, unsettled approvals and safe implementation gates.
- `MEHAK_PROJECT_REACT_BITS_REACTDROPS_ACTUAL_COMPONENTS_MASTER_2026.md`: preserved source policy for actual, licensed integration of free components.
