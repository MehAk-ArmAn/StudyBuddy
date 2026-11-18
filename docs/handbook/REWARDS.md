# Points, badges and achievements — integrity invariants

**Implementation milestone:** P11F R2 was installed on the developer Mac and 294 tests/3911 assertions passed; after the UIUX patch, 296 tests passed. This is verified from supplied Terminal logs, not proof of production launch.

1. **Reward source:** A points credit must be based on a server-owned, completed and graded Math Sprint run. Do not trust browser time, client scores or generic iframe completion buttons.
2. **Atomicity:** Changes to `users.cosmic_points`, `StudyBuddyPointTransaction` and reward events should remain transactional. Duplicates must not produce another credit (event key).
3. **Caps:** Admin-editable rules and a global daily cap are enforced on the server. Stopwatch duration can contribute a *bounded bonus* but is not conclusive engagement proof.
4. **Achievements:** Badge unlocks follow server-recorded activities; cosmetics should be non-cash, non-transferable, non-chance-based where children may use the platform.
5. **Admin authority:** `/admin/control-room/rewards` is restricted to authorized Head Admin. Rate changes require validation and an audit log.
6. **Existing balances:** Never backfill by inventing transactions or reset live users to zero during a migration.

**Currently verified:** Math Sprint is the first integrated verified earning path. Other embedded games may still be playable but should not mint points until their server-verifiable completion is integrated.

**Review paths:** `app/Support/StudyBuddyRewardIntegrity.php`, `app/Http/Controllers/StudyBuddyMathSprintController.php`, `app/Http/Controllers/Admin/StudyBuddyP11FRewardsController.php`, `tests/Feature/Learning/StudyBuddyPack11FRewardsTest.php`.

**Legal:** Virtual points are not a legal exemption. Before public release, consult qualified counsel about minor accounts, privacy, prize/promotions rules and each jurisdiction. No legal approval is claimed.
