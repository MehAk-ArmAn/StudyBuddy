# React Bits / ReactDrops licensing and installation policy

**Verified documentation as of 18 November 2026:** [React Bits Pro installation](https://pro.reactbits.dev/docs/installation) requires a React project with shadcn and an authenticated registry/license; free React Bits has a public source/registry with its own license terms. Store license keys outside Git. We do **not** claim to have a Pro license or access to its source.

## Existing project paths

- `studybuddy-reactbits/pro-ui/components.json` already defines registry aliases `@reactbits-starter` and `@reactbits-pro` with the header `Authorization: Bearer ${REACTBITS_LICENSE_KEY}`; review the exact latest provider instructions if a license is purchased.
- The React workspace is `studybuddy-reactbits/pro-ui/` with its own `package.json`, not the Laravel root. Existing Vite/React source and locally built UI assets are separate from Laravel/Blade templates.
- **Do not run** `npx shadcn@latest add @reactbits-pro/...` with fake credentials, do not scrape Pro source, and do not add a private key into `components.json`, the public bundle, docs, screenshots, shell history or `.env.example`.
- If a licensed teammate gains legitimate access, first read official current terms, ensure license is legitimately owned and usable by that developer/project, then install only the necessary items. Update `COMPONENT_SHIPPING_LEDGER.md` with source, license and exact paths.

## Original free replacements vs official source

A screen can be designed after the **public description** of a Pro item (e.g., “detail side panel”, “activity heatmap”) using independently authored code. Label it `original equivalent`; it must not contain, imply or claim the actual Pro block was installed. Review the separate license/attribution of public React Bits and ReactDrops code, images and fonts before copying any free assets into an app.

## Integration recipe for an approved component

1. Assign a single role-bound StudyBuddy page and data contract from `REACT_COMPONENT_PLACEMENT_MATRIX.md`.
2. Verify license; record package URL/version and source/attribution. Ensure dependencies are installable in the current React workspace.
3. Build one locally bundled React island and mount it in Blade after authentication/permissions. No second global header/footer, no duplicate app shells.
4. Use original StudyBuddy color/spacing/type tokens, readable dark/light surfaces, device-safe animation, screen reader labels, keyboard behavior and reduced motion.
5. Connect real Laravel APIs; the component should handle zero data, validation errors and server-denied access gracefully.
6. Add PHP role/tenant/ownership tests and client interaction tests, verify on 390/768/1440 breakpoints, inspect payload/performance, and only then mark installed.
