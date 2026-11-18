# Billing, integrations, support and feedback — future contracts

These modules are **requested product capabilities**, not confirmed live providers, payment histories, connected OAuth apps, support metrics, or verified production features. Link detailed work to `PHASES_AND_RELEASE_GATES.md` and component IDs in `REACT_COMPONENT_PLACEMENT_MATRIX.md`.

## Help center + ticket/feedback pipeline (P14)

- **Support 1:** indexed published knowledge articles (`title, topic, version, audience, updated_at, body`) with full-text search fallback and accessible empty state.
- **Support 2:** signed-in or safe guest ticket form, suggestions from actual article search; priority and category controlled by validated server rules. Rate limits, CSRF and safe optional attachments.
- **Feedback 1/2/3 + Forms 10:** image-screenshot consent and redaction before upload, stars with reason chips, 0–10 recommendation survey. Collect minimum PII; school surveys scoped to organization, anonymous where appropriate.
- **Feedback 5:** admin triage queue, staff ownership, statuses (new/reviewing/replied/resolved), event history and avoid ticket disclosure across schools.
- **Feedback 6:** aggregate categories and reason chips. Any "sentiment" is a **method-labeled estimate**; no algorithmic assumption about individual children. Never show a support leaderboard until real response-time and ticket-resolution events exist.
- Suggested model chain: `support_articles` → `support_tickets` → `ticket_events`; `feedback_submissions` → `feedback_files` → `feedback_triage_events`; `feedback_aggregates` computed (not user-editable) with minimum group size thresholds.

## Billing workflow (P18 / approval hold)

- **Billing 1** invoice ledger must read real provider-backed invoices with currency, tax/discount and payment-state history; no editable amounts as financial truth.
- **Billing 2** plan/entitlement cards are backed by trusted subscription states and school contract ownership. Check access on every service call.
- **Billing 5** plan change/proration preview must match gateway-calculated estimate and require explicit user confirmation before attempting a charge.
- **Billing 4** failed payments feed comes only from verified gateway webhook events. Idempotent retries, limited/manual retry, redacted payment tokens, proper audit and escalation. No storing card data on StudyBuddy.
- **Wizard 3 / paywalls** remain hidden/disabled until real prices, legal review, refunds policy, gateway identity, and tests are approved. Do not charge children or create simulated invoices in production.

## Integrations platform (P19 / security hold)

- **Integrations 1** available-connector marketplace with “not connected / configured / needs attention” from real provider state.
- **Integrations 2** exact scope and expiry detail; revoke and reauth only after backend verification.
- **Integrations 3** API keys hashed at rest where possible, once-only plaintext display, prefix and last-used metadata only afterward; rotate/revoke must invalidate tokens.
- **Integrations 4** webhooks: exact signing secret owner, signature verification, timestamps, retry/backoff, delivery log with redaction, endpoint allowlists/SSRF mitigation.
- **Integrations 5** OAuth consent: client registration, verified redirect URI, exact scopes, PKCE, CSRF state, tenant bounds, deny flow, consent audit, revocation and incident handling; a decorative OAuth page is NOT an OAuth provider.
- Credentials in runtime `.env` / secrets manager only; never share license keys, access tokens, audit logs containing student PII, or passwords in Git.

## Settings / wizards / scheduled meetings

- **Settings Form 1** sticky unsaved-changes bar with Save/Discard. Multiple tabs must not accidentally submit stale settings; server revision token prevents overwrites.
- **Wizard 2/4/5/6** full-screen guided setup and confirm, with only approved provisioning tasks reported as actually running.
- **Scheduling 3/4/7** school attendees, deadlines, meeting availability; teacher/parent roles must be validated on the server, and booking transactions must reject conflicts and honor time zones.
