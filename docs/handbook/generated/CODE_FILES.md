# PHP code and test file index

> Automatically generated from the **current local source tree**. Do not edit by hand: run `python3 tools/docs/refresh_repo_maps.py`. No user data, credentials or database rows are read.

**Indexed files:** 224. Method lists are names only, not behavioral guarantees.

| File | Class / entrypoints |
| --- | --- |
| `app/Http/Controllers/AccountConnectionController.php` | class AccountConnectionController; public methods: requestParentConnection, requestTeacherConnection, approve, reject, revoke |
| `app/Http/Controllers/Admin/DashboardController.php` | class DashboardController; public methods: __invoke |
| `app/Http/Controllers/Admin/FooterItemController.php` | class FooterItemController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/HomepageSectionController.php` | class HomepageSectionController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/HomepageSectionItemController.php` | class HomepageSectionItemController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/LoginController.php` | class LoginController; public methods: show, store, showTwoFactor, verifyTwoFactor, destroy |
| `app/Http/Controllers/Admin/MediaAssetController.php` | class MediaAssetController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/NavigationItemController.php` | class NavigationItemController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/PageController.php` | class PageController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/PageSectionController.php` | class PageSectionController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/PageSectionItemController.php` | class PageSectionItemController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/SchoolOrganizationReviewController.php` | class SchoolOrganizationReviewController; public methods: index, update |
| `app/Http/Controllers/Admin/SiteSettingController.php` | class SiteSettingController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Admin/StudyBuddyAppController.php` | class StudyBuddyAppController; public methods: index, create, store, edit, update, togglePublish, toggleFeatured… |
| `app/Http/Controllers/Admin/StudyBuddyP11BSchoolOversightController.php` | class StudyBuddyP11BSchoolOversightController; public methods: show |
| `app/Http/Controllers/Admin/StudyBuddyP11FRewardsController.php` | class StudyBuddyP11FRewardsController; public methods: index, updateSettings, updateRule |
| `app/Http/Controllers/Admin/UserController.php` | class UserController; public methods: index, create, store, edit, update, destroy |
| `app/Http/Controllers/Controller.php` | class Controller |
| `app/Http/Controllers/DashboardController.php` | class DashboardController; public methods: index, updateProfile, updatePassword |
| `app/Http/Controllers/DashboardThemeController.php` | class DashboardThemeController; public methods: update |
| `app/Http/Controllers/EmailVerificationController.php` | class EmailVerificationController; public methods: notice, verify, send |
| `app/Http/Controllers/FamilyLearnerController.php` | class FamilyLearnerController; public methods: index, store, update, moment, link, destroy |
| `app/Http/Controllers/HomeController.php` | class HomeController; public methods: index |
| `app/Http/Controllers/PageController.php` | class PageController; public methods: show |
| `app/Http/Controllers/PasswordRecoveryController.php` | class PasswordRecoveryController; public methods: forgot, send, resetForm, reset |
| `app/Http/Controllers/SavedQuestController.php` | class SavedQuestController; public methods: index, store, update, destroy |
| `app/Http/Controllers/SchoolOrganizationController.php` | class SchoolOrganizationController; public methods: info, apply, store, workspace, invite, revokeInvitation, changeStaffRole… |
| `app/Http/Controllers/StudyBuddyAdminAccountController.php` | class StudyBuddyAdminAccountController; public methods: edit, updateProfile, updatePassword |
| `app/Http/Controllers/StudyBuddyAdminContactMessageController.php` | class StudyBuddyAdminContactMessageController; public methods: index, show, update |
| `app/Http/Controllers/StudyBuddyAdminContentController.php` | class StudyBuddyAdminContentController; public methods: index, updatePage, updateItem, updateApp |
| `app/Http/Controllers/StudyBuddyAdminHealthController.php` | class StudyBuddyAdminHealthController; public methods: index |
| `app/Http/Controllers/StudyBuddyAdminHomepageCmsController.php` | class StudyBuddyAdminHomepageCmsController; public methods: index, updateSection, updateItem |
| `app/Http/Controllers/StudyBuddyAdminRoleToolsController.php` | class StudyBuddyAdminRoleToolsController; public methods: index |
| `app/Http/Controllers/StudyBuddyAssignedWorkController.php` | class assignment; public methods: complete |
| `app/Http/Controllers/StudyBuddyCommandCenterController.php` | class StudyBuddyCommandCenterController; public methods: __invoke |
| `app/Http/Controllers/StudyBuddyConnectedProgressController.php` | class StudyBuddyConnectedProgressController; public methods: start, heartbeat, finish, leave, insights |
| `app/Http/Controllers/StudyBuddyContactController.php` | class StudyBuddyContactController; public methods: show, store |
| `app/Http/Controllers/StudyBuddyControlRoomBridgeController.php` | class StudyBuddyControlRoomBridgeController; public methods: users, verifications, contentStudio |
| `app/Http/Controllers/StudyBuddyControlRoomController.php` | class StudyBuddyControlRoomController; public methods: index |
| `app/Http/Controllers/StudyBuddyDailyPulseController.php` | class StudyBuddyDailyPulseController; public methods: show, addPlan, deletePlan, markRead, preferences |
| `app/Http/Controllers/StudyBuddyExperienceController.php` | class StudyBuddyExperienceController; public methods: learningHub, learningPaths, rewards, parentsCenter, teacherStudio, safetySupport, appEcosystem |
| `app/Http/Controllers/StudyBuddyFinalAdminController.php` | class StudyBuddyFinalAdminController; public methods: index, updateSettings, updateApp, updateChecklist, quickAward |
| `app/Http/Controllers/StudyBuddyFinalPlatformController.php` | class StudyBuddyFinalPlatformController; public methods: apps, appDetail, adminAppPreview, appLaunchpadRedirect, webPlay, adminWebPlayPreview, pointsWallet… |
| `app/Http/Controllers/StudyBuddyInfoPageController.php` | class StudyBuddyInfoPageController; public methods: show, roles |
| `app/Http/Controllers/StudyBuddyLearningActivityController.php` | class StudyBuddyLearningActivityController; public methods: index, start, finish, leave, destroy |
| `app/Http/Controllers/StudyBuddyLogoutController.php` | class StudyBuddyLogoutController; public methods: confirm, destroy, goodbye |
| `app/Http/Controllers/StudyBuddyMailingListController.php` | class StudyBuddyMailingListController; public methods: subscribe, adminIndex, adminUpdate, adminDestroy, adminExport |
| `app/Http/Controllers/StudyBuddyMathSprintController.php` | class StudyBuddyMathSprintController; public methods: index, start, submit, result |
| `app/Http/Controllers/StudyBuddyOnboardingController.php` | class StudyBuddyOnboardingController; public methods: show, save |
| `app/Http/Controllers/StudyBuddyP11TeamController.php` | class StudyBuddyP11TeamController; public methods: index, create, update, revoke |
| `app/Http/Controllers/StudyBuddyProfileController.php` | class StudyBuddyProfileController; public methods: edit, update, show, community |
| `app/Http/Controllers/StudyBuddyResourceStudioController.php` | class StudyBuddyResourceStudioController; public methods: index, createNote, updateNote, deleteNote, upload, download, preview… |
| `app/Http/Controllers/StudyBuddyRoleDashboardActionController.php` | class StudyBuddyRoleDashboardActionController; public methods: regenerateConnectCode, addChild, removeChild, updateTeacherOrganization, createClass, addStudent, createAssignment |
| `app/Http/Controllers/StudyBuddySchoolBoardController.php` | class StudyBuddySchoolBoardController; public methods: index, store, update, archive |
| `app/Http/Controllers/StudyBuddySchoolMessagingController.php` | class StudyBuddySchoolMessagingController; public methods: index, show, createChannel, startDirect, send, markRead, download… |
| `app/Http/Controllers/StudyBuddySchoolSafetyCenterController.php` | class StudyBuddySchoolSafetyCenterController; public methods: index, postNotice, markRead, archiveNotice, updatePreference, reportIssue, reviewIssue… |
| `app/Http/Controllers/StudyBuddySearchController.php` | class StudyBuddySearchController; public methods: index, suggest |
| `app/Http/Controllers/StudyBuddyShellAdminController.php` | class StudyBuddyShellAdminController; public methods: index, update |
| `app/Http/Controllers/StudyBuddyVerificationController.php` | class StudyBuddyVerificationController; public methods: show, submit, adminIndex, adminUpdate |
| `app/Http/Controllers/StudyBuddyW33ShowcaseController.php` | class StudyBuddyW33ShowcaseController; public methods: edit, update, uploadScreenshots, updateScreenshot, deleteScreenshot, adminImage, publicImage… |
| `app/Http/Controllers/StudyBuddyW34SubjectsController.php` | class StudyBuddyW34SubjectsController; public methods: index, show, edit, update |
| `app/Http/Controllers/StudyBuddyWebAppAssetController.php` | class StudyBuddyWebAppAssetController; public methods: __invoke, preview |
| `app/Http/Controllers/UserAccessController.php` | class UserAccessController; public methods: showLogin, showRegister, login, register, verificationNotice, resendVerification, showLogout… |
| `app/Http/Middleware/EnsureAdmin.php` | class EnsureAdmin; public methods: handle |
| `app/Http/Middleware/StudyBuddySecurityHeaders.php` | class StudyBuddySecurityHeaders; public methods: handle |
| `app/Http/Requests/Admin/StudyBuddyAppRequest.php` | class StudyBuddyAppRequest; public methods: authorize, rules, messages, attributes, withValidator |
| `app/Models/AccountConnection.php` | class AccountConnection; public methods: requester, target, isApproved |
| `app/Models/FooterItem.php` | class FooterItem |
| `app/Models/HomepageSection.php` | class HomepageSection; public methods: items |
| `app/Models/HomepageSectionItem.php` | class HomepageSectionItem; public methods: section |
| `app/Models/MediaAsset.php` | class MediaAsset |
| `app/Models/NavigationItem.php` | class NavigationItem |
| `app/Models/Page.php` | class Page; public methods: sections |
| `app/Models/PageSection.php` | class PageSection; public methods: page, items |
| `app/Models/PageSectionItem.php` | class PageSectionItem; public methods: section |
| `app/Models/SavedQuest.php` | class SavedQuest; public methods: user, getStatusLabelAttribute |
| `app/Models/SchoolOrganization.php` | class SchoolOrganization; public methods: owner, reviewer, members, invitations |
| `app/Models/SchoolOrganizationInvite.php` | class SchoolOrganizationInvite; public methods: organization, inviter |
| `app/Models/SchoolOrganizationMember.php` | class SchoolOrganizationMember; public methods: organization, user |
| `app/Models/SiteSetting.php` | class SiteSetting; public methods: getValue |
| `app/Models/StudyBuddyAppCatalogItem.php` | class StudyBuddyAppCatalogItem |
| `app/Models/StudyBuddyContentItem.php` | class StudyBuddyContentItem |
| `app/Models/StudyBuddyContentPage.php` | class StudyBuddyContentPage; public methods: items |
| `app/Models/StudyBuddyLaunchChecklistItem.php` | class StudyBuddyLaunchChecklistItem |
| `app/Models/StudyBuddyMailingSubscriber.php` | class StudyBuddyMailingSubscriber; public methods: scopeActive |
| `app/Models/StudyBuddyMiniAppPlatform.php` | class StudyBuddyMiniAppPlatform; public methods: subjectAssignment, scopeActive, scopeOrdered, getAvailablePlatformsAttribute, visibleForRole, hasPublishedWebApp, launcherUrl… |
| `app/Models/StudyBuddyPlatformSetting.php` | class StudyBuddyPlatformSetting; public methods: publicMap |
| `app/Models/StudyBuddyPointTransaction.php` | class StudyBuddyPointTransaction; public methods: user |
| `app/Models/StudyBuddySchoolConversation.php` | class StudyBuddySchoolConversation; public methods: organization, messages |
| `app/Models/StudyBuddySchoolMessage.php` | class StudyBuddySchoolMessage; public methods: conversation, author |
| `app/Models/StudyBuddySchoolNotice.php` | class StudyBuddySchoolNotice; public methods: organization, poster |
| `app/Models/StudyBuddySchoolSafetyReport.php` | class StudyBuddySchoolSafetyReport; public methods: reporter, handler |
| `app/Models/StudyBuddySchoolTask.php` | class StudyBuddySchoolTask; public methods: organization, assignee, creator |
| `app/Models/StudyBuddySecurityEvent.php` | class StudyBuddySecurityEvent; public methods: record |
| `app/Models/StudyBuddySubjectAssignment.php` | class StudyBuddySubjectAssignment; public methods: app |
| `app/Models/StudyBuddyVerificationCase.php` | class StudyBuddyVerificationCase; public methods: user, reviewer |
| `app/Models/User.php` | class User; public methods: requestedConnections, receivedConnections, pointTransactions, savedQuests, schoolMemberships, isAdult, normalizedRole… |
| `app/Services/StudyBuddyW33Cleanup.php` | class StudyBuddyW33Cleanup; public methods: forApp |
| `app/Services/StudyBuddyW33DesktopKit.php` | class StudyBuddyW33DesktopKit; public methods: create |
| `app/Services/StudyBuddyW33Pwa.php` | class StudyBuddyW33Pwa; public methods: manifest, serviceWorker, decorate |
| `app/Services/StudyBuddyWebAppPublisher.php` | class StudyBuddyWebAppPublisher; public methods: buildRoot, buildDirectory, publish, renamePublishedSlug, remove, deleteStoredPackage, hasLocalCanvasKit… |
| `app/Support/AdminLabel.php` | class AdminLabel; public methods: humanize, isBoolean, isImage, isTechnical, booleanLabel, describe |
| `app/Support/StudyBuddyAdminAuthority.php` | class StudyBuddyAdminAuthority; public methods: schemaReady, headId, isHead, can, isProtected, requiredPermission |
| `app/Support/StudyBuddyAssetVersion.php` | class StudyBuddyAssetVersion; public methods: for |
| `app/Support/StudyBuddyClassProgress.php` | class assignment; public methods: forTeacher |
| `app/Support/StudyBuddyDailyPulse.php` | class StudyBuddyDailyPulse; public methods: profile, settings, plans, assignments, calendar, notices, weekly |
| `app/Support/StudyBuddyLearningSnapshot.php` | class StudyBuddyLearningSnapshot; public methods: forUser |
| `app/Support/StudyBuddyResourceStudio.php` | class StudyBuddyResourceStudio; public methods: profile, notes, files, find, persist, storageDirectory, allowedMime… |
| `app/Support/StudyBuddyRewardIntegrity.php` | class StudyBuddyRewardIntegrity; public methods: installed, verifiedMathRun, headAdjustment, isHead, badgeNames |
| `database/migrations/0001_01_01_000000_create_users_table.php` | class extends; public methods: up, down |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | class extends; public methods: up, down |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_16_000000_create_clean_cms_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_18_000001_create_trust_verification_supervision.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_22_000002_add_sort_order_to_media_assets.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_30_000100_create_saved_quests_table.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_30_000300_create_studybuddy_content_studio_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_30_000600_create_studybuddy_final_platform_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_30_001000_add_studybuddy_verification_hardening.php` | class extends; public methods: up, down |
| `database/migrations/2026_06_30_001100_unify_studybuddy_apps_platform.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_01_002000_disable_email_verification_and_polish_apps.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_02_002000_auth_role_personalization_fields.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_02_003000_fix_site_settings_admin_columns.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_02_004000_professional_admin_site_settings_columns.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_02_006000_complete_control_room_site_settings_columns.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_02_007000_route_stabilization_safety_columns.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_14_000200_finalize_studybuddy_auth_columns.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_14_000300_add_profile_photo_to_users.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_14_000450_ensure_global_profile_photo_path.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_14_000520_create_studybuddy_role_learning_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_16_001500_create_studybuddy_contact_messages_table.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_16_200000_add_web_app_upload_fields_to_studybuddy_mini_app_platforms.php` | class extends; public methods: up, down |
| `database/migrations/2026_07_17_120000_create_studybuddy_mailing_list_subscribers_table.php` | class extends; public methods: up, down |
| `database/migrations/2026_08_20_000100_retire_demo_app_catalogue.php` | class extends; public methods: up, down |
| `database/migrations/2026_08_21_000100_add_android_package_id_to_studybuddy_apps.php` | class extends; public methods: up, down |
| `database/migrations/2026_08_21_000250_polish_public_copy.php` | class extends; public methods: up, down |
| `database/migrations/2026_08_21_000275_expand_privacy_disclosure.php` | class extends; public methods: up, down |
| `database/migrations/2026_08_21_000300_move_web_app_builds_to_private_storage.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_13_000100_create_school_workspaces_and_security_events.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_17_200000_create_studybuddy_learning_sessions_table.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_17_210000_add_connected_progress_fields_to_learning_sessions.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_17_220000_create_studybuddy_w32_math_sprint_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_17_230000_create_studybuddy_w33_showcase_tables.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_17_240000_create_studybuddy_w34_subject_assignments.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_18_120000_create_studybuddy_p11_admin_grants.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_18_170000_create_studybuddy_school_tasks.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_18_190000_create_studybuddy_school_messaging.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_18_200000_create_studybuddy_school_safety_center.php` | class extends; public methods: up, down |
| `database/migrations/2026_11_18_220000_create_studybuddy_reward_integrity.php` | class extends; public methods: up, down |
| `database/seeders/DatabaseSeeder.php` | class DatabaseSeeder; public methods: run |
| `database/seeders/PublishReadyContentSeeder.php` | class PublishReadyContentSeeder; public methods: run |
| `database/seeders/StudyBuddyAdminSeeder.php` | class StudyBuddyAdminSeeder; public methods: run |
| `database/seeders/StudyBuddyCopySeeder.php` | class StudyBuddyCopySeeder; public methods: run |
| `database/seeders/StudyBuddyLocalDemoAppsSeeder.php` | class StudyBuddyLocalDemoAppsSeeder; public methods: run |
| `database/seeders/StudyBuddyPublishContentSeeder.php` | class StudyBuddyPublishContentSeeder; public methods: run |
| `database/seeders/StudyBuddyRemoveLocalDemoAppsSeeder.php` | class StudyBuddyRemoveLocalDemoAppsSeeder; public methods: run |
| `database/seeders/StudyBuddyShellDefaultsSeeder.php` | class StudyBuddyShellDefaultsSeeder; public methods: run |
| `database/seeders/StudyBuddyShellSafeLinksSeeder.php` | class StudyBuddyShellSafeLinksSeeder; public methods: run |
| `routes/console.php` | route registrations |
| `routes/studybuddy.php` | route registrations |
| `routes/studybuddy_admin_health_cms.php` | route registrations |
| `routes/studybuddy_admin_pro_final.php` | route registrations |
| `routes/studybuddy_connected_progress.php` | route registrations |
| `routes/studybuddy_contact_inbox_final.php` | route registrations |
| `routes/studybuddy_dashboard_profile_final.php` | route registrations |
| `routes/studybuddy_family_learners.php` | route registrations |
| `routes/studybuddy_info_pages_final.php` | route registrations |
| `routes/studybuddy_learning_activity.php` | route registrations |
| `routes/studybuddy_logout.php` | route registrations |
| `routes/studybuddy_mailing_list.php` | route registrations |
| `routes/studybuddy_p08_onboarding.php` | route registrations |
| `routes/studybuddy_p09_pulse.php` | route registrations |
| `routes/studybuddy_p10_resources.php` | route registrations |
| `routes/studybuddy_p11_admin.php` | route registrations |
| `routes/studybuddy_p11_console.php` | route registrations |
| `routes/studybuddy_p11b_school_os.php` | route registrations |
| `routes/studybuddy_p11c_messaging.php` | route registrations |
| `routes/studybuddy_p11d_safety.php` | route registrations |
| `routes/studybuddy_p11f_rewards.php` | route registrations |
| `routes/studybuddy_role_dashboard_tools.php` | route registrations |
| `routes/studybuddy_schools.php` | route registrations |
| `routes/studybuddy_search_final.php` | route registrations |
| `routes/studybuddy_w32_math_sprint.php` | route registrations |
| `routes/studybuddy_w33.php` | route registrations |
| `routes/studybuddy_w34.php` | route registrations |
| `routes/studybuddy_w36.php` | route registrations |
| `routes/web.php` | route registrations |
| `tests/CreatesApplication.php` | class CreatesApplication; public methods: createApplication |
| `tests/Feature/Admin/AdminAccountTest.php` | class AdminAccountTest; public methods: test_the_default_seeder_creates_an_administrator, test_the_seeded_password_is_hashed_and_never_stored_in_plain_text, test_seeding_twice_does_not_create_a_second_administrator, test_an_administrator_from_an_earlier_build_is_adopted_not_duplicated, test_an_admin_can_sign_in_and_reach_the_dashboard, test_a_signed_in_admin_can_reach_every_main_admin_area, test_the_wrong_password_is_rejected… |
| `tests/Feature/Admin/AdminUiRedesignTest.php` | class AdminUiRedesignTest; public methods: test_the_admin_shell_uses_grouped_canonical_navigation, test_dashboard_metrics_are_backed_by_real_database_values, test_add_app_is_a_five_step_editor_with_persistent_actions, test_published_editor_uses_a_safe_default_submit_action, test_apps_library_has_premium_empty_and_product_row_states |
| `tests/Feature/Admin/AppsCmsIntegrityTest.php` | class AppsCmsIntegrityTest; public methods: test_content_studio_has_one_clear_route_to_the_canonical_apps_workspace, test_apps_pagination_uses_the_local_accessible_controls, test_store_and_support_destinations_must_be_secure_urls, test_typed_artwork_paths_cannot_traverse_or_be_protocol_relative, test_replacing_one_shared_artwork_field_keeps_the_other_file, test_new_artwork_uses_the_updated_slug_directory, test_failed_replacement_zip_keeps_the_last_good_draft_build_private… |
| `tests/Feature/Admin/AppsCmsTest.php` | class AppsCmsTest; public methods: test_guests_cannot_reach_the_apps_cms, test_signed_in_non_admins_cannot_reach_the_apps_cms, test_a_non_admin_cannot_create_an_app, test_an_admin_can_open_the_list_and_the_create_form, test_an_admin_can_create_an_app_and_the_slug_is_generated, test_a_published_app_reaches_the_public_pages, test_an_unpublished_app_is_hidden_from_the_public_pages… |
| `tests/Feature/Admin/StudyBuddyPack11AccessTest.php` | class StudyBuddyPack11AccessTest; public methods: test_email_string_and_role_field_do_not_grant_platform_access, test_claimed_head_can_access_and_cannot_be_deleted_or_demoted_through_ordinary_routes, test_legacy_admin_without_scoped_grant_is_denied_after_head_claim, test_staff_only_receives_specific_school_permission_and_cannot_promote_others, test_standard_user_management_does_not_accept_admin_promotion_and_marks_test_accounts, test_head_claim_command_anchors_existing_row_without_deleting_legacy_account, test_real_accounts_are_not_deleted_by_ordinary_admin_directory… |
| `tests/Feature/Apps/BrowserLauncherTest.php` | class BrowserLauncherTest; public methods: test_no_browser_button_when_browser_play_is_disabled, test_no_browser_button_when_enabled_but_nothing_is_published, test_no_browser_button_when_the_build_files_are_missing, test_an_uploaded_build_shows_the_browser_button_and_launches, test_the_published_build_is_actually_served, test_inactive_build_assets_are_private_and_admin_preview_is_not_cached, test_legitimate_double_dot_filename_is_served_but_parent_segments_are_not… |
| `tests/Feature/Apps/EmptyCatalogueTest.php` | class EmptyCatalogueTest; public methods: test_migrations_leave_no_apps_behind, test_the_legacy_catalogue_table_is_empty_too, test_seeding_does_not_recreate_the_demo_apps, test_the_apps_page_shows_an_intentional_empty_state, test_the_apps_page_hides_its_filters_when_there_is_nothing_to_filter, test_the_apps_page_does_not_advertise_the_admin_panel, test_the_homepage_works_and_shows_no_app_cards… |
| `tests/Feature/Apps/FlutterWebBuildTest.php` | class FlutterWebBuildTest; public methods: test_the_browser_zip_limit_is_configured_at_60_mb, test_a_zip_over_the_limit_is_rejected_in_plain_language, test_publishing_repoints_a_root_base_href_at_the_app_build_path, test_the_same_build_published_under_another_slug_gets_that_slug, test_a_base_tag_is_inserted_when_the_build_has_none, test_the_served_base_href_follows_the_admin_preview_mount, test_a_build_published_before_this_existed_is_repaired_as_it_is_served… |
| `tests/Feature/Apps/StudyBuddyAcademicCatalogueBridgeTest.php` | class StudyBuddyAcademicCatalogueBridgeTest; public methods: test_canonical_apps_page_links_to_real_assigned_subject, test_unpublished_assignments_do_not_create_subject_links |
| `tests/Feature/Apps/StudyBuddyLiveCatalogueTest.php` | class StudyBuddyLiveCatalogueTest; public methods: test_homepage_counts_actual_active_apps_not_demo_concepts, test_homepage_has_truthful_empty_state |
| `tests/Feature/Apps/StudyBuddyW33StoreTest.php` | class StudyBuddyW33StoreTest; public methods: test_only_admin_can_edit_showcase_or_upload_packages, test_admin_updates_showcase_and_public_page_displays_real_highlights, test_screen_captures_are_scoped_to_published_app_and_private_when_unpublished, test_native_desktop_download_exists_only_for_its_published_app, test_installable_manifest_and_electron_kit_use_published_web_build_without_rewriting_the_game |
| `tests/Feature/Apps/StudyBuddyW34SubjectsTest.php` | class StudyBuddyW34SubjectsTest; public methods: test_all_twenty_eight_taxonomy_subjects_exist_without_public_empty_categories, test_admin_can_classify_real_app_and_visitors_can_find_it, test_unpublished_game_does_not_appear_in_subject_discovery, test_regular_user_cannot_change_classifications, test_bad_subject_is_rejected_and_existing_category_is_not_rewritten |
| `tests/Feature/Architecture/StudyBuddyUIUXIntegrationTest.php` | class StudyBuddyUIUXIntegrationTest; public methods: test_rewards_admin_navigation_is_discoverable_only_behind_head_check, test_rewards_screen_uses_shared_admin_layout_and_css_not_inline_styles |
| `tests/Feature/Architecture/StudyBuddyUnifiedPageTest.php` | class StudyBuddyUnifiedPageTest; public methods: test_homepage_has_one_laravel_shell_and_no_demo_app_shelf, test_real_published_app_comes_from_one_catalogue |
| `tests/Feature/Auth/StudyBuddyW22RegistrationTest.php` | class StudyBuddyW22RegistrationTest; public methods: test_registration_page_displays_all_roles_and_learning_setup, test_student_date_of_birth_is_required_to_enforce_child_safeguards, test_student_preferences_are_saved_under_dashboard_profile_keys, test_under_13_direct_registration_requires_parent_managed_setup, test_minor_cannot_select_adult_role, test_teacher_preferences_do_not_bypass_verification |
| `tests/Feature/Auth/StudyBuddyW23TeenIndependentTest.php` | class StudyBuddyW23TeenIndependentTest; public methods: test_a_15_year_old_can_register_as_an_independent_learner_without_adult_privileges, test_13_year_old_independent_learners_are_eligible, test_under_13_independent_registration_is_rejected_without_creating_an_account, test_adult_learner_still_requires_existing_adult_verification, test_underage_parent_and_teacher_accounts_remain_blocked, test_register_page_says_teens_can_learn_independently |
| `tests/Feature/Brand/BrandIdentityTest.php` | class BrandIdentityTest; public methods: test_the_brand_name_and_slogan_are_the_canonical_ones, test_a_fresh_install_seeds_the_correct_brand_identity, test_the_homepage_shows_the_brand_name_and_slogan, test_every_brand_icon_file_ships_with_the_app, iconKeys, test_the_pwa_icons_are_the_sizes_they_claim_to_be, test_the_head_declares_a_working_favicon_and_touch_icon… |
| `tests/Feature/Brand/PublicCopyTest.php` | class PublicCopyTest; public methods: test_no_public_page_uses_developer_or_cms_language, test_no_public_page_contains_placeholder_text, test_learner_pages_do_not_expose_internal_or_unfinished_language, test_no_public_page_invents_social_proof, test_a_published_app_catalogue_and_detail_page_keep_product_copy_clean, test_the_legal_pages_carry_real_content, test_internal_release_pages_are_not_publicly_readable… |
| `tests/Feature/Family/StudyBuddyW24FamilyLearnerTest.php` | class StudyBuddyW24FamilyLearnerTest; public methods: test_guests_cannot_access_private_family_profiles, test_verified_adult_parent_can_create_a_profile_for_a_very_young_child, test_unverified_parent_and_teacher_cannot_manage_child_profiles, test_parent_cannot_access_another_parents_private_learner, test_family_logged_moments_change_journey_without_awarding_points, test_verified_parent_can_connect_a_teen_without_losing_family_history, test_wrong_connect_code_cannot_claim_private_learning_history |
| `tests/Feature/Learning/StudyBuddyClassProgressTest.php` | class StudyBuddyClassProgressTest; public methods: test_guest_cannot_complete_tasks, test_verified_student_can_complete_own_connected_task_and_teacher_sees_real_class_progress, test_other_students_and_unconnected_learners_cannot_complete_someone_elses_assignment, test_unverified_teachers_and_other_class_owners_cannot_see_progress, test_pending_unlinked_roster_does_not_count_and_no_grades_are_shared, test_unverified_student_cannot_submit_completion |
| `tests/Feature/Learning/StudyBuddyLearningDashboardTest.php` | class StudyBuddyLearningDashboardTest; public methods: test_guest_cannot_view_private_dashboard, test_student_sees_empty_truthful_snapshot_without_fake_scores, test_only_signed_in_learner_sees_their_actual_journal_and_grades, test_parent_and_teacher_dashboards_do_not_show_private_student_snapshot, test_ungraded_math_attempts_are_not_presented_as_scores |
| `tests/Feature/Learning/StudyBuddyPack07DashboardTest.php` | class StudyBuddyPack07DashboardTest; public methods: test_student_dashboard_contains_real_data_command_center, test_teacher_dashboard_counts_all_owned_assignments_not_only_recent_fourteen, test_parent_dashboard_shows_family_action_without_private_learner_snapshot |
| `tests/Feature/Learning/StudyBuddyPack08OnboardingTest.php` | class StudyBuddyPack08OnboardingTest; public methods: test_private_onboarding_requires_login, test_a_student_sees_six_steps_real_visuals_and_private_preview, test_saving_preferences_keeps_prior_profile_customizations_and_authorization, test_subjects_and_goal_save_only_to_the_signed_in_account, test_bad_subject_and_missing_privacy_review_are_rejected, test_complete_requires_all_five_steps_and_only_updates_private_setup |
| `tests/Feature/Learning/StudyBuddyPack09PulseTest.php` | class StudyBuddyPack09PulseTest; public methods: test_my_day_and_post_actions_are_private, test_plan_is_persisted_in_only_its_owners_existing_profile, test_only_owner_can_remove_a_plan_and_invalid_input_is_rejected, test_notification_preferences_are_account_only_and_do_not_change_privileges, test_verified_assignment_deadlines_are_visible_only_to_the_connected_learner, test_unread_notification_can_only_be_acknowledged_by_its_owner, test_weekly_stats_count_only_the_signed_in_account_recorded_events |
| `tests/Feature/Learning/StudyBuddyPack10ResourceStudioTest.php` | class StudyBuddyPack10ResourceStudioTest; public methods: test_guests_cannot_read_write_or_upload_resources, test_markdown_notes_are_saved_only_for_the_owner_and_existing_preferences_survive, test_bad_input_and_markup_remain_non_executable, test_file_is_on_private_disk_with_owner_only_download_and_preview_access, test_rejects_unsafe_file_extensions_and_preserves_other_account_data |
| `tests/Feature/Learning/StudyBuddyPack11FRewardsTest.php` | class StudyBuddyPack11FRewardsTest; public methods: test_verified_math_sprint_rewards_real_balance_and_ledger, test_very_fast_ungraded_or_wrong_sessions_cannot_mint_rewards, test_duplicate_completion_does_not_credit_twice, test_daily_cap_is_enforced_even_when_games_are_repeated, test_hidden_profile_cosmetics_require_actual_badge_and_then_unlock, test_legacy_click_to_claim_is_disabled_for_real_and_unpublished_apps, test_non_head_cannot_open_governance_console |
| `tests/Feature/Learning/StudyBuddyStudySpaceTest.php` | class StudyBuddyStudySpaceTest; public methods: test_guest_is_redirected_from_private_study_space, test_signed_in_learner_can_open_react_shell_without_fake_ai_responses, test_learner_dashboard_links_to_the_private_study_space |
| `tests/Feature/Learning/StudyBuddyW30LearningActivityTest.php` | class StudyBuddyW30LearningActivityTest; public methods: test_guests_cannot_see_or_write_private_sessions, test_learner_can_start_and_finish_without_earning_points, test_one_open_session_per_account_and_idempotent_finish, test_other_accounts_cannot_read_modify_or_delete_private_sessions, test_unpublished_apps_cannot_be_logged_as_played, test_learner_can_delete_own_history |
| `tests/Feature/Learning/StudyBuddyW31ConnectedProgressTest.php` | class StudyBuddyW31ConnectedProgressTest; public methods: test_guests_cannot_create_visits_or_view_private_insights, test_logged_in_launcher_creates_private_w30_session_without_awarding_points, test_unpublished_apps_and_disallowed_roles_are_rejected, test_different_active_app_requires_explicit_close, test_one_account_cannot_write_to_another_account_session, test_server_timed_heartbeat_and_finish_are_observations_not_scores, test_private_insights_show_only_this_accounts_connected_history |
| `tests/Feature/Learning/StudyBuddyW32MathSprintTest.php` | class StudyBuddyW32MathSprintTest; public methods: test_guests_cannot_view_or_submit_math_sprint, test_start_records_one_private_linked_learning_session_and_resumes_without_duplicates, test_five_server_graded_answers_earn_two_once_only_and_do_not_mint_points, test_wrong_answers_cannot_claim_a_badge, test_four_correct_earns_math_star_but_not_perfect_orbit, test_other_learner_cannot_submit_or_read_private_results, test_expired_run_does_not_award_a_badge_and_closes_the_session… |
| `tests/Feature/School/StudyBuddyPack11BSchoolOSTest.php` | class StudyBuddyPack11BSchoolOSTest; public methods: test_board_is_private_to_approved_school_members, test_manager_creates_and_assigns_real_staff_task_and_external_assignee_is_rejected, test_teacher_may_update_only_the_lane_of_own_task, test_cross_school_task_ids_are_not_accessible, test_manager_archives_tasks_instead_of_deleting_records, test_only_school_owner_may_change_verified_staff_roles_and_not_owners, test_pcl_school_reviewer_sees_governance_but_not_staff_board |
| `tests/Feature/School/StudyBuddyPack11CMessagingTest.php` | class StudyBuddyPack11CMessagingTest; public methods: test_staff_channels_are_private_and_scoped_to_an_approved_school, test_teacher_cannot_create_channels_but_school_manager_can, test_only_current_staff_can_send_and_read_messages, test_cross_school_message_ids_and_threads_cannot_be_read_or_reported, test_direct_messages_are_visible_only_to_both_participants, test_receipts_advance_only_when_authorized_member_marks_read, test_reported_messages_are_moderated_without_opening_full_direct_message… |
| `tests/Feature/School/StudyBuddyPack11DSafetyCenterTest.php` | class StudyBuddyPack11DSafetyCenterTest; public methods: test_private_safety_center_requires_current_approved_membership, test_only_managers_create_staff_notices_and_receipts_are_saved, test_only_valid_school_member_can_acknowledge_notice, test_notices_and_reports_cannot_be_modified_across_schools, test_teachers_can_report_a_concern_but_only_managers_can_resolve, test_ordinary_teacher_cannot_see_another_teachers_concern, test_badge_mute_never_hides_urgent_notice… |
| `tests/TestCase.php` | class TestCase |
| `tests/Unit/StudyBuddyAssetVersionTest.php` | class StudyBuddyAssetVersionTest; public methods: test_missing_public_asset_does_not_throw_and_uses_stable_version, test_present_public_asset_uses_its_real_modified_time |
| `tests/Unit/StudyBuddyMiniAppPlatformTest.php` | class StudyBuddyMiniAppPlatformTest; public methods: test_hero_image_falls_back_to_the_card_image, test_hero_image_wins_when_both_are_set, test_hero_image_is_null_when_no_artwork_is_set, test_accent_colours_are_stable_for_the_same_app, test_different_apps_can_get_different_accents, test_initials_use_the_first_two_words, test_availability_labels_are_human_readable… |
