# Data models and migrations

> Automatically generated from the **current local source tree**. Do not edit by hand: run `python3 tools/docs/refresh_repo_maps.py`. No user data, credentials or database rows are read.

**Models:** 31 · **Migration files:** 40. The following is **source inventory**, not current DB schema state.

## Models

- `app/Models/AccountConnection.php`
- `app/Models/FooterItem.php`
- `app/Models/HomepageSection.php`
- `app/Models/HomepageSectionItem.php`
- `app/Models/MediaAsset.php`
- `app/Models/NavigationItem.php`
- `app/Models/Page.php`
- `app/Models/PageSection.php`
- `app/Models/PageSectionItem.php`
- `app/Models/SavedQuest.php`
- `app/Models/SchoolOrganization.php`
- `app/Models/SchoolOrganizationInvite.php`
- `app/Models/SchoolOrganizationMember.php`
- `app/Models/SiteSetting.php`
- `app/Models/StudyBuddyAppCatalogItem.php`
- `app/Models/StudyBuddyContentItem.php`
- `app/Models/StudyBuddyContentPage.php`
- `app/Models/StudyBuddyLaunchChecklistItem.php`
- `app/Models/StudyBuddyMailingSubscriber.php`
- `app/Models/StudyBuddyMiniAppPlatform.php`
- `app/Models/StudyBuddyPlatformSetting.php`
- `app/Models/StudyBuddyPointTransaction.php`
- `app/Models/StudyBuddySchoolConversation.php`
- `app/Models/StudyBuddySchoolMessage.php`
- `app/Models/StudyBuddySchoolNotice.php`
- `app/Models/StudyBuddySchoolSafetyReport.php`
- `app/Models/StudyBuddySchoolTask.php`
- `app/Models/StudyBuddySecurityEvent.php`
- `app/Models/StudyBuddySubjectAssignment.php`
- `app/Models/StudyBuddyVerificationCase.php`
- `app/Models/User.php`

## Migration files and referenced tables

| Migration | Table names matched in code |
| --- | --- |
| `database/migrations/0001_01_01_000000_create_users_table.php` | `password_reset_tokens`, `sessions`, `users` |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | `failed_jobs`, `job_batches`, `jobs` |
| `database/migrations/2026_06_16_000000_create_clean_cms_tables.php` | `footer_items`, `homepage_section_items`, `homepage_sections`, `media_assets`, `navigation_items`, `page_section_items`, `page_sections`, `pages`, `site_settings` |
| `database/migrations/2026_06_18_000001_create_trust_verification_supervision.php` | `account_connections`, `users` |
| `database/migrations/2026_06_22_000002_add_sort_order_to_media_assets.php` | `media_assets` |
| `database/migrations/2026_06_30_000100_create_saved_quests_table.php` | `saved_quests` |
| `database/migrations/2026_06_30_000300_create_studybuddy_content_studio_tables.php` | `studybuddy_app_catalog_items`, `studybuddy_content_items`, `studybuddy_content_pages` |
| `database/migrations/2026_06_30_000600_create_studybuddy_final_platform_tables.php` | `studybuddy_launch_checklist_items`, `studybuddy_mini_app_platforms`, `studybuddy_platform_settings`, `studybuddy_point_transactions` |
| `database/migrations/2026_06_30_001000_add_studybuddy_verification_hardening.php` | `studybuddy_verification_cases`, `users` |
| `database/migrations/2026_06_30_001100_unify_studybuddy_apps_platform.php` | `studybuddy_mini_app_platforms` |
| `database/migrations/2026_07_01_002000_disable_email_verification_and_polish_apps.php` | `studybuddy_mini_app_platforms` |
| `database/migrations/2026_07_02_002000_auth_role_personalization_fields.php` | `users` |
| `database/migrations/2026_07_02_003000_fix_site_settings_admin_columns.php` | `site_settings` |
| `database/migrations/2026_07_02_004000_professional_admin_site_settings_columns.php` | `site_settings` |
| `database/migrations/2026_07_02_006000_complete_control_room_site_settings_columns.php` | `site_settings` |
| `database/migrations/2026_07_02_007000_route_stabilization_safety_columns.php` | `site_settings` |
| `database/migrations/2026_07_14_000200_finalize_studybuddy_auth_columns.php` | `users` |
| `database/migrations/2026_07_14_000300_add_profile_photo_to_users.php` | `users` |
| `database/migrations/2026_07_14_000450_ensure_global_profile_photo_path.php` | `users` |
| `database/migrations/2026_07_14_000520_create_studybuddy_role_learning_tables.php` | `studybuddy_assignment_recipients`, `studybuddy_assignments`, `studybuddy_group_members`, `studybuddy_learning_groups` |
| `database/migrations/2026_07_16_001500_create_studybuddy_contact_messages_table.php` | `studybuddy_contact_messages` |
| `database/migrations/2026_07_16_200000_add_web_app_upload_fields_to_studybuddy_mini_app_platforms.php` | `studybuddy_mini_app_platforms` |
| `database/migrations/2026_07_17_120000_create_studybuddy_mailing_list_subscribers_table.php` | `studybuddy_mailing_list_subscribers` |
| `database/migrations/2026_08_20_000100_retire_demo_app_catalogue.php` | see migration source |
| `database/migrations/2026_08_21_000100_add_android_package_id_to_studybuddy_apps.php` | `studybuddy_mini_app_platforms` |
| `database/migrations/2026_08_21_000250_polish_public_copy.php` | see migration source |
| `database/migrations/2026_08_21_000275_expand_privacy_disclosure.php` | see migration source |
| `database/migrations/2026_08_21_000300_move_web_app_builds_to_private_storage.php` | see migration source |
| `database/migrations/2026_11_13_000100_create_school_workspaces_and_security_events.php` | `studybuddy_organization_invites`, `studybuddy_organization_members`, `studybuddy_organizations`, `studybuddy_security_events` |
| `database/migrations/2026_11_17_200000_create_studybuddy_learning_sessions_table.php` | `studybuddy_learning_sessions` |
| `database/migrations/2026_11_17_210000_add_connected_progress_fields_to_learning_sessions.php` | `studybuddy_learning_sessions` |
| `database/migrations/2026_11_17_220000_create_studybuddy_w32_math_sprint_tables.php` | `studybuddy_w32_achievements`, `studybuddy_w32_math_runs` |
| `database/migrations/2026_11_17_230000_create_studybuddy_w33_showcase_tables.php` | `studybuddy_w33_desktop_packages`, `studybuddy_w33_screenshots`, `studybuddy_w33_showcases` |
| `database/migrations/2026_11_17_240000_create_studybuddy_w34_subject_assignments.php` | `studybuddy_w34_subject_assignments` |
| `database/migrations/2026_11_18_120000_create_studybuddy_p11_admin_grants.php` | `studybuddy_admin_grants`, `users` |
| `database/migrations/2026_11_18_170000_create_studybuddy_school_tasks.php` | `studybuddy_school_tasks` |
| `database/migrations/2026_11_18_190000_create_studybuddy_school_messaging.php` | `studybuddy_school_conversations`, `studybuddy_school_message_reads`, `studybuddy_school_message_reports`, `studybuddy_school_messages` |
| `database/migrations/2026_11_18_200000_create_studybuddy_school_safety_center.php` | `studybuddy_school_notice_preferences`, `studybuddy_school_notice_receipts`, `studybuddy_school_notices`, `studybuddy_school_safety_reports` |
| `database/migrations/2026_11_18_220000_create_studybuddy_reward_integrity.php` | `studybuddy_reward_events`, `studybuddy_reward_rule_audits`, `studybuddy_reward_rules`, `studybuddy_reward_settings`, `studybuddy_user_reward_badges` |

Run `php artisan migrate:status` to confirm the actual target database. Never infer live user data from migration files.
