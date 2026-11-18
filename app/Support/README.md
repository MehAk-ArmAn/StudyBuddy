# Support utilities and invariants

Review especially admin authority helpers and reward integrity service. Explain idempotency, row locking, audit writes and boundary checks in comments. Security-sensitive helpers need explicit deny-path tests; don't silently reuse admin logic in public routes.
