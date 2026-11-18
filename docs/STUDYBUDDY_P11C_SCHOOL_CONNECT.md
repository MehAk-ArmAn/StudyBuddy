# StudyBuddy Phase 11C — School Connect & Community Gateway

**Source status:** Pack 11C source-ready; Mac Laravel PHPUnit and live MySQL activation pending until confirmed by terminal output. The public community feature remains a profile-discovery hub; **private student messaging is not enabled**.

## Real routes

- `GET /schools/{organization}/connect` — verified adult staff conversation hub; default channels Staff Lounge and Planning & Updates are created on first visit.
- `POST /schools/{organization}/connect/channels` — school owner/school-admin creates private channels (15 maximum per school).
- `POST /schools/{organization}/connect/directs` — one-to-one staff conversation, uniquely keyed by sorted account IDs within the institution.
- `GET /schools/{organization}/connect/{conversation}` — read last 80 messages for authorized members only.
- `POST /schools/{organization}/connect/{conversation}/messages` — send text and optional PDF/PNG/JPEG attachment (private, 4 MB maximum); server validates file MIME and extension.
- `GET /schools/{organization}/connect/{conversation}/attachments/{message}` — authorized private download; no direct storage URL; hidden attachments no longer downloadable.
- `POST /schools/{organization}/connect/{conversation}/read` — explicitly advance read receipt.
- `POST /schools/{organization}/connect/{conversation}/messages/{message}/reports` — report exactly one message; actor must belong to that conversation.
- `GET /schools/{organization}/connect/reports` — owner/school-admin limited moderation queue. Shows **reported message only**, not arbitrary DMs.
- `PUT /schools/{organization}/connect/reports/{report}` — dismiss or hide; audit record maintained.

## Real data model and permissions

Four new migrations tables: `studybuddy_school_conversations`, `studybuddy_school_messages`, `studybuddy_school_message_reads`, `studybuddy_school_message_reports`.

The backend enforces approved school, active staff membership, verified email, allowed adult/staff role. Any global admin, including Head Admin, **must independently be an active staff member of the school** to read messages. School staff must not put identifying learner data in messages or attachments. No PCL platform content access override. Security event logging contains event type, IDs, date, and minimal context; not private message text.

School owner/admin can create channels, review *reported* content, and hide inappropriate messages. Teachers can participate in channels and their own DMs, send and report, but cannot read other DMs or moderate. Revoked members lose access on every request. A separate school cannot read or mutate any ID from another institution.

Messages and attachments are retained for school safeguarding accountability; there is **no automated deletion or retention limit policy yet**. Hidden messages remain in database; attachments become inaccessible. The sending UI discloses moderation scope. For deployment, add privacy notice, retention/export/deletion operations, CSRF/cookie policy, abuse response playbook, file malware scanning, secure upload handling and an incident procedure. On HostGator verify storage is private; never serve `storage/app/private` files directly. Manual refresh, not real-time push; notification delivery, typing indicators, @mentions, richer groups, voice and video are deferred. There is no end-to-end encryption claim.

## Community scope

Public `/community` already provides opt-in public-profile discovery through existing code; this pack only links it clearly from School Connect. School-only messaging stays private. Cross-school messaging, student-to-student direct messaging, adult-to-student DMs, student attachment sharing, and a platform-wide writable community feed **remain disabled**, until separate age verification, reporting, opt-in, guardian controls and anti-grooming safeguards are implemented and reviewed.

## Release gate

1. Source installer passes PHP syntax, Node syntax, compiled Blade checks, route list, full PHPUnit on Mac with no changed existing outcomes.
2. Fresh database backup verified before migration. P11A/P11B migrations already applied.
3. Local owner, school-admin and teacher can send; outsider, student, revoked member and separate-school staff cannot read.
4. Owner does not see a private DM without being a participant; report queue exposes only reported text. School managers can hide the offending message; access to attachments is revoked.
5. Verify local private credentials remain gitignored; stage only changed source/docs, exclude `storage/framework/testing/**`.

React Bits Pro Chat 2, Chat 5 and Comments 4 are **reference concepts only**; this phase contains original independently authored Laravel/Blade/JS/CSS, no paid source. School Connect is not a substitute for consent and child-safety compliance review.
