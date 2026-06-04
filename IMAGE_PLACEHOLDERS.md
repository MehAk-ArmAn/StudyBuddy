# StudyBuddy Image Assets and Placeholders

The frontend is wired to use real generated assets from `public/assets/studybuddy/` whenever those files exist. If an asset is missing, the reusable Blade partial renders a neon fallback art card instead of a gray box.

| Label / asset slot | Preferred file in `public/assets/studybuddy/` | What should replace it if updated | Recommended size | Used in Blade file |
| --- | --- | --- | --- | --- |
| `LOGO_ICON` | `logo-icon.png` | StudyBuddy logo icon | 256×256 PNG/WebP | `resources/views/partials/navigation.blade.php`, `resources/views/partials/footer.blade.php`, `resources/views/apps/index.blade.php` |
| `HERO_MASCOT_IMAGE` | `hero-dolphin-book.png` | Main dolphin/book mascot render | 900×760 transparent PNG/WebP | `resources/views/pages/home.blade.php`, `resources/views/pages/showcase.blade.php` |
| `PLANET_RINGED_LG` | `planet-ringed-lg.png` | Ringed purple/blue planet background | 600×600 transparent PNG/WebP | `resources/views/pages/home.blade.php`, CSS background layers |
| `PLANET_PURPLE_LG` | `planet-purple-lg.png` | Purple planet background | 600×600 transparent PNG/WebP | `resources/views/pages/home.blade.php`, CSS background layers |
| `SPARKLES_PACK` | `sparkles-pack.png` | Star/sparkle overlay texture | 1400×900 transparent PNG/WebP | `public/assets/css/studybuddy.css` |
| `APP_CARD_IMAGE_MATH` | `app-math-quest.png` | Math Quest glowing plus/minus planet art | 640×420 PNG/WebP | `resources/views/partials/app-card.blade.php`, `resources/views/pages/home.blade.php`, `resources/views/pages/showcase.blade.php`, `resources/views/apps/math-quest.blade.php` |
| `APP_CARD_IMAGE_SPELLING` | `app-spelling-sprint.png` | Spelling Sprint rocket art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_READING` | `app-reading-garden.png` | Reading Garden book art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_FOCUS` | `app-focus-forest.png` | Focus Forest character art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_PLANNER` | `app-planner-city.png` | Planner City calendar art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_QUIZ` | `app-quiz-galaxy.png` | Quiz Galaxy trophy art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_SHAPES` | `app-shapes-lab.png` | Shapes Lab geometry art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `APP_CARD_IMAGE_FLASHCARDS` | `app-flashcard-castle.png` | Flashcard Castle art | 640×420 PNG/WebP | App card, home strip, showcase app store |
| `QR_CODE_IMAGE` | pending | QR code for Math Quest downloads | 320×320 SVG/PNG | `resources/views/apps/math-quest.blade.php` |
| `DASHBOARD_BUDDY_IMAGE` | `hero-dolphin-book.png` until a dashboard-specific mascot exists | Younger-child Buddy cloud/dashboard mascot | 700×520 transparent PNG/WebP | `resources/views/demo/dashboard.blade.php`, `resources/views/pages/showcase.blade.php` |
| `BUDDY_CUSTOMIZATION_IMAGE` | `hero-dolphin-book.png` until shop render exists | Blooket-style Buddy customization render | 900×900 transparent PNG/WebP | `resources/views/pages/rewards.blade.php`, `resources/views/pages/showcase.blade.php` |
| `MATH_QUEST_BUDDY_IMAGE` | `hero-dolphin-book.png` until game helper render exists | Buddy helper for playable Math Quest screen | 700×700 transparent PNG/WebP | `resources/views/apps/math-quest-play.blade.php` |
| `FOOTER_QR_IMAGE` | pending | Footer download QR code | 240×240 SVG/PNG | `resources/views/partials/footer.blade.php` |
