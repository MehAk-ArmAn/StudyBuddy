# Admin Control Room, School OS and Community — implementation architecture

**Selected product architecture, pending development.** Existing routes and controllers should be reused where possible; new URLs below are **suggested**, not verified existing endpoints. Routes labeled CURRENT are grounded in the repository/source logs.

## Current feature anchors (source verified unless otherwise noted)

| CURRENT URI / area | Existing source anchor | Planned enhancement |
|---|---|---|
| `/admin/control-room` | `StudyBuddyControlRoomController`, `resources/views/admin/control-room/index.blade.php` | Role-sensitive secondary rail, real operations, school governance |
| `/admin/control-room/schools` | `SchoolOrganizationReviewController`, `routes/studybuddy_schools.php` | Verified workflow with scoped reviewers, audit and statuses |
| `/schools/{organization}/workspace` | `SchoolOrganizationController`, `routes/studybuddy_schools.php` | School hub, staff board, channels, calendar, scoped resources |
| `/community` | `StudyBuddyProfileController::community`, `routes/studybuddy_dashboard_profile_final.php` | Safe discovery + optional approved communication, not global open inbox |
| `/dashboard` | `DashboardController`, role Blade partials | Add reliable quick links to community, notifications, approved school channels |
| `/getting-started` | Pack 08 routes/controller, Mac tests passed | Optional wizard upgrade/review-confirm with actual save |
| `/my-day` | Pack 09 controller/routes, Mac tests passed | Later add meeting booking and teacher assignment deadline UX |
| `/resources` | Pack 10 private Resource Studio, Mac tests passed | Add authorized sharing/review as separate new backend |
| `/study-space` | Pack 06 React island | Later voice/transcript and approved tutor API |

## P11 Control Room high-level modules

**Admin landing:** one compact overview row, counts from actual queries, access-only quick actions, no duplicated hero stacks. `App Sidebar 4` maps to icon rail + context panel; `Data Table 6` maps to searchable paginated list with a detail drawer, not a giant unpaginated SQL dump.

**User directory:** users with verified type, status, school membership, last seen only if collected with notice, and approved actions. Bulk actions cannot affect Head Admin. `Forms 2` is the create-user/test-account wizard. Test accounts use explicit `is_test=true`, isolated namespace and excluded metrics.

**Staff permission grants:** Head Admin creates PCL staff accounts, assigns capabilities; other staff cannot grant superior privileges. Credentials must be user-set using single-use reset links, never sent in plain email or seeded with simple shared passwords.

**School management:** organization applications with verification proofs handled in restricted storage; review decisions, school membership, staff invitations and role changes. Each event has actor, org, action, previous/new state, timestamp and reason. No automatic approval based on email suffix.

**Operations:** `List 3` security event filters, Monitoring 5 scrubbed logs, Monitoring 6 actual daily probes, Monitoring 7 grounded service rows, Monitoring 8 measured latency histogram, Monitoring 9 actual queue entries. If instruments are absent, say "Not monitored yet".

## P13 communication entities (PROPOSED SCHEMA; do not migrate until reviewed)

`conversations(id, kind, organization_id?, class_id?, title, created_by, status)`
`conversation_participants(conversation_id, user_id, role, joined_at, left_at, read_through_id?)`
`messages(id, conversation_id, author_id, body, created_at, edited_at, deleted_at)`
`message_files(id, message_id, storage_key, safe_name, mime, size, scan_state)`
`message_receipts(id, message_id, user_id, read_at)`
`message_reports(id, message_id, reporter_id, reason, state, assigned_to)`
`moderation_access_audit(id, actor_id, conversation_id, purpose_code, ticket_id, accessed_at)`

No message ever loads without fresh policy authorization. Read receipts identify participant acknowledgments only. File download requires recipient membership, scanned state and deliberate secure headers. Search index and notifications must inherit the exact same constraints.

## P15 annotations / task entities (PROPOSED)

`documents + document_versions`; `annotations(document_id, version_id, anchor_type, start,end,x,y,author_id)`; `annotation_comments(...)`; `mentions(comment_id,user_id)`; `boards(org_id,visibility)`; `board_cards(board_id,owner,assignee,status,priority,due_at,sort_index)`; `board_events(card_id,actor,delta,timestamp)`.

A mention triggers notification only when both sides are eligible. A canvas pin must map a stable document/image version and normalized coordinates. Drag/move status must be transactional and permission checked, with undo/review where appropriate.

## UI component to feature contract examples

- **Chat 5:** school-managed channel context + participant list + composer + private attachments, with moderation disclosures.
- **Chat 2:** verified DM conversation and per-recipient read receipts; no contact by typing arbitrary email.
- **Comments 4:** @mention suggestions only from allowed participants; autocomplete is not an authorization control.
- **Data Table 6:** selected user/school reflected in URL/panel, sanitized details and mutation review.
- **Dashboard 11:** staff/coverage board based on actual school roster and assignment workload, not generated staffing forecasts.
- **Analytics 5 and 9:** count only scoped real events; chart displays no-data and exact filter/period.
- **Kanban 2/4:** school task board and detail side panel with saved event history and server filters.
- **Wizard 2/4/5/6:** onboarding progress, selectable valid services, explicit review/confirm, truthful save/job states.
- **Scheduling 3/4/7:** attendee selection, presets, actual conflict-checked slot bookings; no demo "available" times shown as bookable.
- **Settings Form 1:** Dirty state, sticky Save/Discard bar, server validation, stale-version/concurrency notice.

## Cross-cutting service rules

Use Laravel policies/gates/service-layer checks not role-only if-statements scattered across views. Document whether sources are eager-loaded and scoped. Add pagination, ownership lookup, audit event serializer, resource storage service, queue instrumentation, test fixtures and relationship cleanup policies as each phase is introduced. No unreviewed third-party transcript processor or payment gateway. All personal data stays private unless the user, school and purpose expressly permit sharing.
