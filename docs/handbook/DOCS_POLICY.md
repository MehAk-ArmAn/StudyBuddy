# Documentation maintenance contract

**Principle:** Docs tell colleagues where to look and why code exists; source/tests remain authority on behavior.

For every non-trivial feature:
1. Update existing module README / handbook entry instead of creating a disconnected phase document only.
2. Link exact route/controller/service/model/test names. Avoid claims of deployment or legal certification without evidence.
3. Explain security boundaries, data model changes, rollback, and what remains unverified.
4. Put comments beside non-obvious *invariants* (tenant scoping, transactional points, upload hardening), not line-by-line narration.
5. Generate maps with `python3 tools/docs/refresh_repo_maps.py`; review diffs before staging.
6. Run `python3 tools/docs/check_docs.py` to flag missing docs and stale pointers.

`generated/*.md` are **derived from code**; edit their generator, not the output, if possible. Never include secrets, live DB rows or personal identifiers in generated files. Historical `CLAUDE.md`, roadmap and phase notes are useful but may be stale; this handbook is the current entry point.
