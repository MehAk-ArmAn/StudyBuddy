from pathlib import Path
from datetime import datetime
import re

apps_view = Path("resources/views/studybuddy/final/apps.blade.php")
detail_view = Path("resources/views/studybuddy/final/app-detail.blade.php")
css_path = Path("public/assets/css/studybuddy-connected-apps.css")
js_path = Path("public/assets/js/studybuddy-connected-apps.js")

for p in [apps_view, detail_view, css_path, js_path]:
    if not p.exists():
        raise SystemExit(f"❌ Missing {p}")

stamp = datetime.now().strftime("%Y%m%d_%H%M%S")
for p in [apps_view, detail_view, css_path, js_path]:
    p.with_suffix(p.suffix + f".bak_{stamp}").write_text(p.read_text())

# ---------------- Apps page: user-friendly text + extra real image previews ----------------
text = apps_view.read_text()

text = text.replace(
    "Every mini-app is connected to the same app database, the same image paths, the same detail pages, and the same platform system.",
    "Pick a world, follow a tiny mission, collect progress, and make learning feel playful again."
)

text = text.replace(
    "@foreach($apps as $app)\n                @php\n                    $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];",
    """@foreach($apps as $app)
                @php
                    $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];"""
)

# Add preview image helper inside card @php block
text = text.replace(
    "$image = $assetUrl($app->safeHeroImage());\n                    $searchText",
    """$image = $assetUrl($app->safeHeroImage());

                    $previewCandidates = [
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_main-icon.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_icon-512.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-glow.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-small.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/05_planets-bg/{$app->slug}_mini-planet.png",
                        $app->safeHeroImage(),
                    ];

                    $previewImages = collect($previewCandidates)
                        ->map(fn($path) => $assetUrl($path))
                        ->filter()
                        ->unique()
                        ->take(4)
                        ->values();

                    $searchText"""
)

text = text.replace(
    """<img src="{{ $image }}" alt="{{ $app->name }} artwork" loading="lazy">
                        <span>{{ $app->icon ?: '✨' }}</span>""",
    """<img src="{{ $image }}" alt="{{ $app->name }} artwork" loading="lazy">
                        <span>{{ $app->icon ?: '✨' }}</span>

                        <div class="sb-orbit-sparkles" aria-hidden="true">
                            <i></i><i></i><i></i><i></i><i></i>
                        </div>

                        @if($previewImages->count() > 1)
                            <div class="sb-card-mini-gallery" aria-hidden="true">
                                @foreach($previewImages->skip(1)->take(3) as $previewImage)
                                    <img src="{{ $previewImage }}" alt="">
                                @endforeach
                            </div>
                        @endif"""
)

apps_view.write_text(text)

# ---------------- Detail page: remove technical text + use extra app images, no glitchy spark images ----------------
text = detail_view.read_text()

# Stop reading 03_sparks image files on detail page
text = text.replace(
"""        'star' => $assetUrl("assets/studybuddy-imgs/02_apps/{$slug}/03_sparks/{$slug}_star-main.png"),
        'spark' => $assetUrl("assets/studybuddy-imgs/02_apps/{$slug}/03_sparks/{$slug}_spark-small.png"),
        'comet' => $assetUrl("assets/studybuddy-imgs/02_apps/{$slug}/03_sparks/{$slug}_comet-trail.png"),""",
"""        'star' => null,
        'spark' => null,
        'comet' => null,"""
)

# Add preview image collection after decor
text = text.replace(
"""    $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];""",
"""    $previewImages = collect([
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/01_app-icon/{$slug}_icon-512.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/02_orbs/{$slug}_orb-glow.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/02_orbs/{$slug}_orb-small.png",
        "assets/studybuddy-imgs/02_apps/{$slug}/05_planets-bg/{$slug}_mini-planet.png",
        $app->safeHeroImage(),
    ])->map(fn($path) => $assetUrl($path))->filter()->unique()->take(5)->values();

    $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];"""
)

# Remove technical "Connected image"
text = text.replace(
"""        <article><span>Safety</span><strong>{{ $app->safety_note ? 'Guided learning' : 'Safe practice mode' }}</strong></article>
        <article><span>Connected image</span><strong>{{ $app->safeHeroImage() }}</strong></article>""",
"""        <article><span>Safety</span><strong>{{ $app->safety_note ? 'Guided learning' : 'Safe practice mode' }}</strong></article>
        <article><span>Experience</span><strong>Playful, calm, and progress-friendly</strong></article>"""
)

# Add generated sparkles + preview gallery inside detail art
text = text.replace(
"""            <img class="main-art" src="{{ $heroImage }}" alt="{{ $app->name }} artwork">
            @if($decor['orb'])<img class="float orb" src="{{ $decor['orb'] }}" alt="" aria-hidden="true">@endif
            @if($decor['smallOrb'])<img class="float small-orb" src="{{ $decor['smallOrb'] }}" alt="" aria-hidden="true">@endif
            @if($decor['spark'])<img class="float spark" src="{{ $decor['spark'] }}" alt="" aria-hidden="true">@endif""",
"""            <img class="main-art" src="{{ $heroImage }}" alt="{{ $app->name }} artwork">

            <div class="sb-orbit-sparkles detail-sparkles" aria-hidden="true">
                <i></i><i></i><i></i><i></i><i></i><i></i><i></i>
            </div>

            @if($decor['orb'])<img class="float orb" src="{{ $decor['orb'] }}" alt="" aria-hidden="true">@endif
            @if($decor['smallOrb'])<img class="float small-orb" src="{{ $decor['smallOrb'] }}" alt="" aria-hidden="true">@endif

            @if($previewImages->count() > 1)
                <div class="sb-detail-mini-gallery" aria-hidden="true">
                    @foreach($previewImages->skip(1)->take(4) as $previewImage)
                        <img src="{{ $previewImage }}" alt="">
                    @endforeach
                </div>
            @endif"""
)

detail_view.write_text(text)

# ---------------- CSS: smoother motion, more sparkles, cleaner layers, no fixed glitch layer ----------------
css = css_path.read_text()
css = re.sub(
    r"/\* === StudyBuddy no-glitch magical user polish === \*/.*?/\* === End StudyBuddy no-glitch magical user polish === \*/",
    "",
    css,
    flags=re.S,
)

css += r'''

/* === StudyBuddy no-glitch magical user polish === */

/* Keep all magic inside the page, not floating over the whole browser */
.sb-connected-apps,
.sb-app-detail-world {
    position: relative;
    isolation: isolate;
}

.sb-life-layer {
    position: absolute !important;
    inset: 0 !important;
    z-index: 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
    opacity: .62 !important;
    contain: strict !important;
}

.sb-life-particle {
    will-change: transform, opacity;
}

/* Hide glitchy decorative sparkle files only. Real app images stay visible. */
.sb-detail-decor.star,
.sb-detail-decor.comet,
.sb-detail-art .spark {
    display: none !important;
}

/* Generated sparkles around real app images */
.sb-orbit-sparkles {
    position: absolute;
    inset: 0;
    z-index: 4;
    pointer-events: none;
}

.sb-orbit-sparkles i {
    position: absolute;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: radial-gradient(circle, #fff 0 22%, var(--app-three, #22d3ee) 35%, transparent 70%);
    box-shadow:
        0 0 12px color-mix(in srgb, var(--app-three, #22d3ee) 72%, transparent),
        0 0 24px color-mix(in srgb, var(--app-one, #7c3cff) 45%, transparent);
    opacity: .88;
    animation: sbTinySparkle 3.8s ease-in-out infinite;
}

.sb-orbit-sparkles i:nth-child(1) { left: 18%; top: 20%; animation-delay: -.2s; }
.sb-orbit-sparkles i:nth-child(2) { right: 18%; top: 24%; width: 10px; height: 10px; animation-delay: -.9s; }
.sb-orbit-sparkles i:nth-child(3) { left: 14%; bottom: 30%; width: 5px; height: 5px; animation-delay: -1.4s; }
.sb-orbit-sparkles i:nth-child(4) { right: 12%; bottom: 24%; animation-delay: -2s; }
.sb-orbit-sparkles i:nth-child(5) { left: 48%; top: 12%; width: 6px; height: 6px; animation-delay: -2.8s; }
.sb-orbit-sparkles i:nth-child(6) { left: 38%; bottom: 12%; width: 9px; height: 9px; animation-delay: -3.1s; }
.sb-orbit-sparkles i:nth-child(7) { right: 42%; bottom: 16%; width: 5px; height: 5px; animation-delay: -3.6s; }

/* More real image moments */
.sb-card-mini-gallery {
    position: absolute;
    left: 16px;
    bottom: 16px;
    z-index: 5;
    display: flex;
    gap: 8px;
    align-items: center;
    padding: 8px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    background: rgba(5, 8, 22, .45);
    backdrop-filter: blur(14px);
    box-shadow: 0 14px 34px rgba(2,6,23,.30);
}

.sb-card-mini-gallery img {
    width: 38px !important;
    height: 38px !important;
    object-fit: contain;
    border-radius: 14px;
    padding: 4px;
    background: rgba(255,255,255,.10);
    filter: drop-shadow(0 8px 14px rgba(0,0,0,.28));
    animation: sbMiniFloat 4.8s ease-in-out infinite;
}

.sb-card-mini-gallery img:nth-child(2) {
    animation-delay: -1.1s;
}

.sb-card-mini-gallery img:nth-child(3) {
    animation-delay: -2.2s;
}

.sb-detail-mini-gallery {
    position: absolute;
    left: 18px;
    right: 18px;
    bottom: 18px;
    z-index: 6;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    padding: 10px;
    border: 1px solid rgba(255,255,255,.13);
    border-radius: 24px;
    background: rgba(5, 8, 22, .48);
    backdrop-filter: blur(16px);
    box-shadow: 0 18px 44px rgba(2,6,23,.34);
}

.sb-detail-mini-gallery img {
    width: 100%;
    height: 58px;
    object-fit: contain;
    border-radius: 18px;
    padding: 6px;
    background: rgba(255,255,255,.09);
    filter: drop-shadow(0 10px 16px rgba(0,0,0,.30));
    animation: sbMiniFloat 5.2s ease-in-out infinite;
}

.sb-detail-mini-gallery img:nth-child(2) { animation-delay: -1s; }
.sb-detail-mini-gallery img:nth-child(3) { animation-delay: -2s; }
.sb-detail-mini-gallery img:nth-child(4) { animation-delay: -3s; }

/* Smooth premium interactions */
.sb-connected-card,
.sb-detail-art,
.sb-detail-outcomes article,
.sb-detail-missions article,
.sb-related-grid a {
    will-change: transform;
}

.sb-connected-card:hover {
    transform: translateY(-8px) scale(1.008);
}

.sb-connected-media img {
    transform-origin: center;
}

.sb-connected-card:hover .sb-connected-media img {
    transform: translateY(-7px) scale(1.045) rotate(-1deg);
}

.sb-connected-card:hover .sb-card-mini-gallery img,
.sb-detail-art:hover .sb-detail-mini-gallery img {
    animation-duration: 3.2s;
}

.sb-detail-art:hover .main-art {
    transform: translateY(-8px) scale(1.035);
}

.sb-detail-art .main-art {
    transition: transform .28s ease, filter .28s ease;
}

/* Extra user-friendly shine, not glitch */
.sb-connected-card::after {
    content: "";
    position: absolute;
    inset: 0;
    z-index: 3;
    pointer-events: none;
    border-radius: inherit;
    background: linear-gradient(120deg, transparent 12%, rgba(255,255,255,.10), transparent 32%);
    transform: translateX(-140%);
    transition: transform .75s ease;
}

.sb-connected-card:hover::after {
    transform: translateX(140%);
}

/* Cleaner mobile motion */
@keyframes sbTinySparkle {
    0%, 100% {
        opacity: .28;
        transform: translateY(0) scale(.75);
    }
    45% {
        opacity: 1;
        transform: translateY(-9px) scale(1.22);
    }
    70% {
        opacity: .62;
        transform: translateY(3px) scale(.96);
    }
}

@keyframes sbMiniFloat {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-7px) rotate(3deg);
    }
}

/* Remove visible user-facing technical-looking tiny path overflow if any old cache remains */
.sb-detail-strip strong {
    max-height: 4.2em;
    overflow: hidden;
}

/* Stronger reduced-motion support */
@media (prefers-reduced-motion: reduce) {
    .sb-life-layer {
        display: none !important;
    }

    .sb-orbit-sparkles i,
    .sb-card-mini-gallery img,
    .sb-detail-mini-gallery img,
    .sb-connected-media img,
    .sb-detail-art .main-art {
        animation: none !important;
        transform: none !important;
    }

    .sb-connected-card:hover,
    .sb-connected-card:hover .sb-connected-media img,
    .sb-detail-art:hover .main-art {
        transform: none !important;
    }
}

@media (max-width: 760px) {
    .sb-card-mini-gallery {
        left: 12px;
        bottom: 12px;
        gap: 6px;
        padding: 7px;
    }

    .sb-card-mini-gallery img {
        width: 32px !important;
        height: 32px !important;
    }

    .sb-detail-mini-gallery {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        left: 12px;
        right: 12px;
        bottom: 12px;
    }

    .sb-detail-mini-gallery img {
        height: 48px;
    }
}

@media (max-width: 460px) {
    .sb-card-mini-gallery img:nth-child(3),
    .sb-detail-mini-gallery img:nth-child(4) {
        display: none;
    }
}
/* === End StudyBuddy no-glitch magical user polish === */
'''

css_path.write_text(css)

# ---------------- JS: keep particles inside the page + add magnetic gentle motion ----------------
js = js_path.read_text()
js = re.sub(
    r"// === StudyBuddy no-glitch magical user polish ===.*?// === End StudyBuddy no-glitch magical user polish ===",
    "",
    js,
    flags=re.S,
)

js += r'''

// === StudyBuddy no-glitch magical user polish ===
(() => {
    const root = document.querySelector('.sb-connected-apps, .sb-app-detail-world');
    if (!root) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Remove old body-level magic layer if it exists from previous version
    document.querySelectorAll('body > .sb-life-layer').forEach((oldLayer) => oldLayer.remove());

    // Put magic inside the actual app page only
    let layer = root.querySelector(':scope > .sb-life-layer');
    if (!layer) {
        layer = document.createElement('div');
        layer.className = 'sb-life-layer';
        layer.setAttribute('aria-hidden', 'true');
        root.prepend(layer);
    }

    if (!layer.dataset.ready) {
        const styles = getComputedStyle(root);
        const one = styles.getPropertyValue('--app-one').trim() || '#7c3cff';
        const two = styles.getPropertyValue('--app-two').trim() || '#246bff';
        const three = styles.getPropertyValue('--app-three').trim() || '#22d3ee';

        const count = reduceMotion ? 0 : 72;

        for (let i = 0; i < count; i++) {
            const particle = document.createElement('span');
            particle.className = 'sb-life-particle';

            const size = Math.random() * 6 + 2.5;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 80 - 40).toFixed(1);
            const dy = (Math.random() * 80 - 40).toFixed(1);
            const duration = Math.random() * 8 + 7;
            const delay = Math.random() * -10;
            const opacity = Math.random() * .34 + .24;
            const rotate = Math.random() * 360;

            particle.style.setProperty('--x', `${x}%`);
            particle.style.setProperty('--y', `${y}%`);
            particle.style.setProperty('--s', `${size}px`);
            particle.style.setProperty('--dx', `${dx}px`);
            particle.style.setProperty('--dy', `${dy}px`);
            particle.style.setProperty('--d', `${duration}s`);
            particle.style.setProperty('--delay', `${delay}s`);
            particle.style.setProperty('--o', opacity.toFixed(2));
            particle.style.setProperty('--r', `${rotate}deg`);
            particle.style.setProperty('--app-one', one);
            particle.style.setProperty('--app-two', two);
            particle.style.setProperty('--app-three', three);

            layer.appendChild(particle);
        }

        layer.dataset.ready = 'true';
    }

    // Gentle magnetic movement on cards, not glitchy jump
    document.querySelectorAll('.sb-connected-card, .sb-detail-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            if (reduceMotion) return;

            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - .5;
            const y = (event.clientY - rect.top) / rect.height - .5;

            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);

            if (card.classList.contains('sb-detail-art')) {
                card.style.transform = `translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
            }
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });
})();
// === End StudyBuddy no-glitch magical user polish ===
'''

js_path.write_text(js)

print("✅ More sparkles, more real images, smoother motion, and no user-facing technical stuff.")
