# React Bits / ReactDrops — StudyBuddy placement matrix

**Updated:** 18 November 2026 · **Source of truth for selected UI placement, not proof of installation.**

> The repo contains a shadcn-ready `studybuddy-reactbits/pro-ui/components.json` with `@reactbits-starter` and `@reactbits-pro` registry aliases using `${REACTBITS_LICENSE_KEY}`. **This does not grant Pro access.** The user has not confirmed a valid paid license; the UI selections below are design targets. Any shipped alternatives are original or separately licensed. The exact `npx shadcn@latest add ...` commands may be run only after a valid license is available and in the correct React workspace, never at Laravel root by mistake.

## Placement rules

1. **Component ≠ feature.** A React view needs a Laravel route, real server data, authorization, validation, persistence, error/empty states, and tests before marking functional.
2. **One main UI pattern per surface.** School/Admin share design tokens but not data permissions; mobile has one primary navigation scheme; page-specific effects stay scoped.
3. **Do not overload learner pages** with admin monitoring/billing; keep analytics aggregate, never identify minors in public leaderboards.
4. **Accessibility/performance:** keyboard and screen-reader labels, reduced-motion, responsive grid, bundle size budget, focus states, color contrast; all interactive visualizations have textual alternatives.

## Exact Pro / starter references — 94 unique selected blocks

| Requested ID | Phase | Best StudyBuddy home | Required backend / boundary | Status |
|---|---|---|---|---|
| `@reactbits-starter/celebration-cursor-tw` | P06/P21 | public/learner pages; optional opt-in — Optional learning-completion celebration; no global click confetti | reduced motion & touch safe | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-starter/smooth-cursor-tw` | P06/P21 | public/learner pages; optional opt-in — Public desktop marketing only; disabled for reduced motion and touch | reduced motion & touch safe | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-starter/user-cursor-tw` | P06/P21 | public/learner pages; optional opt-in — Only future opted-in live collaboration; not enabled by default | reduced motion & touch safe | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/onboarding-1` | P08/P11 | /getting-started; school setup — Profile personalization with live preview and private data boundaries | role safe, persisted progress | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/onboarding-2` | P08/P11 | /getting-started; school setup — Six-step learner/guardian/school enrollment flow | role safe, persisted progress | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/onboarding-3` | P08/P11 | /getting-started; school setup — Getting-started checklist driven by completed activities | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/onboarding-4` | P08/P11 | /getting-started; school setup — Personalization use-case picker; age/role-aware | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/onboarding-5` | P08/P11 | /getting-started; school setup — School invite colleagues, permission-checked | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/onboarding-6` | P08/P11 | /getting-started; school setup — Only after verified actual setup completion | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/onboarding-7` | P08/P11 | /getting-started; school setup — School/workspace creation steps using existing protected flows | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/wizard-7` | P08/P11 | /getting-started; school setup — Accessible single-question onboarding with server-side checks | role safe, persisted progress | Selected · not official Pro installed |
| `@reactbits-pro/notifications-2` | P09/P13 | /my-day; global notification bell — Transient confirmation toasts, no fake event stream | saved prefs, scoped events | Selected · not official Pro installed |
| `@reactbits-pro/notifications-3` | P09/P13 | /my-day; global notification bell — Per-user delivery preferences; real settings persistence needed | saved prefs, scoped events | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/notifications-4` | P09/P13 | /my-day; global notification bell — Teacher/Admin notification inbox with server-authorized activity data | saved prefs, scoped events | Selected · not official Pro installed |
| `@reactbits-pro/notifications-6` | P09/P13 | /my-day; global notification bell — Role-aware bell popover; read from real notification feed only | saved prefs, scoped events | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/editor-2` | P10 | /resources; /study-space — Teacher/Admin Markdown editor with write/preview, sanitized server output | private storage and sanitization | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/editor-5` | P10 | /resources; /study-space — Article/lesson reader and accessible outline | private storage and sanitization | Selected · not official Pro installed |
| `@reactbits-pro/file-manager-1` | P10 | /resources; /study-space — Admin assets explorer if protected storage access exists | private storage and sanitization | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/file-manager-2` | P10 | /resources; /study-space — Admin media grid with verified type filters | private storage and sanitization | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/file-manager-3` | P10 | /resources; /study-space — Admin resource detail panel; avoid leaking private files | private storage and sanitization | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/file-manager-4` | P10 | /resources; /study-space — Admin verified upload manager; existing storage backend | private storage and sanitization | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-starter/waveform-player-tw` | P10 | /resources; /study-space — Audio learning/library resource with permission checks | private storage and sanitization | Selected · not official Pro installed |
| `@reactbits-pro/app-shell-4` | P11 | /admin/control-room; school workspace — Authorized admin/teacher resource workspace: three-column context panel | RBAC, Head Admin safe | Selected · not official Pro installed |
| `@reactbits-pro/app-sidebar-2` | P11 | /admin/control-room; school workspace — Desktop PCL/School nav | RBAC, Head Admin safe | Selected · not official Pro installed |
| `@reactbits-pro/app-sidebar-4` | P11 | /admin/control-room; school workspace — Admin and school contextual console | RBAC, Head Admin safe | Selected · not official Pro installed |
| `@reactbits-pro/authentication-1` | P11 | /login; /admin/login; /schools/invitations/{token} — Sign-in screen presentation, existing Laravel auth | current Laravel auth; no spoofed SSO | Selected · not official Pro installed |
| `@reactbits-pro/authentication-12` | P11 | /login; /admin/login; /schools/invitations/{token} — School invitation acceptance (existing token verifier) | current Laravel auth; no spoofed SSO | Selected · not official Pro installed |
| `@reactbits-pro/authentication-2` | P11 | /login; /admin/login; /schools/invitations/{token} — Only when real SSO provider is configured | current Laravel auth; no spoofed SSO | Selected · not official Pro installed |
| `@reactbits-pro/data-table-6` | P11 | /admin/control-room; school workspace — Users/schools/admin directory and queue | RBAC, Head Admin safe | Selected · not official Pro installed |
| `@reactbits-pro/forms-2` | P11 | /admin/control-room; school workspace — Head Admin: create staff admins/test users/school account roles | RBAC, Head Admin safe | Selected · not official Pro installed |
| `@reactbits-pro/mobile-1` | P11 | global / role-specific navigation — Phone-only contextual nav; do not duplicate existing global nav | touch accessible | Selected · not official Pro installed |
| `@reactbits-pro/mobile-2` | P11 | global / role-specific navigation — Teacher/admin quick action FAB, tap-target and role gated | touch accessible | Selected · not official Pro installed |
| `@reactbits-pro/mobile-4` | P11 | global / role-specific navigation — Mobile menu sheet; choose ONE nav pattern | touch accessible | Selected · not official Pro installed |
| `@reactbits-pro/settings-form-1` | P11 | /admin/control-room and /schools/{organization}/workspace — Settings page + sticky Save/Discard | roles, settings, dirty form state | Selected · not installed |
| `@reactbits-pro/wizard-2` | P11 | /getting-started; school setup — Full-screen progress wizard | resumable role-safe onboarding | Selected · not installed |
| `@reactbits-pro/wizard-4` | P11 | school onboarding / internal setup — Service picker + live summary | valid selectable services only | Selected · not installed |
| `@reactbits-pro/wizard-5` | P11 | /getting-started; school setup — Review + confirm | server-validation and explicit confirmation | Selected · not installed |
| `@reactbits-pro/wizard-6` | P11 | admin/school provisioning wizard — Running step with task states | actual queued/success/error states; never fake | Selected · not installed |
| `@reactbits-pro/forms-12` | P11/P14 | /admin/control-room; school setup — Optional procurement/billing only if real quotes exist | server authorization | Selected · not official Pro installed |
| `@reactbits-pro/forms-3` | P11/P14 | /admin/control-room; school setup — Booking order breakdown only if checkout backend exists | server authorization | Selected · not official Pro installed |
| `@reactbits-pro/forms-4` | P11/P14 | /admin/control-room; school setup — Admin CMS property editor with authenticated write actions | server authorization | Selected · not official Pro installed |
| `@reactbits-pro/forms-9` | P11/P14 | /admin/control-room; school setup — Schools timetable settings only (not arbitrary opening hours) | server authorization | Selected · not official Pro installed |
| `@reactbits-pro/analytics-11` | P12 | /dashboard; /admin/control-room; school workspace — Platform-admin cohort retention, only with consented aggregates | real verified aggregates, role scoping | Selected · not official Pro installed |
| `@reactbits-pro/analytics-5` | P12 | /dashboard; /admin/control-room; school workspace — School/admin aggregate data | real verified aggregates, role scoping | Selected · not official Pro installed |
| `@reactbits-pro/analytics-9` | P12 | /dashboard; /admin/control-room; school workspace — School operational activity and platform admin | real verified aggregates, role scoping | Selected · not official Pro installed |
| `@reactbits-pro/dashboard-11` | P12 | /dashboard; /admin/control-room; school workspace — School staffing/workload | real verified aggregates, role scoping | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/dashboard-3` | P12 | /dashboard; /admin/control-room; school workspace — Student/independent dashboard mixed-card reference | real verified aggregates, role scoping | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/list-3` | P12 | /admin/control-room/operations — Restricted security/audit center | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-pro/monitoring-5` | P12 | /admin/control-room/operations — Head Admin / infra roles | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-pro/monitoring-6` | P12 | /admin/control-room/operations — Platform health history | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-pro/monitoring-7` | P12 | /admin/control-room/operations — Platform services/storage/queues | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-pro/monitoring-8` | P12 | /admin/control-room/operations — Platform health/latency | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-pro/monitoring-9` | P12 | /admin/control-room/operations — Queue operations | instrumentation and redacted audit events | Selected · not official Pro installed |
| `@reactbits-starter/particle-chart-tw` | P12 | /dashboard; /admin/control-room; school workspace — Optional performance chart for aggregate verified data | real verified aggregates, role scoping | Selected · not official Pro installed |
| `@reactbits-starter/simple-graph-tw` | P12 | /dashboard; /admin/control-room; school workspace — Learner progress or admin aggregate trends | real verified aggregates, role scoping | Selected · not official Pro installed |
| `@reactbits-pro/chat-2` | P13 | /messages; school channels — Approved staff and role-permitted user messaging | consent and participant restrictions | Selected · not official Pro installed |
| `@reactbits-pro/chat-5` | P13 | /schools/{organization}/workspace/channels — School-managed classes, staff and organization channels | verified school membership; disclosed audit | Selected · not official Pro installed |
| `@reactbits-pro/comments-4` | P13 | /community; school classes and staff channels — Comment composer with @mentions | only permitted mention targets | Selected · not installed |
| `@reactbits-pro/feedback-1` | P14 | global feedback entry; /feedback — In-app feedback with screenshots | consent before screenshot, redact personal data | Selected · not installed |
| `@reactbits-pro/feedback-2` | P14 | /feedback; school feedback — 0–10 recommendation survey | accessible 0–10 form and grouped aggregates | Selected · not installed |
| `@reactbits-pro/feedback-3` | P14 | /feedback — Star rating + reason chips | real persisted reasons and scores | Selected · not installed |
| `@reactbits-pro/feedback-5` | P14 | /admin/control-room/feedback — Feedback triage inbox | staff assignment, audit, status | Selected · not installed |
| `@reactbits-pro/feedback-6` | P14 | /admin/control-room/feedback/insights — Theme and sentiment insights | label estimated sentiment; source-backed aggregates | Selected · not installed |
| `@reactbits-pro/forms-10` | P14 | /feedback; /help; /admin/control-room/feedback — Public community feedback + school service/experience surveys | real ticket/survey backend | Selected · not official Pro installed |
| `@reactbits-pro/list-11` | P14 | /admin/control-room/support — Platform support quality dashboard | support cases only, never minors | Selected · not official Pro installed |
| `@reactbits-pro/support-1` | P14 | /help — Help center with search | published support articles and search | Selected · not installed |
| `@reactbits-pro/support-2` | P14 | /help/new; /contact — Ticket form with suggested articles | real ticket, privacy, authorization | Selected · not installed |
| `@reactbits-pro/comments-2` | P15 | /resources/{id}/review; teacher lesson review — Inline annotation comments rail | document version anchors, access checked | Selected · not installed |
| `@reactbits-pro/comments-6` | P15 | school teacher review / visual lesson planning — Canvas pins + notes rail | normalized coordinates, private attachments | Selected · not installed |
| `@reactbits-pro/editor-4` | P15 | /resources; /community; school workspace — Teacher content suggestions review when workflow exists | tenant ownership and review | Selected · not official Pro installed |
| `@reactbits-pro/kanban-2` | P15 | school workspace tasks; support ops — Collapsible swimlane board | role-scoped board and audit | Selected · not installed |
| `@reactbits-pro/kanban-4` | P15 | school tasks; /admin/control-room/tasks — Board + synced detail panel | authorized changes, status and event history | Selected · not installed |
| `@reactbits-pro/scheduling-1` | P16 | /my-day; school calendar — Teacher/school calendar with real events | true timeslots & conflict rules | Selected · not official Pro installed |
| `@reactbits-pro/scheduling-3` | P16 | /schools/{organization}/calendar; /my-day — New schedule form with attendees | verified participants, conflict checks | Selected · not installed |
| `@reactbits-pro/scheduling-4` | P16 | teacher assignments; /my-day — Deadline presets | timezone-aware persisted deadlines | Selected · not installed |
| `@reactbits-pro/scheduling-7` | P16 | /my-day; school calendar — Only if bookable sessions/slot backend exists | true timeslots & conflict rules | Selected · not official Pro installed |
| `@reactbits-pro/filtering-1` | P17 | /apps; subject discovery — Apps library facets; adapt product swatches to subject tags | real catalogue taxonomy | Selected · not official Pro installed |
| `@reactbits-starter/arc-list-tw` | P17 | /apps; subject discovery — Website /apps discovery; academic topics | real catalogue taxonomy | Selected · not official Pro installed |
| `@reactbits-pro/billing-1` | P18 | /admin/control-room/billing; school billing — Billing overview + invoice ledger | verified invoices, tenant ownership, payment provider | Selected · not installed |
| `@reactbits-pro/billing-2` | P18 | /admin/control-room/billing; /schools/{organization}/billing — Plan manager + entitlement cards | entitlements only from actual subscriptions | Selected · not installed |
| `@reactbits-pro/billing-4` | P18 | /admin/control-room/billing/recovery — Failed-payment recovery queue | real gateway events, manual retry gating | Selected · not installed |
| `@reactbits-pro/billing-5` | P18 | /schools/{organization}/billing/change — Plan change + proration preview | provider-priced immutable quote; no invented charges | Selected · not installed |
| `@reactbits-pro/paywall-4` | P18 | school billing — Only when genuine user usage caps exist | only with live entitlement service | Selected · not official Pro installed |
| `@reactbits-pro/paywall-5` | P18 | school billing — Only when verified trial billing state exists | only with live entitlement service | Selected · not official Pro installed |
| `@reactbits-pro/wizard-3` | P18 | school subscription wizard — Plan step + invoice preview | real invoice estimate, disabled until billing backend | Selected · not installed |
| `@reactbits-pro/integrations-1` | P19 | /admin/control-room/integrations — Integration marketplace grid | show supported real connectors only | Selected · not installed |
| `@reactbits-pro/integrations-2` | P19 | /admin/control-room/integrations/{id} — Connection scopes + details | server-side scopes, revocation | Selected · not installed |
| `@reactbits-pro/integrations-3` | P19 | /admin/control-room/api-keys — API key table + actions | hashed keys, one-time display, rotate/revoke | Selected · not installed |
| `@reactbits-pro/integrations-4` | P19 | /admin/control-room/webhooks — Webhook endpoints + deliveries | signed delivery, replay safety, redaction | Selected · not installed |
| `@reactbits-pro/integrations-5` | P19 | /integrations/authorize — OAuth consent | OAuth server only after verified real client & scopes | Selected · not installed |
| `@reactbits-pro/ai-chat-8` | P20 | /study-space — Study Space accessibility and tutor input | mic consent and backend when configured | Selected · not official Pro installed |
| `@reactbits-pro/app-shell-9` | P20 | /study-space — Future private AI tutor chat with thread history; no fake conversations or backend | mic consent and backend when configured | Selected; some analogous ORIGINAL functionality exists in prior packs (not this Pro block) |
| `@reactbits-pro/editor-3` | P21 | context-specific UI — Platform admin code preview, read-only by default | only when supported by real data | Selected · not official Pro installed |
| `@reactbits-starter/device-tw` | P21 | public product showcase — Public feature showcase, decorative/accessible fallback | static accessible fallback | Selected · not official Pro installed |

## Free React Bits / ReactDrops selections — actual placement

| Library-style effect | Family | Chosen surface | Phase / constraint |
|---|---|---|---|
| Galaxy | Ambient / backgrounds | Public landing hero, opt-in light effects | P21; Selected; verify actual repo integration and license |
| LiquidEther | Ambient / backgrounds | Public /apps discovery only when performant | P21; Selected; verify actual repo integration and license |
| MagicRings | Ambient / backgrounds | App launch transitions, low-motion fallback | P21; Selected; verify actual repo integration and license |
| WebThreads | Ambient / backgrounds | Subject discovery backdrop | P21; Selected; verify actual repo integration and license |
| GradientWaves | Ambient / backgrounds | Schools information pages | P21; Selected; verify actual repo integration and license |
| Antigravity | Ambient / backgrounds | Role marketing pages; never form background | P21; Selected; verify actual repo integration and license |
| Glimmer Wrap | Ambient / backgrounds | Optional callouts | P21; Selected; verify actual repo integration and license |
| Fading Stars | Ambient / backgrounds | Homepage limited area | P21; Selected; verify actual repo integration and license |
| Glowing Wave | Ambient / backgrounds | Homepage low-power mode | P21; Selected; verify actual repo integration and license |
| Topographic Flow | Ambient / backgrounds | Subject landing section | P21; Selected; verify actual repo integration and license |
| Topographic Terrain | Ambient / backgrounds | World explorer | P21; Selected; verify actual repo integration and license |
| Fractal Glass | Ambient / backgrounds | Editorial/public cards | P21; Selected; verify actual repo integration and license |
| Fluid Typing Background | Ambient / backgrounds | Public marketing only | P21; Selected; verify actual repo integration and license |
| FlexCarousel | Navigation / showcases | Featured game cards /home | P17; Selected; verify actual repo integration and license |
| CircularGallery | Navigation / showcases | /apps/collections; only actual games | P17; Selected; verify actual repo integration and license |
| Fan Gallery | Navigation / showcases | /apps and curriculum showcases | P17; Selected; verify actual repo integration and license |
| DepthCarousel | Navigation / showcases | Public showcase | P21; Selected; verify actual repo integration and license |
| AccordionGallery | Navigation / showcases | Subject discovery | P17; Selected; verify actual repo integration and license |
| MorphSlider | Navigation / showcases | Public learning-world preview | P21; Selected; verify actual repo integration and license |
| CardSwap | Navigation / showcases | Homepage real apps | P17; Selected; verify actual repo integration and license |
| MagicBento | Navigation / showcases | Public feature grid | P21; Selected; verify actual repo integration and license |
| AnimatedList | Navigation / showcases | Upcoming notifications only if data exists | P09; Selected; verify actual repo integration and license |
| Folder | Navigation / showcases | Resource studio collection icon | P10; Selected; verify actual repo integration and license |
| Dock | Navigation / showcases | Optional learner desktop nav | P21; Selected; verify actual repo integration and license |
| CardNav | Navigation / showcases | Public navbar if non-duplicate | P21; Selected; verify actual repo integration and license |
| PillNav | Navigation / showcases | Public navbar if non-duplicate | P21; Selected; verify actual repo integration and license |
| BubbleMenu | Navigation / showcases | Mobile contextual menu | P21; Selected; verify actual repo integration and license |
| StaggeredMenu | Navigation / showcases | Public support menu | P21; Selected; verify actual repo integration and license |
| OptionWheel | Navigation / showcases | Learning module selector | P21; Selected; verify actual repo integration and license |
| Scroll Stack | Navigation / showcases | My Quest journey / dashboard | P21; Selected; verify actual repo integration and license |
| SloshGauge | Interactions / illustration | Curiosity meter only if metric is defined | P21; Selected; verify actual repo integration and license |
| Shape Physics | Interactions / illustration | Lesson interaction playground, non-essential | P21; Selected; verify actual repo integration and license |
| Chromatic Button | Interactions / illustration | Public actions and sign-up | P21; Selected; verify actual repo integration and license |
| Liquid Glass Orb | Interactions / illustration | Public brand display | P21; Selected; verify actual repo integration and license |
| Liquid Glass Logo | Interactions / illustration | Brand hero only | P21; Selected; verify actual repo integration and license |
| Refracted Text | Interactions / illustration | Public headings | P21; Selected; verify actual repo integration and license |
| Showcase Pointer | Interactions / illustration | Public portfolio interactions | P21; Selected; verify actual repo integration and license |
| Bubble UI Button | Interactions / illustration | Play/launch controls | P21; Selected; verify actual repo integration and license |
| SpringCheck | Interactions / illustration | Saved completion feedback | P21; Selected; verify actual repo integration and license |
| Topographic Contours | Interactions / illustration | Background only | P21; Selected; verify actual repo integration and license |
| PaperCrumple | Interactions / illustration | DISABLED due to previous build issues | HOLD; HOLD: previously caused build issues |

## Visual asset map (already supplied)

| Asset path | Usage | Loading guideline |
|---|---|---|
| `public/assets/studybuddy-p07/banners/learner.webp` | Student/independent dashboard | LCP-optimized; never repeat on every card |
| `.../banners/family.webp` | Parent overview | Decorative with accessible heading |
| `.../banners/school.webp` | School workspace and teacher shell | Calm reduced-effects layout |
| `.../banners/progress.webp` | Learning progress and analytics | Do not overlay chart labels on busy background |
| `.../banners/worlds.webp` | Subject/world discovery | Static fallback for reduced motion |
| `.../icons/{math,science,reading,geography,creativity,achievements,mascot}.webp` | Relevant subject/achievement cards | Consistent alt text; object-fit contain |
| `.../library/archive-00.webp` to `archive-43.webp` | Optional asset library for future worlds | Human-curated semantic labels; no automatic random usage |

## Canonical implementation checklist

- Choose selected UI + surface + role → verify current route and DOM mount → check library license → implement isolated React island or Blade equivalent → connect authorized Laravel endpoint → add tests → validate visually → record `installed` status and commit.
- Stop if the backend or entitlement is missing; offer an honest no-data state, **never demo numbers on a real admin page**.
- Track each UI item with implementation ID, code path, license/source, screenshots, runtime test output, and QA owner in `docs/COMPONENT_SHIPPING_LEDGER.md`.
