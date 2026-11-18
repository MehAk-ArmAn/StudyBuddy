# Registered routes

> Automatically generated from the **current local source tree**. Do not edit by hand: run `python3 tools/docs/refresh_repo_maps.py`. No user data, credentials or database rows are read.

**Actual Laravel route registrations:** 311. This command examines routes; it does not assert each UI page is operational.

| Method | URI | Name | Action | Middleware |
| --- | --- | --- | --- | --- |
| `GET\|HEAD` | `/` | `home` | `App\Http\Controllers\HomeController@index` | web |
| `GET\|HEAD` | `about` | `studybuddy.info.about` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `about-us` | `pages.about-us` | `App\Http\Controllers\PageController@show` | web |
| `GET\|HEAD` | `admin/control-room` | `admin.control-room.index` | `App\Http\Controllers\StudyBuddyControlRoomController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/account` | `admin.control-room.account.edit` | `App\Http\Controllers\StudyBuddyAdminAccountController@edit` | web, auth, admin |
| `PATCH` | `admin/control-room/account/password` | `admin.control-room.account.password` | `App\Http\Controllers\StudyBuddyAdminAccountController@updatePassword` | web, auth, admin |
| `PATCH` | `admin/control-room/account/profile` | `admin.control-room.account.profile` | `App\Http\Controllers\StudyBuddyAdminAccountController@updateProfile` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps` | `admin.control-room.apps.index` | `App\Http\Controllers\Admin\StudyBuddyAppController@index` | web, auth, admin |
| `POST` | `admin/control-room/apps` | `admin.control-room.apps.store` | `App\Http\Controllers\Admin\StudyBuddyAppController@store` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/create` | `admin.control-room.apps.create` | `App\Http\Controllers\Admin\StudyBuddyAppController@create` | web, auth, admin |
| `POST` | `admin/control-room/apps/reorder` | `admin.control-room.apps.reorder` | `App\Http\Controllers\Admin\StudyBuddyAppController@reorder` | web, auth, admin |
| `PUT` | `admin/control-room/apps/{app}` | `admin.control-room.apps.update` | `App\Http\Controllers\Admin\StudyBuddyAppController@update` | web, auth, admin |
| `DELETE` | `admin/control-room/apps/{app}` | `admin.control-room.apps.destroy` | `App\Http\Controllers\Admin\StudyBuddyAppController@destroy` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/edit` | `admin.control-room.apps.edit` | `App\Http\Controllers\Admin\StudyBuddyAppController@edit` | web, auth, admin |
| `PATCH` | `admin/control-room/apps/{app}/featured` | `admin.control-room.apps.featured` | `App\Http\Controllers\Admin\StudyBuddyAppController@toggleFeatured` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/preview` | `admin.control-room.apps.preview` | `App\Http\Controllers\StudyBuddyFinalPlatformController@adminAppPreview` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/preview/build/{path?}` | `admin.control-room.apps.preview.asset` | `App\Http\Controllers\StudyBuddyWebAppAssetController@preview` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/preview/play` | `admin.control-room.apps.preview.play` | `App\Http\Controllers\StudyBuddyFinalPlatformController@adminWebPlayPreview` | web, auth, admin |
| `PATCH` | `admin/control-room/apps/{app}/publish` | `admin.control-room.apps.publish` | `App\Http\Controllers\Admin\StudyBuddyAppController@togglePublish` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/showcase` | `studybuddy.w33.admin.edit` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@edit` | web, auth, admin |
| `POST` | `admin/control-room/apps/{app}/showcase` | `studybuddy.w33.admin.update` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@update` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/showcase/desktop-kit` | `studybuddy.w33.admin.desktop.kit` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@desktopKit` | web, auth, admin |
| `POST` | `admin/control-room/apps/{app}/showcase/desktop/{platform}` | `studybuddy.w33.admin.desktop.upload` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@uploadDesktop` | web, auth, admin |
| `DELETE` | `admin/control-room/apps/{app}/showcase/desktop/{platform}` | `studybuddy.w33.admin.desktop.destroy` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@deleteDesktop` | web, auth, admin |
| `POST` | `admin/control-room/apps/{app}/showcase/screenshots` | `studybuddy.w33.admin.screenshots.upload` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@uploadScreenshots` | web, auth, admin |
| `POST` | `admin/control-room/apps/{app}/showcase/screenshots/{shot}` | `studybuddy.w33.admin.screenshots.update` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@updateScreenshot` | web, auth, admin |
| `DELETE` | `admin/control-room/apps/{app}/showcase/screenshots/{shot}` | `studybuddy.w33.admin.screenshots.destroy` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@deleteScreenshot` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/showcase/screenshots/{shot}/image` | `studybuddy.w33.admin.screenshots.image` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@adminImage` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/apps/{app}/subjects` | `studybuddy.w34.admin.edit` | `App\Http\Controllers\StudyBuddyW34SubjectsController@edit` | web, auth, admin |
| `POST` | `admin/control-room/apps/{app}/subjects` | `studybuddy.w34.admin.update` | `App\Http\Controllers\StudyBuddyW34SubjectsController@update` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/content-studio` | `admin.control-room.content-studio` | `App\Http\Controllers\StudyBuddyAdminContentController@index` | web, auth, admin |
| `PATCH` | `admin/control-room/content-studio/apps/{app}` | `admin.control-room.content.apps.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updateApp` | web, auth, admin |
| `PATCH` | `admin/control-room/content-studio/items/{item}` | `admin.control-room.content.items.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updateItem` | web, auth, admin |
| `PATCH` | `admin/control-room/content-studio/pages/{page}` | `admin.control-room.content.pages.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updatePage` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/final-platform` | `admin.control-room.final-platform` | `App\Http\Controllers\StudyBuddyFinalAdminController@index` | web, auth, admin |
| `PATCH` | `admin/control-room/final-platform/apps/{app}` | `admin.control-room.final.apps.update` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateApp` | web, auth, admin |
| `PATCH` | `admin/control-room/final-platform/checklist/{item}` | `admin.control-room.final.checklist.update` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateChecklist` | web, auth, admin |
| `POST` | `admin/control-room/final-platform/points` | `admin.control-room.final.points.award` | `App\Http\Controllers\StudyBuddyFinalAdminController@quickAward` | web, auth, admin |
| `POST` | `admin/control-room/final-platform/settings` | `admin.control-room.final.settings` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateSettings` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/health` | `admin.control-room.health` | `App\Http\Controllers\StudyBuddyAdminHealthController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/homepage-cms` | `admin.control-room.homepage-cms.index` | `App\Http\Controllers\StudyBuddyAdminHomepageCmsController@index` | web, auth, admin |
| `PATCH` | `admin/control-room/homepage-cms/items/{item}` | `admin.control-room.homepage-cms.items.update` | `App\Http\Controllers\StudyBuddyAdminHomepageCmsController@updateItem` | web, auth, admin |
| `PATCH` | `admin/control-room/homepage-cms/sections/{section}` | `admin.control-room.homepage-cms.sections.update` | `App\Http\Controllers\StudyBuddyAdminHomepageCmsController@updateSection` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/mailing-list` | `admin.control-room.mailing-list.index` | `App\Http\Controllers\StudyBuddyMailingListController@adminIndex` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/mailing-list/export` | `admin.control-room.mailing-list.export` | `App\Http\Controllers\StudyBuddyMailingListController@adminExport` | web, auth, admin |
| `PATCH` | `admin/control-room/mailing-list/{subscriber}` | `admin.control-room.mailing-list.update` | `App\Http\Controllers\StudyBuddyMailingListController@adminUpdate` | web, auth, admin |
| `DELETE` | `admin/control-room/mailing-list/{subscriber}` | `admin.control-room.mailing-list.destroy` | `App\Http\Controllers\StudyBuddyMailingListController@adminDestroy` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/messages` | `admin.control-room.contact-messages.index` | `App\Http\Controllers\StudyBuddyAdminContactMessageController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/messages/{message}` | `admin.control-room.contact-messages.show` | `App\Http\Controllers\StudyBuddyAdminContactMessageController@show` | web, auth, admin |
| `PATCH` | `admin/control-room/messages/{message}` | `admin.control-room.contact-messages.update` | `App\Http\Controllers\StudyBuddyAdminContactMessageController@update` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/pages-legal` | `admin.control-room.pages-legal.index` | `Closure` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/rewards` | `admin.control-room.rewards.index` | `App\Http\Controllers\Admin\StudyBuddyP11FRewardsController@index` | web, auth, admin |
| `PUT` | `admin/control-room/rewards/rules/{rule}` | `admin.control-room.rewards.rules.update` | `App\Http\Controllers\Admin\StudyBuddyP11FRewardsController@updateRule` | web, auth, admin, throttle:12,1 |
| `PUT` | `admin/control-room/rewards/settings` | `admin.control-room.rewards.settings` | `App\Http\Controllers\Admin\StudyBuddyP11FRewardsController@updateSettings` | web, auth, admin, throttle:12,1 |
| `GET\|HEAD` | `admin/control-room/role-tools` | `admin.control-room.role-tools.index` | `App\Http\Controllers\StudyBuddyAdminRoleToolsController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/schools` | `admin.control-room.schools.index` | `App\Http\Controllers\Admin\SchoolOrganizationReviewController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/schools/{organization}/overview` | `admin.control-room.schools.overview` | `App\Http\Controllers\Admin\StudyBuddyP11BSchoolOversightController@show` | web, auth, admin |
| `POST` | `admin/control-room/schools/{organization}/review` | `admin.control-room.schools.review` | `App\Http\Controllers\Admin\SchoolOrganizationReviewController@update` | web, auth, admin, throttle:20,60 |
| `GET\|HEAD` | `admin/control-room/shell` | `admin.control-room.shell` | `App\Http\Controllers\StudyBuddyShellAdminController@index` | web, auth, admin |
| `POST` | `admin/control-room/shell` | `admin.control-room.shell.update` | `App\Http\Controllers\StudyBuddyShellAdminController@update` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/site-settings` | `admin.control-room.site-settings.index` | `App\Http\Controllers\Admin\SiteSettingController@index` | web, auth, admin |
| `POST` | `admin/control-room/site-settings` | `admin.control-room.site-settings.store` | `App\Http\Controllers\Admin\SiteSettingController@store` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/site-settings/create` | `admin.control-room.site-settings.create` | `App\Http\Controllers\Admin\SiteSettingController@create` | web, auth, admin |
| `PUT\|PATCH` | `admin/control-room/site-settings/{site_setting}` | `admin.control-room.site-settings.update` | `App\Http\Controllers\Admin\SiteSettingController@update` | web, auth, admin |
| `DELETE` | `admin/control-room/site-settings/{site_setting}` | `admin.control-room.site-settings.destroy` | `App\Http\Controllers\Admin\SiteSettingController@destroy` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/site-settings/{site_setting}/edit` | `admin.control-room.site-settings.edit` | `App\Http\Controllers\Admin\SiteSettingController@edit` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/team` | `admin.control-room.team.index` | `App\Http\Controllers\StudyBuddyP11TeamController@index` | web, auth, admin |
| `POST` | `admin/control-room/team` | `admin.control-room.team.create` | `App\Http\Controllers\StudyBuddyP11TeamController@create` | web, auth, admin, throttle:5,60 |
| `PUT` | `admin/control-room/team/{user}` | `admin.control-room.team.update` | `App\Http\Controllers\StudyBuddyP11TeamController@update` | web, auth, admin, throttle:15,60 |
| `DELETE` | `admin/control-room/team/{user}` | `admin.control-room.team.revoke` | `App\Http\Controllers\StudyBuddyP11TeamController@revoke` | web, auth, admin, throttle:8,60 |
| `GET\|HEAD` | `admin/control-room/users` | `admin.control-room.users.index` | `App\Http\Controllers\Admin\UserController@index` | web, auth, admin |
| `POST` | `admin/control-room/users` | `admin.control-room.users.store` | `App\Http\Controllers\Admin\UserController@store` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/users/create` | `admin.control-room.users.create` | `App\Http\Controllers\Admin\UserController@create` | web, auth, admin |
| `PUT\|PATCH` | `admin/control-room/users/{user}` | `admin.control-room.users.update` | `App\Http\Controllers\Admin\UserController@update` | web, auth, admin |
| `DELETE` | `admin/control-room/users/{user}` | `admin.control-room.users.destroy` | `App\Http\Controllers\Admin\UserController@destroy` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/users/{user}/edit` | `admin.control-room.users.edit` | `App\Http\Controllers\Admin\UserController@edit` | web, auth, admin |
| `GET\|HEAD` | `admin/control-room/verifications` | `admin.control-room.verifications.index` | `App\Http\Controllers\StudyBuddyVerificationController@adminIndex` | web, auth, admin |
| `PATCH` | `admin/control-room/verifications/{case}` | `admin.control-room.verifications.update` | `App\Http\Controllers\StudyBuddyVerificationController@adminUpdate` | web, auth, admin |
| `GET\|HEAD` | `admin/dashboard` | `admin.dashboard` | `App\Http\Controllers\Admin\DashboardController` | web, admin |
| `GET\|HEAD` | `admin/footer-items` | `admin.footer-items.index` | `App\Http\Controllers\Admin\FooterItemController@index` | web, admin |
| `POST` | `admin/footer-items` | `admin.footer-items.store` | `App\Http\Controllers\Admin\FooterItemController@store` | web, admin |
| `GET\|HEAD` | `admin/footer-items/create` | `admin.footer-items.create` | `App\Http\Controllers\Admin\FooterItemController@create` | web, admin |
| `PUT\|PATCH` | `admin/footer-items/{footer_item}` | `admin.footer-items.update` | `App\Http\Controllers\Admin\FooterItemController@update` | web, admin |
| `DELETE` | `admin/footer-items/{footer_item}` | `admin.footer-items.destroy` | `App\Http\Controllers\Admin\FooterItemController@destroy` | web, admin |
| `GET\|HEAD` | `admin/footer-items/{footer_item}/edit` | `admin.footer-items.edit` | `App\Http\Controllers\Admin\FooterItemController@edit` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections` | `admin.homepage-sections.index` | `App\Http\Controllers\Admin\HomepageSectionController@index` | web, admin |
| `POST` | `admin/homepage-sections` | `admin.homepage-sections.store` | `App\Http\Controllers\Admin\HomepageSectionController@store` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections/create` | `admin.homepage-sections.create` | `App\Http\Controllers\Admin\HomepageSectionController@create` | web, admin |
| `PUT\|PATCH` | `admin/homepage-sections/{homepage_section}` | `admin.homepage-sections.update` | `App\Http\Controllers\Admin\HomepageSectionController@update` | web, admin |
| `DELETE` | `admin/homepage-sections/{homepage_section}` | `admin.homepage-sections.destroy` | `App\Http\Controllers\Admin\HomepageSectionController@destroy` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections/{homepage_section}/edit` | `admin.homepage-sections.edit` | `App\Http\Controllers\Admin\HomepageSectionController@edit` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections/{homepage_section}/items` | `admin.homepage-sections.items.index` | `App\Http\Controllers\Admin\HomepageSectionItemController@index` | web, admin |
| `POST` | `admin/homepage-sections/{homepage_section}/items` | `admin.homepage-sections.items.store` | `App\Http\Controllers\Admin\HomepageSectionItemController@store` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections/{homepage_section}/items/create` | `admin.homepage-sections.items.create` | `App\Http\Controllers\Admin\HomepageSectionItemController@create` | web, admin |
| `PUT\|PATCH` | `admin/homepage-sections/{homepage_section}/items/{item}` | `admin.homepage-sections.items.update` | `App\Http\Controllers\Admin\HomepageSectionItemController@update` | web, admin |
| `DELETE` | `admin/homepage-sections/{homepage_section}/items/{item}` | `admin.homepage-sections.items.destroy` | `App\Http\Controllers\Admin\HomepageSectionItemController@destroy` | web, admin |
| `GET\|HEAD` | `admin/homepage-sections/{homepage_section}/items/{item}/edit` | `admin.homepage-sections.items.edit` | `App\Http\Controllers\Admin\HomepageSectionItemController@edit` | web, admin |
| `GET\|HEAD` | `admin/login` | `admin.login` | `App\Http\Controllers\Admin\LoginController@show` | web |
| `POST` | `admin/login` | `admin.login.store` | `App\Http\Controllers\Admin\LoginController@store` | web, throttle:12,5 |
| `POST` | `admin/logout` | `admin.logout` | `App\Http\Controllers\Admin\LoginController@destroy` | web |
| `GET\|HEAD` | `admin/media-assets` | `admin.media-assets.index` | `App\Http\Controllers\Admin\MediaAssetController@index` | web, admin |
| `POST` | `admin/media-assets` | `admin.media-assets.store` | `App\Http\Controllers\Admin\MediaAssetController@store` | web, admin |
| `GET\|HEAD` | `admin/media-assets/create` | `admin.media-assets.create` | `App\Http\Controllers\Admin\MediaAssetController@create` | web, admin |
| `PUT\|PATCH` | `admin/media-assets/{media_asset}` | `admin.media-assets.update` | `App\Http\Controllers\Admin\MediaAssetController@update` | web, admin |
| `DELETE` | `admin/media-assets/{media_asset}` | `admin.media-assets.destroy` | `App\Http\Controllers\Admin\MediaAssetController@destroy` | web, admin |
| `GET\|HEAD` | `admin/media-assets/{media_asset}/edit` | `admin.media-assets.edit` | `App\Http\Controllers\Admin\MediaAssetController@edit` | web, admin |
| `GET\|HEAD` | `admin/navigation-items` | `admin.navigation-items.index` | `App\Http\Controllers\Admin\NavigationItemController@index` | web, admin |
| `POST` | `admin/navigation-items` | `admin.navigation-items.store` | `App\Http\Controllers\Admin\NavigationItemController@store` | web, admin |
| `GET\|HEAD` | `admin/navigation-items/create` | `admin.navigation-items.create` | `App\Http\Controllers\Admin\NavigationItemController@create` | web, admin |
| `PUT\|PATCH` | `admin/navigation-items/{navigation_item}` | `admin.navigation-items.update` | `App\Http\Controllers\Admin\NavigationItemController@update` | web, admin |
| `DELETE` | `admin/navigation-items/{navigation_item}` | `admin.navigation-items.destroy` | `App\Http\Controllers\Admin\NavigationItemController@destroy` | web, admin |
| `GET\|HEAD` | `admin/navigation-items/{navigation_item}/edit` | `admin.navigation-items.edit` | `App\Http\Controllers\Admin\NavigationItemController@edit` | web, admin |
| `GET\|HEAD` | `admin/pages` | `admin.pages.index` | `App\Http\Controllers\Admin\PageController@index` | web, admin |
| `POST` | `admin/pages` | `admin.pages.store` | `App\Http\Controllers\Admin\PageController@store` | web, admin |
| `GET\|HEAD` | `admin/pages/create` | `admin.pages.create` | `App\Http\Controllers\Admin\PageController@create` | web, admin |
| `PUT\|PATCH` | `admin/pages/{page}` | `admin.pages.update` | `App\Http\Controllers\Admin\PageController@update` | web, admin |
| `DELETE` | `admin/pages/{page}` | `admin.pages.destroy` | `App\Http\Controllers\Admin\PageController@destroy` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/edit` | `admin.pages.edit` | `App\Http\Controllers\Admin\PageController@edit` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections` | `admin.pages.sections.index` | `App\Http\Controllers\Admin\PageSectionController@index` | web, admin |
| `POST` | `admin/pages/{page}/sections` | `admin.pages.sections.store` | `App\Http\Controllers\Admin\PageSectionController@store` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections/create` | `admin.pages.sections.create` | `App\Http\Controllers\Admin\PageSectionController@create` | web, admin |
| `PUT\|PATCH` | `admin/pages/{page}/sections/{section}` | `admin.pages.sections.update` | `App\Http\Controllers\Admin\PageSectionController@update` | web, admin |
| `DELETE` | `admin/pages/{page}/sections/{section}` | `admin.pages.sections.destroy` | `App\Http\Controllers\Admin\PageSectionController@destroy` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections/{section}/edit` | `admin.pages.sections.edit` | `App\Http\Controllers\Admin\PageSectionController@edit` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections/{section}/items` | `admin.pages.sections.items.index` | `App\Http\Controllers\Admin\PageSectionItemController@index` | web, admin |
| `POST` | `admin/pages/{page}/sections/{section}/items` | `admin.pages.sections.items.store` | `App\Http\Controllers\Admin\PageSectionItemController@store` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections/{section}/items/create` | `admin.pages.sections.items.create` | `App\Http\Controllers\Admin\PageSectionItemController@create` | web, admin |
| `PUT\|PATCH` | `admin/pages/{page}/sections/{section}/items/{item}` | `admin.pages.sections.items.update` | `App\Http\Controllers\Admin\PageSectionItemController@update` | web, admin |
| `DELETE` | `admin/pages/{page}/sections/{section}/items/{item}` | `admin.pages.sections.items.destroy` | `App\Http\Controllers\Admin\PageSectionItemController@destroy` | web, admin |
| `GET\|HEAD` | `admin/pages/{page}/sections/{section}/items/{item}/edit` | `admin.pages.sections.items.edit` | `App\Http\Controllers\Admin\PageSectionItemController@edit` | web, admin |
| `GET\|HEAD` | `admin/site-settings` | `admin.site-settings.index` | `App\Http\Controllers\Admin\SiteSettingController@index` | web, admin |
| `POST` | `admin/site-settings` | `admin.site-settings.store` | `App\Http\Controllers\Admin\SiteSettingController@store` | web, admin |
| `GET\|HEAD` | `admin/site-settings/create` | `admin.site-settings.create` | `App\Http\Controllers\Admin\SiteSettingController@create` | web, admin |
| `PUT\|PATCH` | `admin/site-settings/{site_setting}` | `admin.site-settings.update` | `App\Http\Controllers\Admin\SiteSettingController@update` | web, admin |
| `DELETE` | `admin/site-settings/{site_setting}` | `admin.site-settings.destroy` | `App\Http\Controllers\Admin\SiteSettingController@destroy` | web, admin |
| `GET\|HEAD` | `admin/site-settings/{site_setting}/edit` | `admin.site-settings.edit` | `App\Http\Controllers\Admin\SiteSettingController@edit` | web, admin |
| `PATCH` | `admin/studybuddy/apps/{app}` | `studybuddy.admin.content.apps.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updateApp` | web, auth, admin |
| `PATCH` | `admin/studybuddy/content-items/{item}` | `studybuddy.admin.content.items.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updateItem` | web, auth, admin |
| `PATCH` | `admin/studybuddy/content-pages/{page}` | `studybuddy.admin.content.pages.update` | `App\Http\Controllers\StudyBuddyAdminContentController@updatePage` | web, auth, admin |
| `GET\|HEAD` | `admin/studybuddy/content-studio` | `studybuddy.admin.content.index` | `App\Http\Controllers\StudyBuddyAdminContentController@index` | web, auth, admin |
| `GET\|HEAD` | `admin/studybuddy/final-platform` | `studybuddy.admin.final.index` | `App\Http\Controllers\StudyBuddyFinalAdminController@index` | web, auth, admin |
| `PATCH` | `admin/studybuddy/final-platform/apps/{app}` | `studybuddy.admin.final.apps.update` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateApp` | web, auth, admin |
| `PATCH` | `admin/studybuddy/final-platform/checklist/{item}` | `studybuddy.admin.final.checklist.update` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateChecklist` | web, auth, admin |
| `POST` | `admin/studybuddy/final-platform/points` | `studybuddy.admin.final.points.award` | `App\Http\Controllers\StudyBuddyFinalAdminController@quickAward` | web, auth, admin |
| `POST` | `admin/studybuddy/final-platform/settings` | `studybuddy.admin.final.settings` | `App\Http\Controllers\StudyBuddyFinalAdminController@updateSettings` | web, auth, admin |
| `GET\|HEAD` | `admin/studybuddy/shell` | `studybuddy.admin.shell.index` | `App\Http\Controllers\StudyBuddyShellAdminController@index` | web, auth, admin |
| `POST` | `admin/studybuddy/shell` | `studybuddy.admin.shell.update` | `App\Http\Controllers\StudyBuddyShellAdminController@update` | web, auth, admin |
| `GET\|HEAD` | `admin/studybuddy/verifications` | `studybuddy.admin.verifications.index` | `App\Http\Controllers\StudyBuddyVerificationController@adminIndex` | web, auth, admin |
| `PATCH` | `admin/studybuddy/verifications/{case}` | `studybuddy.admin.verifications.update` | `App\Http\Controllers\StudyBuddyVerificationController@adminUpdate` | web, auth, admin |
| `GET\|HEAD` | `admin/two-factor` | `admin.two-factor.show` | `App\Http\Controllers\Admin\LoginController@showTwoFactor` | web |
| `POST` | `admin/two-factor` | `admin.two-factor.verify` | `App\Http\Controllers\Admin\LoginController@verifyTwoFactor` | web, throttle:8,10 |
| `GET\|HEAD` | `admin/users` | `admin.users.index` | `App\Http\Controllers\Admin\UserController@index` | web, admin |
| `POST` | `admin/users` | `admin.users.store` | `App\Http\Controllers\Admin\UserController@store` | web, admin |
| `GET\|HEAD` | `admin/users/create` | `admin.users.create` | `App\Http\Controllers\Admin\UserController@create` | web, admin |
| `PUT\|PATCH` | `admin/users/{user}` | `admin.users.update` | `App\Http\Controllers\Admin\UserController@update` | web, admin |
| `DELETE` | `admin/users/{user}` | `admin.users.destroy` | `App\Http\Controllers\Admin\UserController@destroy` | web, admin |
| `GET\|HEAD` | `admin/users/{user}/edit` | `admin.users.edit` | `App\Http\Controllers\Admin\UserController@edit` | web, admin |
| `GET\|HEAD` | `app-builds/{slug}/{path?}` | `studybuddy.web-app.asset` | `App\Http\Controllers\StudyBuddyWebAppAssetController` | web |
| `GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS` | `app-ecosystem` | `studybuddy.experience.app-ecosystem` | `Illuminate\Routing\RedirectController` | web |
| `GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS` | `app-launchpad` | `studybuddy.final.app-launchpad` | `Illuminate\Routing\RedirectController` | web |
| `GET\|HEAD` | `apps` | `studybuddy.apps` | `App\Http\Controllers\StudyBuddyFinalPlatformController@apps` | web |
| `GET\|HEAD` | `apps-catalog` | `pages.apps` | `App\Http\Controllers\StudyBuddyFinalPlatformController@apps` | web |
| `GET\|HEAD` | `apps/{slug}` | `studybuddy.apps.show` | `App\Http\Controllers\StudyBuddyFinalPlatformController@appDetail` | web |
| `GET\|HEAD` | `apps/{slug}/desktop/{platform}` | `studybuddy.w33.desktop.download` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@downloadDesktop` | web |
| `GET\|HEAD` | `apps/{slug}/install-desktop` | `studybuddy.w33.desktop.install` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@installDesktop` | web |
| `GET\|HEAD` | `apps/{slug}/showcase/screenshots/{shot}` | `studybuddy.w33.screenshot` | `App\Http\Controllers\StudyBuddyW33ShowcaseController@publicImage` | web |
| `GET\|HEAD` | `command-center` | `studybuddy.command-center` | `App\Http\Controllers\StudyBuddyCommandCenterController` | web, auth |
| `GET\|HEAD` | `community` | `studybuddy.community` | `App\Http\Controllers\StudyBuddyProfileController@community` | web |
| `GET\|HEAD` | `community-guidelines` | `studybuddy.info.community-guidelines` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `POST` | `connections/parent/request` | `studybuddy.connections.parent.request` | `App\Http\Controllers\AccountConnectionController@requestParentConnection` | web, auth |
| `POST` | `connections/teacher/request` | `studybuddy.connections.teacher.request` | `App\Http\Controllers\AccountConnectionController@requestTeacherConnection` | web, auth |
| `PATCH` | `connections/{connection}/approve` | `studybuddy.connections.approve` | `App\Http\Controllers\AccountConnectionController@approve` | web, auth |
| `PATCH` | `connections/{connection}/reject` | `studybuddy.connections.reject` | `App\Http\Controllers\AccountConnectionController@reject` | web, auth |
| `PATCH` | `connections/{connection}/revoke` | `studybuddy.connections.revoke` | `App\Http\Controllers\AccountConnectionController@revoke` | web, auth |
| `GET\|HEAD` | `contact` | `studybuddy.contact.show` | `App\Http\Controllers\StudyBuddyContactController@show` | web |
| `POST` | `contact` | `studybuddy.contact.store` | `App\Http\Controllers\StudyBuddyContactController@store` | web |
| `GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS` | `contact-us` | `pages.contact-us` | `Illuminate\Routing\RedirectController` | web |
| `GET\|HEAD` | `cookies` | `studybuddy.info.cookies` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `copyright` | `studybuddy.info.copyright` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `dashboard` | `dashboard` | `App\Http\Controllers\DashboardController@index` | web, auth |
| `GET\|HEAD` | `dashboard/command-center` | `studybuddy.dashboard.command-center` | `App\Http\Controllers\StudyBuddyCommandCenterController` | web, auth |
| `POST` | `dashboard/learner/connect-code` | `studybuddy.learner.connect-code.regenerate` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@regenerateConnectCode` | web, auth |
| `POST` | `dashboard/parent/children` | `studybuddy.parent.children.store` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@addChild` | web, auth |
| `DELETE` | `dashboard/parent/children/{member}` | `studybuddy.parent.children.destroy` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@removeChild` | web, auth |
| `PUT` | `dashboard/password` | `dashboard.password.update` | `App\Http\Controllers\DashboardController@updatePassword` | web, auth |
| `PUT` | `dashboard/profile` | `dashboard.profile.update` | `App\Http\Controllers\DashboardController@updateProfile` | web, auth |
| `POST` | `dashboard/tasks/{recipient}/complete` | `studybuddy.tasks.complete` | `App\Http\Controllers\StudyBuddyAssignedWorkController@complete` | web, auth, throttle:20,1 |
| `POST` | `dashboard/teacher/assignments` | `studybuddy.teacher.assignments.store` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@createAssignment` | web, auth |
| `POST` | `dashboard/teacher/classes` | `studybuddy.teacher.classes.store` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@createClass` | web, auth |
| `POST` | `dashboard/teacher/classes/{group}/students` | `studybuddy.teacher.students.store` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@addStudent` | web, auth |
| `POST` | `dashboard/teacher/organization` | `studybuddy.teacher.organization.update` | `App\Http\Controllers\StudyBuddyRoleDashboardActionController@updateTeacherOrganization` | web, auth |
| `POST` | `dashboard/theme` | `studybuddy.dashboard.theme.update` | `App\Http\Controllers\DashboardThemeController@update` | web, auth |
| `GET\|HEAD` | `data-deletion` | `studybuddy.info.data-deletion` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `disclaimer` | `studybuddy.info.disclaimer` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `POST` | `email/verification-notification` | `verification.send` | `App\Http\Controllers\EmailVerificationController@send` | web, auth, throttle:6,60 |
| `GET\|HEAD` | `email/verify` | `verification.notice` | `App\Http\Controllers\EmailVerificationController@notice` | web, auth |
| `GET\|HEAD` | `email/verify/{id}/{hash}` | `verification.verify` | `App\Http\Controllers\EmailVerificationController@verify` | web, auth, signed, throttle:6,1 |
| `GET\|HEAD` | `family/learners` | `studybuddy.family.learners` | `App\Http\Controllers\FamilyLearnerController@index` | web, auth |
| `POST` | `family/learners` | `studybuddy.family.learners.store` | `App\Http\Controllers\FamilyLearnerController@store` | web, auth, throttle:12,1 |
| `PATCH` | `family/learners/{member}` | `studybuddy.family.learners.update` | `App\Http\Controllers\FamilyLearnerController@update` | web, auth |
| `DELETE` | `family/learners/{member}` | `studybuddy.family.learners.destroy` | `App\Http\Controllers\FamilyLearnerController@destroy` | web, auth |
| `POST` | `family/learners/{member}/link` | `studybuddy.family.learners.link` | `App\Http\Controllers\FamilyLearnerController@link` | web, auth, throttle:6,1 |
| `POST` | `family/learners/{member}/moments` | `studybuddy.family.learners.moment` | `App\Http\Controllers\FamilyLearnerController@moment` | web, auth, throttle:12,1 |
| `GET\|HEAD` | `for-parents` | `pages.for-parents` | `App\Http\Controllers\PageController@show` | web |
| `GET\|HEAD` | `for-schools` | `schools.info` | `App\Http\Controllers\SchoolOrganizationController@info` | web |
| `GET\|HEAD` | `for-teachers` | `pages.for-teachers` | `App\Http\Controllers\PageController@show` | web |
| `GET\|HEAD` | `forgot-password` | `password.request` | `App\Http\Controllers\PasswordRecoveryController@forgot` | web, guest |
| `POST` | `forgot-password` | `password.email` | `App\Http\Controllers\PasswordRecoveryController@send` | web, guest, throttle:3,10 |
| `GET\|HEAD` | `getting-started` | `studybuddy.onboarding.show` | `App\Http\Controllers\StudyBuddyOnboardingController@show` | web, auth |
| `POST` | `getting-started` | `studybuddy.onboarding.save` | `App\Http\Controllers\StudyBuddyOnboardingController@save` | web, auth, throttle:25,1 |
| `GET\|HEAD` | `goodbye` | `logout.goodbye` | `App\Http\Controllers\StudyBuddyLogoutController@goodbye` | web |
| `GET\|HEAD` | `launch-readiness` | `studybuddy.final.launch-readiness` | `App\Http\Controllers\StudyBuddyFinalPlatformController@launchReadiness` | web, auth, admin |
| `GET\|HEAD` | `learning-hub` | `studybuddy.experience.learning-hub` | `App\Http\Controllers\StudyBuddyExperienceController@learningHub` | web |
| `GET\|HEAD` | `learning-paths` | `studybuddy.experience.learning-paths` | `App\Http\Controllers\StudyBuddyExperienceController@learningPaths` | web |
| `GET\|HEAD` | `learning/activity` | `studybuddy.learning.activity` | `App\Http\Controllers\StudyBuddyLearningActivityController@index` | web, auth |
| `POST` | `learning/activity/start` | `studybuddy.learning.start` | `App\Http\Controllers\StudyBuddyLearningActivityController@start` | web, auth, throttle:12,1 |
| `DELETE` | `learning/activity/{session}` | `studybuddy.learning.destroy` | `App\Http\Controllers\StudyBuddyLearningActivityController@destroy` | web, auth |
| `POST` | `learning/activity/{session}/finish` | `studybuddy.learning.finish` | `App\Http\Controllers\StudyBuddyLearningActivityController@finish` | web, auth, throttle:20,1 |
| `POST` | `learning/activity/{session}/leave` | `studybuddy.learning.leave` | `App\Http\Controllers\StudyBuddyLearningActivityController@leave` | web, auth |
| `GET\|HEAD` | `learning/connected/insights` | `studybuddy.connected.insights` | `App\Http\Controllers\StudyBuddyConnectedProgressController@insights` | web, auth |
| `POST` | `learning/connected/start` | `studybuddy.connected.start` | `App\Http\Controllers\StudyBuddyConnectedProgressController@start` | web, auth, throttle:12,1 |
| `POST` | `learning/connected/{session}/finish` | `studybuddy.connected.finish` | `App\Http\Controllers\StudyBuddyConnectedProgressController@finish` | web, auth, throttle:12,1 |
| `POST` | `learning/connected/{session}/heartbeat` | `studybuddy.connected.heartbeat` | `App\Http\Controllers\StudyBuddyConnectedProgressController@heartbeat` | web, auth, throttle:12,1 |
| `POST` | `learning/connected/{session}/leave` | `studybuddy.connected.leave` | `App\Http\Controllers\StudyBuddyConnectedProgressController@leave` | web, auth, throttle:12,1 |
| `GET\|HEAD` | `learning/math-sprint` | `studybuddy.w32.index` | `App\Http\Controllers\StudyBuddyMathSprintController@index` | web, auth |
| `GET\|HEAD` | `learning/math-sprint/results/{run}` | `studybuddy.w32.result` | `App\Http\Controllers\StudyBuddyMathSprintController@result` | web, auth |
| `POST` | `learning/math-sprint/start` | `studybuddy.w32.start` | `App\Http\Controllers\StudyBuddyMathSprintController@start` | web, auth, throttle:12,1 |
| `POST` | `learning/math-sprint/{run}/submit` | `studybuddy.w32.submit` | `App\Http\Controllers\StudyBuddyMathSprintController@submit` | web, auth, throttle:20,1 |
| `GET\|HEAD` | `login` | `login` | `App\Http\Controllers\UserAccessController@showLogin` | web, guest |
| `POST` | `login` | `login.store` | `App\Http\Controllers\UserAccessController@login` | web, guest |
| `GET\|HEAD` | `logout` | `logout.confirm` | `App\Http\Controllers\StudyBuddyLogoutController@confirm` | web |
| `POST` | `logout` | `logout` | `App\Http\Controllers\StudyBuddyLogoutController@destroy` | web |
| `GET\|HEAD` | `my-day` | `studybuddy.p09.pulse` | `App\Http\Controllers\StudyBuddyDailyPulseController@show` | web, auth |
| `PUT` | `my-day/notifications/preferences` | `studybuddy.p09.preferences.update` | `App\Http\Controllers\StudyBuddyDailyPulseController@preferences` | web, auth |
| `POST` | `my-day/notifications/read` | `studybuddy.p09.notifications.read` | `App\Http\Controllers\StudyBuddyDailyPulseController@markRead` | web, auth |
| `POST` | `my-day/plans` | `studybuddy.p09.plans.store` | `App\Http\Controllers\StudyBuddyDailyPulseController@addPlan` | web, auth |
| `DELETE` | `my-day/plans/{id}` | `studybuddy.p09.plans.destroy` | `App\Http\Controllers\StudyBuddyDailyPulseController@deletePlan` | web, auth |
| `GET\|HEAD` | `my-quest` | `studybuddy.quests.index` | `App\Http\Controllers\SavedQuestController@index` | web, auth |
| `POST` | `my-quest` | `studybuddy.quests.store` | `App\Http\Controllers\SavedQuestController@store` | web, auth |
| `PATCH` | `my-quest/{savedQuest}` | `studybuddy.quests.update` | `App\Http\Controllers\SavedQuestController@update` | web, auth |
| `DELETE` | `my-quest/{savedQuest}` | `studybuddy.quests.destroy` | `App\Http\Controllers\SavedQuestController@destroy` | web, auth |
| `GET\|HEAD` | `parents-center` | `studybuddy.experience.parents-center` | `App\Http\Controllers\StudyBuddyExperienceController@parentsCenter` | web |
| `GET\|HEAD` | `platform-roadmap` | `studybuddy.final.roadmap` | `App\Http\Controllers\StudyBuddyFinalPlatformController@platformRoadmap` | web, auth, admin |
| `GET\|HEAD` | `play/{slug}` | `studybuddy.final.web-play` | `App\Http\Controllers\StudyBuddyFinalPlatformController@webPlay` | web |
| `GET\|HEAD` | `points-wallet` | `studybuddy.final.points-wallet` | `App\Http\Controllers\StudyBuddyFinalPlatformController@pointsWallet` | web, auth |
| `GET\|HEAD` | `privacy-policy` | `studybuddy.info.privacy-policy` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `profile` | `profile` | `App\Http\Controllers\StudyBuddyProfileController@edit` | web, auth |
| `PATCH` | `profile` | `profile.update` | `App\Http\Controllers\StudyBuddyProfileController@update` | web, auth |
| `GET\|HEAD` | `register` | `register` | `App\Http\Controllers\UserAccessController@showRegister` | web, guest |
| `POST` | `register` | `register.store` | `App\Http\Controllers\UserAccessController@register` | web, guest |
| `POST` | `reset-password` | `password.update` | `App\Http\Controllers\PasswordRecoveryController@reset` | web, guest, throttle:5,10 |
| `GET\|HEAD` | `reset-password/{token}` | `password.reset` | `App\Http\Controllers\PasswordRecoveryController@resetForm` | web, guest |
| `GET\|HEAD` | `resources` | `studybuddy.p10.index` | `App\Http\Controllers\StudyBuddyResourceStudioController@index` | web, auth, throttle:60,1 |
| `POST` | `resources/files` | `studybuddy.p10.files.upload` | `App\Http\Controllers\StudyBuddyResourceStudioController@upload` | web, auth, throttle:60,1 |
| `DELETE` | `resources/files/{id}` | `studybuddy.p10.files.delete` | `App\Http\Controllers\StudyBuddyResourceStudioController@deleteFile` | web, auth, throttle:60,1 |
| `GET\|HEAD` | `resources/files/{id}/download` | `studybuddy.p10.files.download` | `App\Http\Controllers\StudyBuddyResourceStudioController@download` | web, auth, throttle:60,1 |
| `GET\|HEAD` | `resources/files/{id}/preview` | `studybuddy.p10.files.preview` | `App\Http\Controllers\StudyBuddyResourceStudioController@preview` | web, auth, throttle:60,1 |
| `POST` | `resources/notes` | `studybuddy.p10.notes.create` | `App\Http\Controllers\StudyBuddyResourceStudioController@createNote` | web, auth, throttle:60,1 |
| `PUT` | `resources/notes/{id}` | `studybuddy.p10.notes.update` | `App\Http\Controllers\StudyBuddyResourceStudioController@updateNote` | web, auth, throttle:60,1 |
| `DELETE` | `resources/notes/{id}` | `studybuddy.p10.notes.delete` | `App\Http\Controllers\StudyBuddyResourceStudioController@deleteNote` | web, auth, throttle:60,1 |
| `GET\|HEAD` | `rewards` | `studybuddy.experience.rewards` | `App\Http\Controllers\StudyBuddyExperienceController@rewards` | web |
| `GET\|HEAD` | `roles` | `studybuddy.roles` | `App\Http\Controllers\StudyBuddyInfoPageController@roles` | web |
| `GET\|HEAD` | `safety-support` | `studybuddy.experience.safety-support` | `App\Http\Controllers\StudyBuddyExperienceController@safetySupport` | web |
| `GET\|HEAD` | `schools/apply` | `schools.apply` | `App\Http\Controllers\SchoolOrganizationController@apply` | web, auth |
| `POST` | `schools/apply` | `schools.store` | `App\Http\Controllers\SchoolOrganizationController@store` | web, auth, throttle:2,60 |
| `GET\|HEAD` | `schools/invitations/{token}` | `schools.invitations.show` | `App\Http\Controllers\SchoolOrganizationController@invitation` | web, auth |
| `POST` | `schools/invitations/{token}/accept` | `schools.invitations.accept` | `App\Http\Controllers\SchoolOrganizationController@accept` | web, auth, throttle:10,60 |
| `GET\|HEAD` | `schools/{organization}/board` | `schools.board` | `App\Http\Controllers\StudyBuddySchoolBoardController@index` | web, auth |
| `POST` | `schools/{organization}/board/tasks` | `schools.board.tasks.store` | `App\Http\Controllers\StudyBuddySchoolBoardController@store` | web, auth, throttle:20,1 |
| `PUT` | `schools/{organization}/board/tasks/{task}` | `schools.board.tasks.update` | `App\Http\Controllers\StudyBuddySchoolBoardController@update` | web, auth, throttle:40,1 |
| `DELETE` | `schools/{organization}/board/tasks/{task}` | `schools.board.tasks.archive` | `App\Http\Controllers\StudyBuddySchoolBoardController@archive` | web, auth, throttle:15,1 |
| `GET\|HEAD` | `schools/{organization}/connect` | `schools.connect.index` | `App\Http\Controllers\StudyBuddySchoolMessagingController@index` | web, auth |
| `POST` | `schools/{organization}/connect/channels` | `schools.connect.channels.create` | `App\Http\Controllers\StudyBuddySchoolMessagingController@createChannel` | web, auth, throttle:6,60 |
| `POST` | `schools/{organization}/connect/directs` | `schools.connect.directs.start` | `App\Http\Controllers\StudyBuddySchoolMessagingController@startDirect` | web, auth, throttle:12,60 |
| `GET\|HEAD` | `schools/{organization}/connect/reports` | `schools.connect.reports` | `App\Http\Controllers\StudyBuddySchoolMessagingController@reports` | web, auth |
| `PUT` | `schools/{organization}/connect/reports/{report}` | `schools.connect.reports.review` | `App\Http\Controllers\StudyBuddySchoolMessagingController@reviewReport` | web, auth, throttle:20,1 |
| `GET\|HEAD` | `schools/{organization}/connect/{conversation}` | `schools.connect.show` | `App\Http\Controllers\StudyBuddySchoolMessagingController@show` | web, auth |
| `GET\|HEAD` | `schools/{organization}/connect/{conversation}/attachments/{message}` | `schools.connect.attachments.download` | `App\Http\Controllers\StudyBuddySchoolMessagingController@download` | web, auth |
| `POST` | `schools/{organization}/connect/{conversation}/messages` | `schools.connect.messages.send` | `App\Http\Controllers\StudyBuddySchoolMessagingController@send` | web, auth, throttle:20,1 |
| `POST` | `schools/{organization}/connect/{conversation}/messages/{message}/reports` | `schools.connect.messages.report` | `App\Http\Controllers\StudyBuddySchoolMessagingController@report` | web, auth, throttle:8,60 |
| `POST` | `schools/{organization}/connect/{conversation}/read` | `schools.connect.read` | `App\Http\Controllers\StudyBuddySchoolMessagingController@markRead` | web, auth, throttle:40,1 |
| `POST` | `schools/{organization}/invitations` | `schools.invitations.store` | `App\Http\Controllers\SchoolOrganizationController@invite` | web, auth, throttle:6,60 |
| `DELETE` | `schools/{organization}/invitations/{invitation}` | `schools.invitations.revoke` | `App\Http\Controllers\SchoolOrganizationController@revokeInvitation` | web, auth |
| `DELETE` | `schools/{organization}/members/{member}` | `schools.members.revoke` | `App\Http\Controllers\SchoolOrganizationController@removeMember` | web, auth |
| `PUT` | `schools/{organization}/members/{member}/role` | `schools.members.role` | `App\Http\Controllers\SchoolOrganizationController@changeStaffRole` | web, auth, throttle:10,1 |
| `GET\|HEAD` | `schools/{organization}/safety` | `schools.safety.index` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@index` | web, auth |
| `GET\|HEAD` | `schools/{organization}/safety/export/notices` | `schools.safety.export.notices` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@exportNotices` | web, auth, throttle:3,1 |
| `POST` | `schools/{organization}/safety/notices` | `schools.safety.notices.post` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@postNotice` | web, auth, throttle:8,1 |
| `POST` | `schools/{organization}/safety/notices/{notice}/archive` | `schools.safety.notices.archive` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@archiveNotice` | web, auth, throttle:10,1 |
| `POST` | `schools/{organization}/safety/notices/{notice}/read` | `schools.safety.notices.read` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@markRead` | web, auth, throttle:30,1 |
| `POST` | `schools/{organization}/safety/preferences` | `schools.safety.preferences` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@updatePreference` | web, auth, throttle:10,1 |
| `POST` | `schools/{organization}/safety/reports` | `schools.safety.reports.submit` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@reportIssue` | web, auth, throttle:5,1 |
| `PUT` | `schools/{organization}/safety/reports/{report}` | `schools.safety.reports.review` | `App\Http\Controllers\StudyBuddySchoolSafetyCenterController@reviewIssue` | web, auth, throttle:10,1 |
| `GET\|HEAD` | `schools/{organization}/workspace` | `schools.workspace` | `App\Http\Controllers\SchoolOrganizationController@workspace` | web, auth |
| `GET\|HEAD` | `search` | `studybuddy.search` | `App\Http\Controllers\StudyBuddySearchController@index` | web |
| `GET\|HEAD` | `search/suggest` | `studybuddy.search.suggest` | `App\Http\Controllers\StudyBuddySearchController@suggest` | web |
| `GET\|HEAD` | `study-space` | `studybuddy.study-space` | `Closure` | web, auth |
| `POST` | `studybuddy/app-session/complete` | `studybuddy.final.session.complete` | `App\Http\Controllers\StudyBuddyFinalPlatformController@completeSession` | web, auth |
| `GET\|HEAD` | `subjects` | `studybuddy.w34.subjects.index` | `App\Http\Controllers\StudyBuddyW34SubjectsController@index` | web |
| `GET\|HEAD` | `subjects/{slug}` | `studybuddy.w34.subjects.show` | `App\Http\Controllers\StudyBuddyW34SubjectsController@show` | web |
| `GET\|HEAD` | `support` | `pages.support` | `App\Http\Controllers\PageController@show` | web |
| `GET\|HEAD` | `teacher-studio` | `studybuddy.experience.teacher-studio` | `App\Http\Controllers\StudyBuddyExperienceController@teacherStudio` | web |
| `GET\|HEAD` | `terms` | `studybuddy.info.terms` | `App\Http\Controllers\StudyBuddyInfoPageController@show` | web |
| `GET\|HEAD` | `u/{user}` | `studybuddy.profile.public` | `App\Http\Controllers\StudyBuddyProfileController@show` | web |
| `GET\|HEAD` | `up` | `—` | `Closure` |  |
| `POST` | `updates/subscribe` | `newsletter.subscribe` | `App\Http\Controllers\StudyBuddyMailingListController@subscribe` | web, throttle:10,1 |
| `GET\|HEAD` | `verification-center` | `studybuddy.verification.center` | `App\Http\Controllers\StudyBuddyVerificationController@show` | web, auth |
| `POST` | `verification-center` | `studybuddy.verification.submit` | `App\Http\Controllers\StudyBuddyVerificationController@submit` | web, auth, throttle:4,10 |
| `GET\|HEAD\|POST\|PUT\|PATCH\|DELETE\|OPTIONS` | `web-play` | `studybuddy.final.web-play.legacy-index` | `Illuminate\Routing\RedirectController` | web |
| `POST` | `web-play/{app?}/complete` | `studybuddy.final.session.complete.legacy` | `App\Http\Controllers\StudyBuddyFinalPlatformController@completeSession` | web, auth |
| `GET\|HEAD` | `web-play/{slug}` | `studybuddy.final.web-play.legacy` | `App\Http\Controllers\StudyBuddyFinalPlatformController@webPlay` | web |
