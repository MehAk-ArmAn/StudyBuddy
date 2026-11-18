# Testing and QA

**Automated:** `php vendor/bin/phpunit --colors=never`; routes with `php artisan route:list`; Blade with `php artisan view:cache`; docs with `python3 tools/docs/check_docs.py`.

**As last shown in user logs (2026-11-18):** UIUX Fix 01 passed **296 tests and 3,919 assertions**. This is historical evidence, not the result of running the new documentation installer on your Mac.

Must-test matrices:
- **Auth:** Head Admin, delegated PCL staff, School Owner, School Admin, Teacher, learner, outsider; both allow and deny flows.
- **Schools:** Approved/disabled organization, revoked membership, wrong organization IDs, forged message IDs, attachments and manager-only exports.
- **Rewards:** Valid run, duplicate completed run, incorrect score, short duration, cap reached, Head vs non-Head rule editing, shop spending and ledger balance.
- **UI:** 390/768/1440 widths, keyboard-only, focus states, visible errors, contrast, dark mode if supported.
- **Build/ZIP:** Ensure untrusted ZIP uploads cannot escape private storage or run executable payloads; no public path traversal.

Do not treat successful tests as proof of legal compliance, real-device performance, production DB migrations or browser accessibility. Record the manual testing and any missing checks in the next handoff note.
