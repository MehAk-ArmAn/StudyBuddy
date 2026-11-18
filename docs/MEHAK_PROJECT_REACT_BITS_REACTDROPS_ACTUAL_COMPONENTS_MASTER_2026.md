# MEHAK PROJECT — REACT BITS + REACTDROPS ACTUAL-COMPONENTS MASTER

**Version:** 1.0  
**Created:** 14 November 2026  
**Status:** Permanent proposed Project Source; becomes available across Mehak Project chats when uploaded to **Mehak Project → Sources**.  
**Scope:** Mehak Project only — current and future UI-bearing websites, web applications, Flutter/mobile apps, games, dashboards, portfolios, landing pages, and interfaces for products and clients.  
**Priority:** **High-priority UI component sourcing and implementation policy.** This supplements, but does not override, Project privacy, brand boundaries, user requests, app stability, accessibility, license compliance, security, or release rules.

---

## 0. READ THIS FIRST — THE USER'S EXACT INTENT

**WE ARE USING THESE LIBRARIES REGULARLY, DEEPLY, AND FOR REAL.**

The main UI component sources for all eligible Mehak Project software and interface work are:

1. **React Bits** — https://reactbits.dev/  
   Official source: https://github.com/DavidHDev/react-bits
2. **ReactDrops** — https://reactdrops.dev/  
   Official components: https://reactdrops.dev/components  
   Official source: https://github.com/crisjavr/reactdrops

**NO “INSPO-ONLY” IMPLEMENTATIONS.**

- DO NOT look at React Bits / ReactDrops, merely copy their *aesthetic*, and then deliver an unrelated generic interface while claiming the libraries were used.
- DO NOT treat these sites as just Pinterest, Dribbble, or a mood board.
- DO inspect the real documented components, their live demos, source, controls, dependencies, and suitability.
- DO actually install/copy/integrate a relevant **real component from the library** into compatible React codebases, configure it, and connect it to real product behavior.
- Where the target is **not React**, DO create a clearly identified **native adaptation / functional port** that reproduces the selected component's relevant appearance, interactions, and motion as closely as practical with native APIs; never pretend the original React source runs in Flutter or another native framework.
- If the component cannot be used or faithfully ported, say why, show the actual alternative, and **never falsely label it a React Bits / ReactDrops implementation**.

**The goal:** distinctive, highly interactive, premium, memorable **REAL** products that retain correct behavior, suitable accessibility, and strong performance — not static screenshots, fake clickable elements, random animated decoration, or design-mockup replacements.

**Default question for every new feature/screen:**
> “Which actual React Bits or ReactDrops components should we use for this UI, and how will they be installed or ported and connected to the user's real workflow?”

---

## 1. WHAT “MAIN SOURCE” MEANS

Use these libraries as the **first-choice component catalogue and motion language** for interface-building work. Search both for each substantial UI task before inventing an equivalent implementation.

They are relevant to:

- Page entrance, page transitions, animated typography, section reveals, loaders, microinteractions.
- Buttons, cards, navbars, tabs, menus, hover/press feedback, cursor interactions, visual states.
- Heroes, galleries, image treatments, branded transitions, backgrounds, data displays, dashboard elements.
- Interactive educational/game feedback, onboarding, polished empty/error/success states.
- Product landing pages, marketing showcases, client brand experiences, web portals, admin/CMS interfaces.

**Heavy and regular use** means routinely selecting multiple meaningful component candidates across a feature or product and actually integrating the good ones. It **does not** mean forcing every tiny control to animate, putting WebGL everywhere, or sacrificing quality for a quota.

For each implementation, pick what genuinely fits the brand and screen, make the component functional, and use it consistently across the experience.

**Preference order for an eligible React UI:**
1. Use a real **React Bits** component when it solves the job elegantly.
2. Use a real **ReactDrops** component when it offers a more distinctive or appropriate interaction/material.
3. Combine components from both when they coexist cleanly and create a coherent system.
4. Use existing product design-system or platform-native primitives where more appropriate for standard accessibility-critical UI, large datasets, complex forms, low-end devices, and performance-sensitive interactions.
5. Only build custom UI when the libraries do not provide a suitable solution or the result needs essential product-specific behavior — explain the reason, briefly.

**Never substitute mere resemblance for actual integration when the project is React-compatible.**

---

## 2. VERIFIED LIBRARY FACTS / OFFICIAL LINKS

### React Bits

- Catalogue + interactive documentation: https://reactbits.dev/
- Official GitHub: https://github.com/DavidHDev/react-bits
- Current install guide linked from project: https://reactbits.dev/
- Official project documents React components in JavaScript/TypeScript with CSS or Tailwind variants. Its official repo shows source-copy/CLI installation and examples.
- Current documented license (as checked 14 Nov 2026): **MIT + Commons Clause**; check the current exact license in the repository at the moment of use: https://github.com/DavidHDev/react-bits/blob/main/LICENSE.md
- Allowed in finished commercial apps and websites subject to the license; **do not sell, sublicense, or redistribute the components themselves** as standalone assets or ported component libraries. Preserve copyright/license notices.
- React Bits Pro is separate, paid, and is **not** assumed purchased. Use free eligible assets unless Mehak explicitly authorizes a purchase and provides access.

### ReactDrops

- Component catalogue with demos/controls/install steps: https://reactdrops.dev/components
- Official repo: https://github.com/crisjavr/reactdrops
- Official usage terms: https://reactdrops.dev/terms
- The site uses `reactdrops.dev` as the primary domain; do not mistake lookalike domains for official installation registries.
- The documented **ReactDrops Component License 1.0** permits qualifying personal and commercial end products, including client apps/websites, but prohibits redistributing the components as reusable libraries, UI kits, templates, registries, starters, or component packs without permission. Preserve all mandated source notices and any third-party notices. Read the actual installed component license before use.
- ReactDrops demo images, fonts and assets can have **separate** licenses. Do not silently ship demo photography, licensed fonts, or other example media in a client/product release.

**Important license distinction:** These licenses are NOT blanket permission to create and publish a reusable PCL component kit containing their source code, especially not a public/downloadable one. A **private internal inventory of links, IDs, our integration notes, design tokens, wrappers and project-specific adaptations** is useful; distributing the underlying library components as a library/kit or selling their ports is restricted. Verify terms again whenever shipping/distributing code, client source, libraries, or templates.

---

## 3. THE NON-NEGOTIABLE BUILD ROUTE

For a new screen, UI redesign, or meaningful UX feature:

### STEP A — Inspect the *real* project first

- Identify its exact owner/brand, target audience, technical stack, repository, platforms, current screens, accessibility needs, and release constraints.
- Check existing components, tokens, routing, state management, auth, data flows, integrations and functional tests.
- Preserve the existing code, Git history, identifiers, backend, Firebase configuration and approved client assets. Prefer surgical patches and reversible changes over replacing apps or major repo restructuring.
- Never assume that an idea, screenshot, demo or source file proves an app's current production status.

### STEP B — Inspect *both* official libraries

- Look up **actual named components** and their current documentation. Do not invent an exact component name/API/install command.
- For each useful component: review demo, code variant, source dependencies, bundle cost, mobile behavior, keyboard/touch behavior, reduced-motion behavior, and license.
- Use the chosen component's official install mechanism / source-code instructions. Do not paste a guessed package name.

### STEP C — Select a cohesive set of actual components

- Identify the precise elements and roles: e.g. one purposeful entrance, one card/gallery pattern, one feedback/microinteraction, and possibly a background effect, as warranted.
- Choose by screen purpose, not by maximum animation count. Integrate into a consistent design system (colors, type, spacing, corner radii, gradients, depth, motion speed).
- Avoid stacking expensive particle/WebGL systems or several hero effects on the same screen.
- Keep branding distinct: React Bits / ReactDrops are component **sources**, not the product's brand identity.

### STEP D — Implement, do not merely describe

For compatible React projects:
- Install/copy the **actual library code** using the official documentation, pin/check dependencies, integrate with the existing framework, and hook up real events/data/navigation.
- Use appropriate JS/TS + CSS/Tailwind variants for the existing repo, not a forced migration just to install a component.
- Adapt colors, text, layout, content, and behavior to the specific brand while maintaining the component's essential interactive character.
- Preserve required license, copyright and third-party notices in the project.

For other UI stacks:
- Use the real library component as an **implementation reference** for a native **reimplementation/port**, not as vague visual inspiration. Reproduce key states, transitions, timing, feedback and semantics with that platform's supported APIs.
- Give a transparent provenance label: `Port of / adapted from [actual component name]`, **not** `Installed React Bits component`.
- A port may itself be subject to the component's license restrictions; keep notices and do not redistribute it as a standalone kit/library without checking permission.
- If faithful implementation is too costly or incompatible, tell Mehak and build a suitable native alternative instead.

### STEP E — Validate the experience

- Run lint/typecheck/build and available unit/widget/integration tests.
- Check tiny and large screens, iOS/Android where relevant, tap targets, keyboard focus, screen-reader labeling, contrast, touch/hover fallback, offscreen behavior, dark mode if present, and no accidental interactions.
- Provide `prefers-reduced-motion` support on the web and platform reduced-motion support in native apps; stop or simplify problematic continuous effects.
- Test realistic lower-end devices, startup time, scroll smoothness, memory/battery, WebGL fallback, broken connections, and loading/error states.
- Check that ads, notifications, privacy permissions, child-directed design, and religious content rules remain correct where applicable.

### STEP F — Record real usage

Keep a small factual component record for each project:

| Field | What to record |
|---|---|
| Project / screen | Exact app/site and screen name |
| Library | React Bits / ReactDrops |
| Component | Exact official component name and documentation URL |
| Implementation | Installed original / modified original / native port / alternative |
| Source version | Revision/date and install command when available |
| Files changed | Paths and summary; no made-up edits |
| Customization | Colors, typography, states, interaction mapping |
| Dependencies | Added libraries, Canvas/WebGL, runtime requirements |
| License | Current license and retained notices |
| QA | What was actually tested and what remains unverified |

Do not claim a component is installed or tested unless the actual files/build prove it.

---

## 4. FRAMEWORK-SPECIFIC IMPLEMENTATION RULES

### A. React / Next.js / Vite / React web applications — PRIMARY NATIVE USE

**Strong default:** use the real React Bits / ReactDrops component source in production-ready UI whenever relevant.

- Use named imports, actual source files and official documented install commands.
- Connect to real routes, APIs, app state, callbacks and accessibility semantics. A gorgeous visual with dead buttons FAILS.
- Hydration/SSR-aware integration matters for Next.js: isolate browser-only Canvas, WebGL, cursor or window-dependent effects into appropriate client-side boundaries; test the actual build.
- If existing site uses server-rendered templates or a non-React architecture, do not silently rewrite the entire stack. Evaluate a bounded React island/embedded component only when technically maintainable and justified.

### B. Flutter Android/iOS — FAITHFUL NATIVE PORTS, NOT A REACT IMPORT

**React libraries do not run directly as Dart widgets.**

- Use React Bits/ReactDrops to select exact, concrete interactions and motions; implement them with Flutter's own widgets, animations, CustomPainter, shaders, gesture/physics APIs, and suitable vetted packages as needed.
- Faithfully port the actual behavior where feasible: entry/exit, hover/tap equivalents, physics, animations, gradients, states, haptics, confetti, cards and transitions.
- Do not place a React website in WebView merely to claim component usage, especially for core learning gameplay, forms, accessibility, offline screens, or ad-supported production apps.
- Do not introduce dependency/version changes that destabilize an app just for polish.
- Respect Flutter release locks, Families/child-safety, real AdMob vs test-mode separation, notification permissions, offline behavior and the macOS external-drive cleanup/stability workflow.
- Keep a note that this is a **native adaptation of a named source component**, with relevant license restrictions observed.

### C. Static sites / CMS / client-owned website

- Where React is viable, integrate scoped real library source without destroying the CMS, SEO, editor tools, forms or page load.
- Otherwise use a carefully engineered native equivalent and label it accurately.
- Prioritize good contrast, readable copy and original approved photos over an animation that hides a service/product message.

### D. Unity / games / other runtimes

- Use the libraries as a **specific component-behavior specification** for custom native recreation only where worthwhile. Never imply React source is directly integrated into Unity scenes or native mobile UI.
- Prefer gameplay clarity and stable frame rates over showcase animations.

### E. Slides, static social posts, videos, print and other non-UI artifacts

- The libraries can produce real capture/exported assets **only when the library's actual tooling and licenses support it**, but they are **not** a blanket requirement for non-interactive artifacts. Follow the dedicated brand/marketing/content workflows and real-media sourcing rules.
- A screenshot or rendered animation is not a working component. Label it as an asset, not a software integration.

---

## 5. SHARED PROJECT ECOSYSTEM ROUTING

Apply this source automatically to any **UI implementation task** connected to Mehak Project; route the brand-specific design through its own active project source.

| Project / ecosystem | How to apply this component-first policy |
|---|---|
| **Pixel Crafts Lab Studio (PCL) / PCL portfolio** | Premium, consistent page/UI motion, impressive product demos, usable high-end interactive sections, genuine component-backed implementations. |
| **PCL-OS** | Purposeful dashboards, management workflows, navigation, statuses, microinteractions; preserve backend, database, installed previews and isolated tests. |
| **StudyBuddy site + CMS** | Playful but credible education UI, responsive cards, navigation, learning feedback, usable administration; no fake functionality or unsupported learning claims. |
| **StudyBuddy learning apps / Flag Quest / Around the Globe** | Translate selected source animations to age-appropriate Flutter/native widgets and actual gameplay feedback. Keep games usable and supportive; do not overwhelm learners with motion. |
| **Verdyve, Toyviko, JamJinx, NumBlip, Bloxabet, MatchMallow, Mathibble, GlobePop, Spellio, TypeJett, etc.** | Select distinctive but purposeful UI/motion; native implementation for Flutter; keep release, consent/child safety, ads and stability intact. |
| **BangTan.info** | Fandom-native, premium BTS experience with galleries, media transitions, discovery, profile interactions, quizzes and content. Keep sourcing and fan content honest; avoid unlicensed BTS media. |
| **Trusted Touch client website/content** | Brand-appropriate, calm, premium React/web components when implementing an actual website; real approved service imagery visible, not buried under trendy animation. Do not mix the client's brand with PCL's. |
| **Fikar-e-Adab, Mashhud, Rashadan** | Readability, cultural appropriateness and content trust first; restrained relevant transitions. Honor ad-free exceptions for designated faith/community apps. |
| **Future Mehak Project apps, websites, games, clients** | Default to the same real component investigation/integration rules, with their own unique brand style, functional needs and constraints. |

**This source does not create a shared identity between distinct brands, transfer client-owned IP, or grant permission to reuse private client materials across projects.** Each product has its own audience, design and licenses.

---

## 6. CURRENT CATALOGUE STARTING POINTS — ALWAYS RECHECK LIVE

These are *names to look up*, **NOT instructions to insert every one**, nor a guarantee that every API remains unchanged.

**React Bits — browse actual categories and docs:**
- Text animations / reveal transitions
- Animated components and card treatments
- Navigation, menus, galleries
- Microinteractions and input feedback
- Dynamic but controlled backgrounds
- Example documented installer (verify live before running):

```bash
npx shadcn@latest add @react-bits/SplitText-TS-TW
```

**ReactDrops — actual catalogue examples observed 14 Nov 2026:**
- Liquid Glass Orb — suited to voice/AI agent states when a real agent exists
- Liquid Glass Logo — branded logo reveal
- Fan Gallery — portfolio / product card gallery
- Topographic Flow and Topographic Terrain — rich backgrounds where performance fits
- Shape Physics — playful physics-led interaction where appropriate
- Chromatic Button — distinctive CTA; ensure contrast and fallback
- Fractal Glass — deliberate image treatment, not a replacement for seeing real photos
- Refracted Text — selective premium text effect
- Showcase Pointer — demonstration overlay; does not automatically make underlying UI interactive
- Bubble UI Button / Fluid Typing Background — evaluate current demo/implementation before use
- Example official install command (verify live before running):

```bash
npx shadcn@latest add https://reactdrops.dev/r/fan-gallery.json
```

No fabricated component APIs, no guessed dependencies, no unverified pro features.

---

## 7. ANTI-PATTERNS — DO NOT DO THESE

- Claim “used React Bits” when only a lookalike CSS gradient or screenshot is delivered.
- Use library visuals as loose inspiration and discard actual components in a compatible React app without reason.
- Add nonfunctional buttons, dead UI, fake data, fake analytics, fake product screens or misleading screenshots.
- Copy-paste demo content, logo, stock photos, fonts, external assets or BTS/client media without rights checks.
- Install dependencies without auditing source, runtime, library license, security and release implications.
- Hide basic content under oversized animated overlays, force animations on reduced-motion users, or tank performance.
- Put expensive WebGL motion on every page, especially low-end mobile or child-focused games.
- Rewrite a working backend/app/repository only to make a prettier screen.
- Change app-store configurations, production ad IDs, release status, paid subscriptions, client permissions, or published branding without authorization.
- Repackage React Bits/ReactDrops source into an independently distributed **component library / starter / template / kit**, or sell ports separately without reviewing or obtaining the required rights.
- Claim a project-wide file is installed/uploaded when only a local download was produced.

---

## 8. DEFAULT RESPONSE / DELIVERY FORMAT FOR FUTURE UI TASKS

When Mehak says “upgrade UI/UX,” “make this premium,” “use our design system,” “build a screen,” “redesign this app,” or similar:

1. Identify the exact **project, stack and screen** from Project-local material; don't demand info already available in the Project.
2. Identify **actual React Bits / ReactDrops components by name and official URL**, choosing only the relevant ones.
3. Say whether these are **original React integrations** or **Flutter/native ports**. Never confuse the two.
4. Build/update real project files when source access and tools permit; give accurate ready-to-use code/patches otherwise.
5. Ensure that all meaningful UI interactions work and the brand remains recognizable.
6. Report changes, licensing/dependencies, QA results, and remaining issues truthfully and briefly. If Mehak asks “short,” reply like a short DM.

**Definition of done:** real components or explicitly identified native ports + connected functionality + original brand preserved + performance/accessibility checked + licenses respected + existing project not destructively replaced.

---

## 9. SOURCE PRECEDENCE / INTEGRATION WITH EXISTING MEHAK PROJECT FILES

Use this file **alongside**, not instead of, the following Project sources wherever relevant:

- `01_MEHAK_PROJECT_CORE_MASTER_2026.md` — privacy, separate user identity, cross-chat/project routing, branding boundaries.
- `02_MEHAK_PROJECT_MARKETING_MASTER_2026.md` — marketing workflows, real-media and approval requirements.
- `03_MEHAK_PROJECT_TECH_RELEASE_MASTER_2026.txt` — Flutter stability and release restrictions.
- `PCL_2026_RELEASE_LOCK.md` — Play release, ads, family safeguards and no-ads exceptions.
- `StudyBuddy_Academic_Category_Master_Guide_2026.docx` — verified learning categories and academic claims.
- Any newer and more specific user-supplied source, repository, client guide, brand kit or scoped instruction applicable to the actual project.

**Conflict handling:** Explicit current user instructions and privacy/safety/license/security/release requirements win over component selection. More recent verified project details win over dated historical snapshots. If a specific app technically cannot run a component, use its best safe and honestly labeled implementation route rather than breaking the project to follow an aesthetic rule.

---

## 10. PERMANENT HANDOFF MESSAGE FOR OTHER CHATS OR AI CODING TOOLS

> **Mehak Project UI SOURCE LOCK:** React Bits (https://reactbits.dev/) and ReactDrops (https://reactdrops.dev/) are our primary, heavily reused sources for real UI components and animation behaviors across UI-bearing projects. **NO INSPIRATION-ONLY imitation**: for React apps, inspect the actual component, install/integrate its real source, customize it for the current brand, and connect it to working app behavior. For Flutter/native/Unity, select exact named components and faithfully recreate their behavior using native APIs, clearly marking them as ports/adaptations rather than claiming React runs natively. Preserve existing code/brand, meet accessibility/performance standards, check current licenses/notices/assets, and do not redistribute source as a kit/library or invent a successful installation. This is a strong default for every UI/UX task, without overriding project safety, release, privacy, client and content rules.

---

**Maintenance:** Recheck live catalogs, installation commands, dependencies and license terms at the start of actual implementations. Update this source when the user changes the policy. Creating this Markdown file alone does **not** automatically add it to ChatGPT Project Sources or change existing repositories.
