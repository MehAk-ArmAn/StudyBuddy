# Data boundaries and migration rules

Main data classes: users/auth; mini-app catalogue; learning sessions; school organizations/members/invitations/tasks; conversations/messages/reports; safety notices/concerns; reward ledger/events/rules/badges; UI/page/site settings. For exact tables and migrations, see the generated [data inventory](generated/DATA_INVENTORY.md).

- Schema migrations should be additive and reversible where reasonable. Read status before applying and take a **fresh complete** database backup outside the repo.
- Existing identity rows and admin accounts should be preserved; never run destructive seeding on a real database.
- Scope exported school records and audit who exported them; assume student/teacher information is private.
- The reward ledger and spendable wallet balance must be updated atomically; avoid historical auto-reconciliation without an approved data-repair plan.
- Uploads and generated test files do not belong in source control; `storage/app` should be treated according to privacy and backup policy.
- Logs may contain identifiers; keep them private and minimize retention.

The generated inventories are code-derived metadata only and do not reveal database records.
