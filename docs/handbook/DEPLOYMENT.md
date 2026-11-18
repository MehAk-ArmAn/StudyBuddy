# Deployment checklist and ownership

**No production deployment has been performed or verified by this documentation pack.** Do not extrapolate from local Mac tests.

1. Verify the current commit and release owner; coordinate any simultaneous changes to `main`.
2. Back up live application data and assets **privately**, verify restore path; do not place SQL under Git.
3. Review `.env` and production config through a secure secret manager; do not print or commit it.
4. Review the planned migrations one by one; confirm idempotency/data retention. Never use destructive reset commands.
5. Validate production-only mail/queue/storage/security headers, rate limits and deployed asset versioning.
6. Run staging tests, role smoke tests, actual browser checks, child-safety/privacy review, upload/points abuse checks.
7. Have a rollback plan for code **and** schema/data. Never assume `git revert` reverses a migration.
8. Record release commit, QA evidence and known limitations in a handoff note.

Production hosting and deployment implementation are **not confirmed** by the evidence used to prepare this pack. Assign an actual deployment owner before launch.
