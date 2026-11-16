STUDYBUDDY V7 CLARITY - FOCUSED LANDING REBUILD

Scope:
- resources/views/home.blade.php (replaces old homepage visual stack)
- resources/views/layouts/app.blade.php (loads V7 CSS/JS late)
- resources/views/layouts/partials/sb-shell-navbar.blade.php (3 primary links; rest retained in More)
- public/assets/css/studybuddy-v7-clarity.css
- public/assets/js/studybuddy-v7-clarity.js
- public/assets/studybuddy-v7/learning-portal.webp and dots.svg

What stays unchanged: Laravel routes, CMS records, app metadata, school records,
policies, authentication, account roles, and database schema.
Existing hero, sections, and featured apps are still read from the CMS.
Secondary CMS sections are preserved in accessible expandable details.

This is a non-destructive *visual patch* tested against the uploaded StudyBuddy
source with NextChapter + Sparkle V2 + V4 + V5 + V6 merged, NOT against your live Mac.
The zip overwrites the three included Blade views; inspect if you made newer edits.

Real React Bits components require a React runtime. This is native CSS/JS
inspired by the restrained motion patterns (split text, scroll reveal, glow cards),
not a direct copy of library source or a React install.

QA: JS syntax, CSS parser, standalone desktop/mobile light/dark browser preview.
Not yet tested on your live Laravel/DB installation. No migrations necessary.
Run locally, verify at /, /apps, /for-schools, /login, /dashboard,
and test a role tab, app card, navigation More, and theme menu.
Do not run against production or run DB migration commands for this visual update.
