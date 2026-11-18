# StudyBuddy Pack 11D — School Safety Center and In-App Staff Notices

**Stage:** Local development release. **Not cleared for HostGator or public school use.**

## What actually works

- Private route `/schools/{organization}/safety`: verifies a school is approved and signed-in user has an **active membership** in that institution. Role privileges are scoped to that school; PCL platform admins get no silent override.
- **Notices:** owner and school admin can post text-only updates to staff, mark normal or important, optionally set expiration, and archive rather than delete. Existing active staff get persisted receipt rows, and new active members can acknowledge a visible notice. Explicit user clicks update `read_at`. There is no global 'push delivered' claim.
- **Unread badge preference:** all staff can choose whether normal unread notices count in their *in-app Safety Center badge*. Important notices always count. No notice is hidden; this does **not** change OS notifications or email delivery.
- **Confidential concern intake:** adult school members can submit staff safety/access/messaging concerns. Reporters see only their own case statuses. Managers see all cases in their institution and must enter a resolution note to change status. Cases are separate from the existing 11C **message report queue**, which remains linked for managers only.
- **Notice CSV export:** only school managers can export notices for their school. Includes archived notices and uses spreadsheet-formula escaping for free-text values; download is marked `private, no-store` and audited. The export contains **not** chat/DM content or student records.
- **Audit:** notice creation, archiving, case creation, case review and export are logged using the existing security-event service, without including report body or notice content.
- Original responsive interface. No React Bits Pro source code; no remote assets or dynamic third-party dependencies are introduced.

## What is NOT shipped in this phase

- Real-time WebSockets or FCM/APNs/web-push, outbound email, or schedule workers for delivery.
- Integrating the unread badge into the global navbar/11C chat notification icon.
- Auto-retention cleanup for messages or attachments; delete/edit/export of DM content.
- Virus-scanning user-submitted attachments, automated toxicity detection, child messaging, or PCL access to private message bodies.
- Mandatory reporting, escalation to external agencies, emergency dispatch, school legal data-retention agreements, or a compliance certification.

These remain **Phase 11D follow-up / security hardening** work and must be designed against the **current** 11C controller and migration (not an old snapshot) before public launch. Do not rely on this internal form as the only safeguarding process.

## Permissions and security guarantees

- Active school staff members may view notices, acknowledge them, change their own badge preference and submit reports.
- School Owner / School Admin manage notices, cases and CSV export. No teacher bypass or platform-level override.
- Tenant IDs checked on every model action, not only in route binding; cross-tenant message IDs denied with 404.
- CSRF protection, Laravel validation and rate limiting on all write/export routes.
- No student profile or message details from 11C are fetched by this feature.
- Case details aren't exposed to other reporting staff; only school managers and the submitter.
- Reports and notices do not hard-delete from UI. Database CASCADE applies only if the parent organization is deliberately removed; plan organizational data retention separately.

## Installation and activation

1. Confirm P11C source was committed/pushed to `main` independently, and that local tests and migration are complete.
2. Run pack `INSTALL.command` **from `~/Desktop/StudyBuddy-main`**. The installer checks current 11C files, backs up source, adds module files, appends one route include, injects a Safety Center link in the school workspace, lints PHP, caches Blade, verifies routes, and runs the full PHPUnit suite. On failure it restores changed source files; it does **not** touch the database.
3. If and only if the Mac reports `P11D SOURCE + FULL MAC TEST SUITE PASSED`, run pack `ACTIVATE_LOCAL.command`. It validates the exact local MySQL environment and makes a fresh, private, checksum-verified database dump **before** running the one P11D migration, then runs PHPUnit again. It does not reset passwords, delete users or seed organizations.
4. Browser-test as synthetic school owner/admin/teacher. Review with multiple sessions and verify cross-school permissions; confirm archived notices still export but aren't displayed.
5. Stage only the intended pack files, review `git diff --cached`, and commit to `main` if it all passes. Never commit local SQL backups or private credential READMEs.

**No raw database passwords are included in this pack.** Your local developer password exists only in the previously generated ignored `docs/LOCAL_DEV_ACCOUNTS.private.md` on your Mac.
