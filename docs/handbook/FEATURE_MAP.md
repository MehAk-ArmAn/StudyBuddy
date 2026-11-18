# Feature and ownership map

| System | Entry points / code | Notes and tests |
| --- | --- | --- |
| Public web + catalogue | `routes/web.php`, `StudyBuddyFinalPlatformController`, `StudyBuddyMiniAppPlatform` | App CMS and catalogue are connected; publisher security is high risk |
| Authentication / profiles | `StudyBuddyProfileController`, `app/Http/Middleware/EnsureAdmin.php`, `resources/views/profile/` | Current profile UI must be preserved; don't replay old ZIP blindly |
| First-party Math Sprint | `StudyBuddyMathSprintController`, `routes/studybuddy_w32_math_sprint.php` | Server-grades answers; see `tests/Feature/Learning/` |
| Reward economy / badges | `StudyBuddyRewardIntegrity`, `StudyBuddyP11FRewardsController`, `routes/studybuddy_p11f_rewards.php` | P11F added verified crediting; don't reward launch clicks |
| School applications and roles | `SchoolOrganizationController`, `routes/studybuddy_schools.php` | Invitation membership; distinct owner / school admin / staff permissions |
| School board | `StudyBuddySchoolBoardController`, `routes/studybuddy_p11b_school_os.php` | Phase 11B governance and tasks |
| School messaging | `StudyBuddySchoolMessagingController`, `routes/studybuddy_p11c_messaging.php` | Phase 11C: DMs, channels, moderation; tenant isolation |
| School Safety Center | `StudyBuddySchoolSafetyCenterController`, `routes/studybuddy_p11d_safety.php` | Phase 11D notices and confidential concerns |
| PCL Head Admin / grants | `StudyBuddyAdminAuthority`, `StudyBuddyP11TeamController`, P11A route modules | Head-only authority; check endpoint itself, not just nav link |
| Control Room UI | `resources/views/layouts/admin.blade.php`, admin views | Latest UIUX Fix 01 added Head-only Rewards discovery |
| Future calendar | proposed Phase 11E | **Not confirmed installed** in user-provided terminal logs |

**Team assignment:** Individual module owners and reviewers are currently **not specified**; use [Ownership & handoff template](HANDOFF_TEMPLATE.md) rather than inventing names. Use the generated code index to find additional controllers and tests.
