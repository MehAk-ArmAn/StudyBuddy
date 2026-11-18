# Roles and authorization checklist

Do not assume a role's label alone grants permission. Route auth, controller membership/authority and database row scoping must agree.

| Context | Checks to preserve |
| --- | --- |
| PCL Head Admin | Protected canonical account / authority check; verify route and controller |
| PCL delegated staff | Explicit scoped grants; a sidebar link is never authorization |
| School owner / school admin | Approved organization, active membership, allowed role for write/moderation/export |
| School staff / teachers | Active membership and limited actions (e.g. read notice, submit concern) |
| Student / learner | Own profile, own game sessions, own reward wallet and unlocks |
| Public user | Only explicitly public catalogue/content; no private records |

Test both **allowed and denied** cases, including cross-school IDs and forged form submissions. UI hide/show tests alone are insufficient. For the official policy, inspect current code and `tests/Feature/Admin` and `tests/Feature/School`.
