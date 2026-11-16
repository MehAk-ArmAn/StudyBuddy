# StudyBuddy Final Access + UI Decision

This patch intentionally removes email inbox verification from StudyBuddy.

Instead, StudyBuddy uses:

- account login
- role-based navigation
- admin-only content editing
- adult/teacher verification status
- parent/student approval connections
- server-controlled points
- guest preview mode
- default public theme reset on logout

Important pages:

- `/apps` is the official app listing.
- `/apps/{slug}` is the app detail page.
- `/app-launchpad` redirects to `/apps`.
- `/app-ecosystem` redirects to `/apps`.
- `/play/{slug}` is preview for guests and playable for logged-in users when enabled.
- `/points-wallet`, `/my-quest`, `/command-center` require login.
- Admin panels require `is_admin = true`.

Parent/student security:

- Parent accounts must be adult/power-role accounts.
- A parent request must be approved by the student account before controls unlock.
- Parent permissions never include child password/private-setting access.
- Teacher links remain limited and require teacher role verification.
