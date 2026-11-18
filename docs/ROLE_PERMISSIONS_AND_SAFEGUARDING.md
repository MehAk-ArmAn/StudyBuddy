# Role access, school tenancy and child safeguarding — implementation contract

**Decision records, not implemented security claims.** All new features must enforce this table on the server. Last known admin DB evidence: two admin users existed locally (IDs 6 and 13, addresses `admin@studybuddy.local` and `admin@studybuddy.fun`); **do not delete/merge either row without dependency audit and a database backup**. An account password was set using a literal placeholder in the user's terminal history; rotate immediately before external access. Never hardcode, share, or commit short/default credentials.

## Canonical role hierarchy

| Capability | Head Admin | PCL Staff Admin | School Owner/Admin | Teacher | Parent | Student/Independent |
|---|---|---|---|---|---|---|
| Global admin role grants | Yes (step-up auth and audit) | No | No | No | No | No |
| Create/lock PCL staff admins | Yes | No | No | No | No | No |
| Manage verified schools | Yes | Only explicit scope | Own institution only | Limited to own classes | No | No |
| Review school applications | Yes | If `schools.review` granted | No | No | No | No |
| User/test account provisioning | Yes | If explicit limited scope | Own approved school invites only | No unrestricted accounts | No | No |
| Content / apps CMS | Yes | If `content.manage` granted | Own approved content only | Own authored material only | No | No |
| Read school chats | Disclosed escalation or scoped ops review only | Disclosed moderation scope only | Own organization under published rules | Participating conversations | Explicitly authorized | Participant only |
| Modify another user's private notes | No normal access | No | No | No | No | No |
| Technical logs / provider secrets | High-trust security scope with audit | Least privilege | No | No | No | No |
| Billing operations | If approved business feature | Finance-only scoped | Own school account | No | Own parent payment only if product offers | No |

## Head Admin protection

- Canonical proposed identity: `admin@studybuddy.fun`. **Identity must be tied to a trusted immutable UUID/primary key after deliberate migration**, not `email` alone. Restrict delete, demotion, suspension, reassignment, and privilege reset both in policy/controllers and DB or service-layer invariants where feasible.
- Separate a break-glass **recovery process** with independent trusted operators, security logs, backup verification and strong MFA. Do not claim a database row is literally impossible to delete.
- Existing `.local` and `.fun` accounts may have independent records, references and history. Audit child relations, admin action logs, school ownership and foreign keys first; migrate references transactionally, disable or retire duplicate under a documented plan; no automatic account deletion.
- Never grant privileges based on email string, display name, or user-selected `role` field alone. Validate `is_admin`, resolved scoped grants, school membership and account verification server-side. Separate `platform_role` from learner `role` and school membership `role` to avoid accidental privilege mixing.
- Admin session protections: MFA, short session, step-up for changing privileges/passwords/integrations, new-session audit, rate limit, CSRF, no privilege promotion via local test data.

## Role/scoping capabilities

Suggested permission names: `platform.head`, `platform.users.manage`, `platform.staff.grant`, `schools.review`, `schools.manage`, `apps.manage`, `content.manage`, `moderation.review`, `support.manage`, `feedback.triage`, `billing.manage`, `ops.view`, `ops.run`, `integration.manage`, `audit.read`. Each scoped permission is explicitly delegated, revocable and audited. No static UI-only checks.

School resources must always have `organization_id`; enforce approved organization status and **active** member/role through middleware/policies, tenant-scoped queries, ID comparisons and authorization tests. A PCL staff account is not automatically a school member. School invitations are time-limited, address-specific, revocable and cannot be accepted by unauthorized role types.

## Community and messaging — safe and clearly disclosed

- Public `/community` may provide account discovery with privacy-first profiles. *Not a global open inbox to every child account.*
- Class channels: school administrators/teachers can configure permitted participants and retention; default staff-only until safeguarding/legal checks approve student channels.
- DMs: participant authorization checks on every send/read/receipt/attachment; no arbitrary adult–minor stranger messaging; children can block/report; guardians and school roles see only appropriate approved information.
- @mentions: autocomplete only validated eligible recipient IDs within same authorized conversation/organization; suggestions never leak user directory across schools.
- Attachments: private disk, extension+MIME checks, size limits, malware scanning policy, authorization on every download, expiry and report path; never expose arbitrary raw storage paths.
- Audit: PCL safety access only through transparent, documented reports/escalations, reason codes, separate roles, strict retention and review. Do not silently read messages or create undetectable backdoors.
- Consent and retention: age-appropriate notices, school agreements, data minimization, deletion/export process and incident response must be approved before real-school pilot.

## Concrete negative tests required

1. A teacher from School A cannot discover a School B child/channel/task/resource through query, search, ID swap, or API.
2. A parent only sees children with a verified, approved connection and correct policy, not simply a matching email field.
3. A school manager cannot create a PCL admin or override platform privilege flags.
4. Non-Head admins cannot demote/suspend/delete/replace the protected principal even by direct POST request or bulk update.
5. A learner cannot mention another school's minors or send messages outside authorized participants.
6. An admin-created **test** learner cannot become real member or inflate production analytics.
7. Failed upload/invalid attachment cannot leave a retrievable public file.
8. Every privileged moderation access produces a durable event; no private message body in generic application logs.
9. Different roles experience 403/404 as appropriate, not only hidden buttons.
10. Emergency recovery works on a staging clone without corrupting production identities.
