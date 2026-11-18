# StudyBuddy P11A — Head Admin and PCL Team Foundation

**Stage:** Code delivered for Mac validation; production deployment unverified. This phase is an early security-and-governance foundation, not the entirety of School OS.

## Confirmed baseline / dependencies

- `main` through `dc3f3bd` contains Packs 05–09; `b45963e` contains the comprehensive documentation blueprint. P10 Resource Studio was confirmed installed locally with **252 tests and 3,689 assertions**; push not shown in latest log.
- Phase 11A source packages against the **P10 installed source**, not an imaginary future P11 merge. Use the guarded installer and do not reset uncommitted P10 code.
- The P10 installer printed the incorrect line `PACK 09 INSTALLED`; the surrounding P10 file and test output confirm the Resource Studio source was installed.

## New functional modules

1. **Scoped PCL staff grants:** `studybuddy_admin_grants` stores each platform admin's user ID, role `head`/`staff`, active status and selected capability strings. An active Head grant is anchored to the user ID, not the supplied email string. No student/school user can grant platform permissions.
2. **Existing Head Admin recovery flow:** console commands `studybuddy:p11:rotate-head-password` and `studybuddy:p11:claim-head`; both require explicitly specifying the canonical *existing* account's numeric user ID, not create a new account or delete older rows. Placeholder passwords are rejected by claim.
3. **Team UI:** `/admin/control-room/team`, original responsive free component with scoped permissions. Head can create staff with undisclosed random credentials and request a password-setup email, update grants, and revoke access. A staff user is blocked until email verification.
4. **Privileged route gating:** all `admin` middleware routes check the required server-side capability once Head has been claimed. Non-granted legacy admin users (including `admin@studybuddy.local`) lose admin-page access but their records are preserved.
5. **Protect Head/ordinary users:** Head record cannot be edited/deleted by legacy UserController; normal account editor cannot promote anyone to `role=admin`, toggle `is_admin`, or overwrite the Head. Self-service Head email change is blocked. Deleting real school/learner accounts through the admin directory is denied until a dedicated privacy/export workflow exists.
6. **Test users:** `studybuddy_test_account` boolean marks accounts in the users table. This is a label/foundation, not yet complete analytics isolation, so **do not put test accounts into real schools or production analytics**.
7. **School governance:** existing school application review is retained and now requires `schools.review` capability for non-Head staff. School owners continue managing their own approved workspaces under existing tenancy rules.
8. **Audit events:** existing `studybuddy_security_events` stores successful Head claiming, staff grants/changes/revokes, user-account operations, and Head password rotation where audit storage is available.

## How to install safely on a Mac

1. Verify you are on `main`, that P10 has passed local PHPUnit and its uncommitted changes are backed up/committed. Make a real database backup before migration.
2. Install P11 source via `INSTALL.command`. The installer checks **exact source hashes**, backs up overwritten files, validates compiled Blade PHP and routes, and runs the **full PHPUnit** suite. On failure it restores the source. It intentionally does **not** migrate data or touch admin accounts.
3. Once source tests pass, take a DB backup and run `php artisan migrate --force` in the intended local/staging environment.
4. Use `php artisan studybuddy:p11:rotate-head-password 13` (change 13 if your canonical `.fun` user has a different ID). The password is entered interactively without appearing in shell history; **12+ characters, letters and numbers**. If the command refuses the ID, inspect rather than guessing.
5. Use `php artisan studybuddy:p11:claim-head 13`. This refuses insecure default/placeholder credentials. The old `.local` record remains for reference; once Head is claimed, that account has **no admin route access unless explicitly granted by Head**.
6. Open `/admin/control-room/team`, create a staff invite with limited capability and verify both allowed pages and denied pages. For email delivery, configure mail and verify a real reset-link flow before issuing invites.
7. Inspect changes, rerun PHPUnit, and only then commit the P11 source to `main`.

**Do not seed an additional admin after claim**, manually delete legacy administrators, change IDs in the database, or use short shared passwords. A secure recovery operator should separately document how to re-establish a protected Head identity after a genuine loss of access.

## Phase 11A limitations / next steps

- Production admin MFA is not enabled by this patch. Existing configurable email MFA is retained; enforce MFA and session hardening before real-school deployment.
- Legacy user pages remain styled as they were, with the new security behavior. This is not a wholesale re-skin of every PCL Control Room module.
- School-wide staffing dashboard, real user-account invitation lifecycle, granular multi-organization permissions, complete test-account isolation, exports/deletion tooling, classroom communication, and student safeguarding expansion are **separate later phases**.
- React Bits Pro source is **not installed**: the UI here is original and free.
- No staff or school can inspect private study notes or student chats merely because they have a platform permission.
- Run cross-school and MFA integration QA before claiming production readiness.

**Optional reconciliation:** after verifying `.fun` Head Admin login and backing up the database, run `php artisan studybuddy:p11:retire-legacy 6` to interactively demote the legacy `.local` user shown as ID 6 in your local log. This retains its record and related history, and requires explicit confirmation; do not guess another ID.
