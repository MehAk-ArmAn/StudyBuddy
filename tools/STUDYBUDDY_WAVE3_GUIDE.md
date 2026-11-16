# StudyBuddy W3 — visual experiences and micro-interactions

**Use with the existing StudyBuddy Laravel 11 + Blade + React/Vite bridge (after W2 Storybook Hero).** This is a frontend-only change. No changes to PHP routes, controllers, models, auth, CMS rows, school memberships, permissions, billing, or DB schema.

## Visible pages

- **Home `/`:** old test tube section removed. Featured app carousel now uses the actual free React Bits **FlexCarousel**, with real images and destinations from `$sb8CarouselItems`. Original app cards remain below as semantic, no-JavaScript links. Roles receive a new interactive visual carousel with original StudyBuddy artwork, and the existing role tabs stay functional. Feature carousel gets text and links from actual CMS `what` section, using original StudyBuddy vector graphic assets. A user-controlled **My little plan** section uses genuine free **SpringCheck**, **JellyRadio**, **SwipeToast**, and local-only preferences.
- **Apps `/apps`:** if a real image-rich app list is detected, the React bridge adds the real FlexCarousel above the existing list. Categories are sourced from existing app elements. **GlideSelect**, **JellyRadio**, **PulseHeart**, **SquishSwitch** and **FolderFloat** provide filtering, a local saved shelf, animation toggling and interactive folder selection. When no usable images exist, the unchanged Blade page remains the only catalogue.
- **Roles `/roles`:** original-studybuddy artwork role gallery is inserted above the existing Blade content (only once).
- **Teacher/parent/student dashboards:** a small, optional personalized corner is inserted only for recognized authenticated dashboard paths, with **SpringCheck** and **JellyRadio**. It stores user-selected checkboxes/role preference only in this browser, **not in Laravel grades or school records**.
- **School information:** existing page stays as-is; the original **Lanyard** (Three.js) source is registered as `SBLanyard` for future permission-safe school card preview once a real design asset exists. **Crosshair** is registered as `SBCrosshair` for localized app scene interactions; not global cursor replacement.

## Actual source and scope

Source files for `FlexCarousel`, `FolderFloat`, `Crosshair` and `Lanyard` are copied directly from the user's supplied *complete JS-CSS React Bits source*. Other micro components are downloaded from the official **free** React Bits registry (`https://reactbits.dev/r/<Name>-JS-CSS.json`) on the user's Mac **before changing the project**. `resources/js/studybuddy-react/components/StudyBuddyWave3/micro` stores all 34 catalog entries that can be retrieved; 6 are imported for this release. Extra source availability is listed in `CATALOG_DOWNLOAD_REPORT.json`.

Important: **Downloaded source ≠ active on every page.** Components such as `CodeSlots` must not be inserted without an actual OTP flow, `VoicePill` without a recording workflow/consent policy, `SlideCommit` without a real server action, and `Shredder` without authorized deletion. Avoid fake “saved” server statuses or invented grades. This package uses the relevant micro controls now; the rest are available to integrate during feature development.

## Pro galleries: licensed installation ONLY

The paid React Bits Pro components **Elastic Reel**, **Helix Gallery**, and **Depth Ribbon** are **not included** in this free source package. No Pro code was fabricated. We use the *vanilla CSS* variants for Laravel when a Starter-or-higher license is available; Tailwind variants require Tailwind to be set up. The Pro registry snippet is merged into **existing** `components.json` without touching the rest of its config:

```json
"registries": {
  "@reactbits-starter": {"url":"https://pro.reactbits.dev/api/r/starter/{name}.json","headers":{"Authorization":"Bearer ${REACTBITS_LICENSE_KEY}"}},
  "@reactbits-pro": {"url":"https://pro.reactbits.dev/api/r/pro/{name}.json","headers":{"Authorization":"Bearer ${REACTBITS_LICENSE_KEY}"}}
}
```

If `components.json` doesn't exist, the installer does **not** initialize shadcn automatically because shadcn could rewrite Laravel's existing frontend. Follow https://pro.reactbits.dev/docs/installation; `npx shadcn@latest init` may prompt for a component library. Once you have a paid license, add it locally to a **gitignored** root `.env.local` as `REACTBITS_LICENSE_KEY=...` and run from the Laravel project root:

```bash
npx shadcn@latest add @reactbits-starter/elastic-reel-css
npx shadcn@latest add @reactbits-starter/helix-gallery-css
npx shadcn@latest add @reactbits-starter/depth-ribbon-css
```

**These commands retrieve licensed files; they do NOT automatically mount them.** Mount via the working React registry with real CMS images after checking the downloaded component API. Do not paste or transmit the license key to ChatGPT or commit `.env.local`.

## Confirm

1. Run the installer from `~/Downloads` with `bash ...`; it uses only `~/Desktop/StudyBuddy-main` and refuses `APP_ENV=production`.
2. Open `/`, `/apps`, `/roles` and one permitted dashboard. Verify the old test tube is gone, app links still work, personalized controls react, mobile scrolling works, and the light/dark schemes are readable.
3. In Chrome Console, `window.StudyBuddyReactBridge.available()` should include `SBAppFlexGallery`, `SBRoleFlexGallery`, `SBFeatureFlexGallery`, `SBPersonalCorner`, `SBCrosshair`, `SBLanyard`.
4. If build or Blade compile fails, the script restores changed files and the old Vite asset directory. The old database is never touched.

## Performance

`FlexCarousel` uses WebGL2 through `ogl`: if WebGL2 is unsupported or the visitor requests reduced motion, an accessible scrolling set of actual app images is shown instead. Full source remains in `resources/js` for editable React integration. Heavy WebGL worlds should remain opt-in, one per view. All interactive elements should be checked for keyboard/focus and screen-reader behavior before deployment.

**Licenses:** Free React Bits is under its published MIT + Commons Clause license, which is not an unrestricted resale license for component collections. https://github.com/DavidHDev/react-bits/blob/main/LICENSE.md. Pro has a separate commercial license. See the official sites for terms.
