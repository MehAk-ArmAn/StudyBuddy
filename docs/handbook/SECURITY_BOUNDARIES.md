# Security boundaries (read before modifying)

- Auth is server-side. UI visibility is a convenience, not enforcement.
- P11A Head Admin is a protected canonical identity. Delegated staff should only receive explicit grants. Never give Head Admin a browser-based arbitrary code editor/executor.
- School content is multi-tenant. Scope every query by organization **and** active membership; enforce role before write/export/download.
- Messaging attachments are sensitive; private storage and authorization are mandatory. Reporting a message does not mean it becomes publicly visible.
- Rewards must be idempotent, capped and atomic. Only server-verified game completion earns currency.
- Do not copy local account fixture passwords, API keys, SQL dumps or user information into Git, docs, issues or prompts.
- Hosted browser game ZIPs may be same-origin and powerful; don't widen iframe permissions or storage paths casually. Review `CLAUDE.md` publisher threat-model notes and current implementation.
- Production data retention, children's privacy, moderation workflows and access audits need explicit sign-off; do not label feature implementation a compliance certification.

**Escalation:** If a change weakens these boundaries, stop and request an owner/security review before deploying.
