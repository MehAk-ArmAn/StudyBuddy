# StudyBuddy — Next Chapter upgrade, school pilot and security release gates

**Prepared 13 November 2026.** This is a source-code *candidate*, not proof of a deployed, tested, certified or secure production platform. The original uploaded repository was not modified in place.

## Scope of this additive release

- New light/premium public landing visual layer while retaining the database-managed CMS sections, app catalogue, user profiles, dashboards, learning-game links, and existing URLs.
- Two original, locally optimized StudyBuddy illustration assets: `public/assets/studybuddy-brand/pages/learning-world-portal.webp` and `school-world-banner.webp`. Both are decorative concepts, **not** screenshots of implemented classroom features. Existing dolphin mascot and other brand assets remain untouched.
- Public `/for-schools` landing page, `/schools/apply` pilot application, admin manual application review, private institution workspace, invite/accept/revoke membership with server-side authorization.
- Time-limited, email-specific, hashed-token teacher invitations. Student accounts cannot accept institution staff invitations.
- Genuine Laravel email-verification registration flow instead of automatically confirming newly registered addresses, plus password-reset workflow.
- Additional admin sign-in throttling, optional two-step email code and auditable security events. Security events can optionally send threshold-based alert emails to the configured security contact.

## Critical limitations before any school deployment

**Do not put student data from a real Dubai school into this build yet.** This implementation provides **school onboarding and a private staff membership directory**, not a complete multi-tenant school information system. A full tenant isolation audit, institutional agreement, safeguarding/consent workflow, data retention/deletion rules, verified third-party processors, penetration testing and relevant UAE school/data law review are required before processing school/student data. No system can truthfully promise to be unhackable.

- Institution requests are **not** automatically proof of school ownership. An admin must independently contact the school through established official channels and check that the requesting person is authorized. The approval form records a human verification attestation; document storage/audit evidence is an external operational requirement.
- Role verification and adult verification from existing accounts should undergo manual review. A self-entered date of birth is not verified identity.
- Classroom rosters, homework assignment management, teacher-parent messages, student reports, school-grade analytics, SSO, institution billing, and SIS integrations are **not** implemented by this pilot.
- Admin email-code MFA exists but is not phishing-resistant TOTP/WebAuthn. `STUDYBUDDY_ADMIN_MFA_ENABLED` must be enabled in the actual environment and transactional email must be verified before a school pilot. Plan to adopt phishing-resistant MFA.
- Existing accounts with `email_verified_at` populated by the previous automatic verification behavior cannot be retroactively assumed to own those inboxes; they need a re-verification plan, especially adult and staff identities.
- Mail default in `.env.example` is `log`, which is **not** actual email delivery; set up authenticated transactional email in a private `.env` before enabling invitations, email verification or MFA.
- Security events are audit hints, **not** threat prevention. Configure log retention, monitoring, backups, rate limits, WAF/edge protections and an incident-response procedure.

## Recommended staging rollout

1. Make a dated backup of the **current working repo and SQL database**, including media and user-generated content. Never replace production with this ZIP; diff and merge changes into a branch.
2. Use PHP 8.2+, Laravel 11, your own staging MySQL, a nonproduction domain and separate email credentials; never expose APP_DEBUG in production.
3. Install composer dependencies, set private `.env` credentials, rotate secrets only in a controlled process. Do not replace a live `APP_KEY` casually because it affects encrypted data/sessions.
4. Review and apply migration `2026_11_13_000100_create_school_workspaces_and_security_events.php` with a **staging DB** using `php artisan migrate` (production would require `--force`, only after testing and backup).
5. Run `php artisan optimize:clear`, `php artisan route:list`, `php artisan test`, then browser tests at desktop, tablet and small mobile sizes. Run a11y, WAVE/axe checks and Lighthouse/performance audit.
6. Configure `MAIL_MAILER`, real SMTP/transactional delivery, `MAIL_FROM_ADDRESS`, `STUDYBUDDY_SECURITY_ALERT_EMAIL`, and `STUDYBUDDY_ADMIN_MFA_ENABLED=true`; test the sender domain SPF/DKIM/DMARC.
7. Test school application as an adult teacher; verify identity/domain **independently**; admin approve; invite a staff teacher by a matching, verified email; test acceptance and revocation. Attempt unauthorized cross-school access to verify refusal. Test expired/revoked invites and replay.
8. Validate existing user/app/CMS paths, legacy account sign-in, password reset, admin access, privacy policy and actual game launches on staging before a selective rollout.
9. Before real-school accounts, complete independent penetration testing, staff account policy, access review, consent and legal approvals. Keep the public school page clearly labeled a pilot during this period.

## New / important navigation

- Public homepage: `/`
- Public school information: `/for-schools`
- Request institutional workspace: `/schools/apply` (signed-in verified adult)
- Private school workspace: `/schools/{id}/workspace` (approved active members only)
- School reviews / security events: `/admin/control-room/schools` (platform admin only)
- Email confirmation: `/email/verify`
- Password recovery: `/forgot-password`

## QA status in this delivered candidate

- ✅ PHP files: syntax lint passed locally.
- ✅ Visual prototype inspected at desktop (1440 px) and mobile (390 px), as a standalone static browser preview.
- ✅ Added images compressed as local WebP files (~100–115 KB each).
- ⛔ Laravel package runtime / DB migration / functional integration tests: **not executed** in this working environment (Composer dependencies and configured database are unavailable).
- ⛔ Production DNS, actual email delivery, security alert delivery and load testing: **not verified**.
- ⛔ Third-party penetration test, compliance assessment and live deployment: **not performed**.

## Asset and visual notes

No replacement of the StudyBuddy logo, original dolphin illustration, existing game artwork, current CMS entries, app URLs or published app data is intended. New graphics are optional additions to a landing-page visual system. Visual pages should not imply that school-grade reporting, real-time messaging or curricular accreditation already exists.
