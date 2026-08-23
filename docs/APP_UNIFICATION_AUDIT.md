# StudyBuddy Final App Unification Audit

This patch makes `/apps` the official StudyBuddy app universe.

## Final route decisions

- `/apps` is the main app listing page.
- `/apps/{slug}` is the individual app detail page.
- `/app-launchpad` redirects to `/apps`.
- `/app-ecosystem` redirects to `/apps`.
- `/play/{slug}` stays protected for logged-in + verified users.
- `/points-wallet` stays protected for logged-in + verified users.

## Admin control

The app catalog is controlled from `/admin/studybuddy/final-platform`.

Admin can edit:

- title
- slug
- category
- status
- icon
- image URL
- short description
- long detail description
- preview text
- age range
- roles
- learning tags
- learning outcomes
- how-it-works steps
- platform links
- points reward
- estimated minutes
- web/download flags
- featured/active flags

## Security expectations

- Guests can preview public content only.
- Logged-in but unverified users see verification prompts for locked actions.
- Verified users can save quests, play web sessions, and earn points.
- Admin routes remain behind admin middleware.
- Points are server-controlled and come from the app database record.
- Public theme resets to Cosmic Dolphin after logout.

## Cleanup performed

The installer removes old patch folders, backup folders, and duplicate app pages from phased builds.
