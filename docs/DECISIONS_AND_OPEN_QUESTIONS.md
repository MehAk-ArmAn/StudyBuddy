# StudyBuddy — decisions, open risks and future approvals

**User intent is not the same as implemented code.** This log captures decision status so a new developer does not silently invent permissions, pricing, providers or credentials.

| Area | Decision / user preference | Status / remaining approval |
|---|---|---|
| Canonical admin | Use `admin@studybuddy.fun` for one protected Head Admin | **Requested**, not yet reconciled; `.local` and `.fun` records both existed in user-provided local DB logs. Confirm FK dependencies and privileged owner before migratory code. |
| Admin credential | User suggested a short easy password to change later | **Not approved for implementation**: highly privileged accounts require strong private generated secrets, forced reset, MFA; never embed passwords in docs, commits or shared output. |
| Multi-admin PCL | Head Admin provisions different staff accounts with limited capabilities; other roles may be created as test users | **Planned** P11; user must approve actual grant assignments and test-account isolation. |
| PCL team | Creative: Mehak, backend: Sahil, API: Mobeen, game: Hamdan historically mentioned | **Roles in project meetings only**; not automatic admin credentials, work allocation or legal organization duties. |
| Schools | Verified organizations manage their staff, workspace, channels and resources | Existing school workspace routes present, broader School OS governance and messaging **planned**. |
| PCL oversight | User asked to quietly oversee school internal systems | Restrict to **published, justified, auditable safeguarding access**, not covert viewing or permanent access to child messages. |
| Community | All user types connected through `/community` | Current route is profile discovery; global DMs/chat not confirmed. New communications must enforce guardian/school/age boundaries. |
| Chat 2/5 and comments | DMs with read receipts; group chat with attachments; mentions and annotations | **Planned** P13/P15 with tenant, participant and file access tests. |
| AI Chat 8 | Voice composer + transcript, potential AI Study Space | **Deferred** until actual transcript service / browser capability and approved AI provider; opt-in microphone, accuracy review. |
| Visual system | Reuse P07 banners and 44 WebP archive assets, free React Bits/ReactDrops when licensed | **Partially realized** in P07–P10, exact screen-by-screen usage to be verified. |
| UI library | Many Pro blocks selected for functionality and design | **No Pro license confirmed.** Do not run registry commands with placeholder key or claim official components installed. |
| Feedback / help | Rating surveys, screenshots, support tickets, triage and insights | **Planned** P14. Need retention, consent and school-specific data access. |
| School meetings | Teacher/parent scheduling, attendees, deadlines and booking slots | **Planned** P16, requires conflict policy, time zones and notifications. |
| Billing | Invoices, plans, recovery, proration preview | **Conditional** P18, requires gateway, commercial model, legal approval, no charges for minors. |
| APIs / OAuth | Marketplace, scopes, API keys, webhooks, consent | **Conditional** P19 security architecture; no registered provider or auth scopes assumed. |
| Deployment | Complete functional, consistent build delivered phase by phase | **Ongoing** with Mac test proof per phase; no deployment claimed. |
| Git workflow | Main only, no random branches, guarded code and rollback, no data loss | **User-selected preference**; coordinate edits/reviews before main commits; never force push. |

## Unanswered implementation decisions that need explicit approval at their relevant phase

- Which existing `users.id` is the real Head Admin principal after reconciling `.fun`/`.local`? Which recovery key/operator and MFA provider?
- Who receives each PCL staff capability and who can review safeguarding incidents? Retention policy and legal basis for accessing minors' school messages?
- Whether students participate in class chat, and what guardian/school permissions and blocking/reporting policies apply?
- Whether any subscription or paid plan will be sold, in which jurisdictions and to whom? Tax, refunds, app-store and minors protections?
- Who operates OAuth/webhooks, what data is allowed to leave StudyBuddy, and which external services are vetted?
- Which exact free library components have acceptable licenses/performance for each route? Record implementation, not mere aesthetic similarity.
- Whether the school pilot has completed tenant isolation testing, security review, data processing terms and release governance.

No question above blocks **documentation planning**. It blocks only the specific risky implementation/deployment when reached.
