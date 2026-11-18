# StudyBuddy — security contact and reporting

StudyBuddy handles learner, family and school information. Do **not** open public issues containing real user records, credentials, database exports, invitation tokens or private attachments. Report vulnerabilities privately to the repository maintainers through an approved private channel. **No security email or SLA is asserted here**; maintainers should add a verified one before public launch.

## Non-negotiable controls

- Server-side role and ownership checks; reject unauthorized calls even when sidebar links are hidden.
- School organization IDs must be scoped in every member, notice, conversation, report and download operation.
- Verified rewards, idempotent credits, journal and wallet balance updates must happen in one transaction; no rewards from unverified game launches.
- Do not trust uploaded HTML ZIP games as harmless; same-origin iframes can carry significant security exposure. Review package sandboxing and publishing decisions before changes.
- No secrets in source, docs, logs, screenshots or releases. Backups live outside the repository with private permissions.
- Review age/child privacy, retention and country-specific legal requirements **before launch**; there is no certification claim.

See [Security boundaries](docs/handbook/SECURITY_BOUNDARIES.md) and [Release checklist](docs/handbook/RELEASE_CHECKLIST.md).
