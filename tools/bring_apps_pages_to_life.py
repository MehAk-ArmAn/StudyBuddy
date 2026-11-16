from pathlib import Path
from datetime import datetime
import re

css_path = Path("public/assets/css/studybuddy-connected-apps.css")
js_path = Path("public/assets/js/studybuddy-connected-apps.js")

if not css_path.exists():
    raise SystemExit("❌ Missing public/assets/css/studybuddy-connected-apps.css")

if not js_path.exists():
    raise SystemExit("❌ Missing public/assets/js/studybuddy-connected-apps.js")

stamp = datetime.now().strftime("%Y%m%d_%H%M%S")
css_backup = css_path.with_suffix(css_path.suffix + f".bak_{stamp}")
js_backup = js_path.with_suffix(js_path.suffix + f".bak_{stamp}")

css_backup.write_text(css_path.read_text())
js_backup.write_text(js_path.read_text())

css = css_path.read_text()
js = js_path.read_text()

css = re.sub(
    r"/\* === StudyBuddy app pages alive polish === \*/.*?/\* === End StudyBuddy app pages alive polish === \*/",
    "",
    css,
    flags=re.S,
)

js = re.sub(
    r"// === StudyBuddy app pages alive polish ===.*?// === End StudyBuddy app pages alive polish ===",
    "",
    js,
    flags=re.S,
)

life_css = r'''
/* === StudyBuddy app pages alive polish === */

/* Hide glitchy decorative image files, keep the real app logos/artwork */
.sb-detail-decor,
.sb-detail-art .float {
    display: none !important;
}

/* Generated magic layer */
.sb-life-layer {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
    overflow: hidden;
    opacity: .9;
}

.sb-connected-apps > *,
.sb-app-detail-world > * {
    position: relative;
    z-index: 1;
}

.sb-life-particle {
    position: absolute;
    left: var(--x);
    top: var(--y);
    width: var(--s);
    height: var(--s);
    border-radius: 999px;
    background:
        radial-gradient(circle, rgba(255,255,255,.95) 0 18%, var(--app-three, #22d3ee) 22% 42%, transparent 70%);
    box-shadow:
        0 0 16px color-mix(in srgb, var(--app-three, #22d3ee) 60%, transparent),
        0 0 28px color-mix(in srgb, var(--app-one, #7c3cff) 35%, transparent);
    animation:
        sbLifeFloat var(--d) ease-in-out infinite,
        sbLifeTwinkle calc(var(--d) * .72) ease-in-out infinite;
    animation-delay: var(--delay);
    opacity: var(--o);
}

.sb-life-particle:nth-child(3n) {
    border-radius: 40% 60% 55% 45%;
    background:
        radial-gradient(circle at 30% 30%, white, var(--app-one, #7c3cff) 32%, transparent 68%);
}

.sb-life-particle:nth-child(4n) {
    width: calc(var(--s) * 1.8);
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, transparent, var(--app-three, #22d3ee), white, transparent);
    box-shadow: 0 0 18px color-mix(in srgb, var(--app-three, #22d3ee) 60%, transparent);
    animation:
        sbLifeComet var(--d) ease-in-out infinite,
        sbLifeTwinkle calc(var(--d) * .9) ease-in-out infinite;
}

/* Living hero glow */
.sb-apps-landing::after,
.sb-detail-hero::after {
    content: "";
    position: absolute;
    inset: 36px;
    z-index: 0;
    border-radius: 42px;
    background:
        radial-gradient(circle at var(--hero-x, 22%) var(--hero-y, 20%), color-mix(in srgb, var(--app-three, #22d3ee) 20%, transparent), transparent 28%),
        radial-gradient(circle at 80% 20%, color-mix(in srgb, var(--app-one, #7c3cff) 16%, transparent), transparent 30%);
    filter: blur(2px);
    opacity: .85;
    pointer-events: none;
    animation: sbHeroBreath 7s ease-in-out infinite;
}

/* Make cards feel alive */
.sb-connected-card,
.sb-detail-outcomes article,
.sb-detail-missions article,
.sb-related-grid a,
.sb-apps-count-card,
.sb-detail-art {
    isolation: isolate;
}

.sb-connected-card::before,
.sb-detail-outcomes article::before,
.sb-detail-missions article::before,
.sb-related-grid a::before,
.sb-apps-count-card::before,
.sb-detail-art::before {
    content: "";
    position: absolute;
    inset: -1px;
    z-index: -1;
    border-radius: inherit;
    background:
        radial-gradient(
            280px circle at var(--mx, 50%) var(--my, 0%),
            color-mix(in srgb, var(--app-three, #22d3ee) 22%, transparent),
            transparent 44%
        );
    opacity: 0;
    transition: opacity .22s ease;
}

.sb-connected-card:hover::before,
.sb-detail-outcomes article:hover::before,
.sb-detail-missions article:hover::before,
.sb-related-grid a:hover::before,
.sb-apps-count-card:hover::before,
.sb-detail-art:hover::before {
    opacity: 1;
}

.sb-connected-media {
    overflow: hidden;
}

.sb-connected-media::before {
    content: "";
    position: absolute;
    inset: 18px;
    border-radius: 999px;
    background:
        conic-gradient(
            from var(--spin, 0deg),
            transparent,
            color-mix(in srgb, var(--app-three) 32%, transparent),
            transparent,
            color-mix(in srgb, var(--app-one) 22%, transparent),
            transparent
        );
    filter: blur(4px);
    opacity: .55;
    animation: sbSpinRing 9s linear infinite;
}

.sb-connected-media img,
.sb-detail-art .main-art {
    animation: sbLogoHover 5.2s ease-in-out infinite;
}

/* Friendly moving scan line, not glitch */
.sb-connected-card-body::after {
    content: "";
    display: block;
    height: 1px;
    margin-top: 18px;
    background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--app-three) 70%, white), transparent);
    opacity: .34;
    transform-origin: left;
    animation: sbSoftLine 3.4s ease-in-out infinite;
}

.sb-connected-topline span::before {
    content: "●";
    margin-right: 7px;
    color: var(--app-three);
    filter: drop-shadow(0 0 8px var(--app-three));
    animation: sbPulseDot 1.8s ease-in-out infinite;
}

/* Detail page generated constellations */
.sb-detail-art::after {
    content: "";
    position: absolute;
    inset: 18px;
    border-radius: inherit;
    background-image:
        radial-gradient(circle at 18% 22%, white 0 1px, transparent 2px),
        radial-gradient(circle at 72% 26%, var(--app-three) 0 1.5px, transparent 3px),
        radial-gradient(circle at 46% 74%, white 0 1px, transparent 2px),
        radial-gradient(circle at 82% 70%, var(--app-one) 0 1.5px, transparent 3px),
        linear-gradient(120deg, transparent 18%, rgba(255,255,255,.12), transparent 26%);
    opacity: .52;
    animation: sbConstellation 6s ease-in-out infinite;
    pointer-events: none;
}

/* App-specific vibes using each card’s own theme vars */
.sb-connected-card:hover .sb-connected-topline span,
.sb-connected-card:hover h2 {
    text-shadow:
        0 0 18px color-mix(in srgb, var(--app-three) 52%, transparent),
        0 0 30px color-mix(in srgb, var(--app-one) 28%, transparent);
}

.sb-role-chips span,
.sb-connected-meta span,
.sb-world-meta span {
    transition: transform .18s ease, border-color .18s ease, background .18s ease;
}

.sb-role-chips span:hover,
.sb-connected-meta span:hover,
.sb-world-meta span:hover {
    transform: translateY(-2px);
    border-color: color-mix(in srgb, var(--app-three, #22d3ee) 36%, white);
    background: rgba(255,255,255,.12);
}

.sb-connected-links a,
.sb-apps-actions a,
.sb-back-link {
    position: relative;
    overflow: hidden;
}

.sb-connected-links a::after,
.sb-apps-actions a::after,
.sb-back-link::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(115deg, transparent 0%, rgba(255,255,255,.28) 45%, transparent 75%);
    transform: translateX(-120%);
    transition: transform .55s ease;
}

.sb-connected-links a:hover::after,
.sb-apps-actions a:hover::after,
.sb-back-link:hover::after {
    transform: translateX(120%);
}

@keyframes sbLifeFloat {
    0%, 100% {
        transform: translate3d(0, 0, 0) scale(1);
    }
    50% {
        transform: translate3d(var(--dx), var(--dy), 0) scale(1.28);
    }
}

@keyframes sbLifeTwinkle {
    0%, 100% { opacity: calc(var(--o) * .5); }
    45% { opacity: var(--o); }
    70% { opacity: calc(var(--o) * .72); }
}

@keyframes sbLifeComet {
    0%, 100% {
        transform: translate3d(0, 0, 0) rotate(var(--r));
    }
    50% {
        transform: translate3d(var(--dx), var(--dy), 0) rotate(var(--r)) scaleX(1.35);
    }
}

@keyframes sbHeroBreath {
    0%, 100% {
        transform: scale(1);
        opacity: .72;
    }
    50% {
        transform: scale(1.025);
        opacity: .96;
    }
}

@keyframes sbSpinRing {
    to {
        --spin: 360deg;
        transform: rotate(360deg);
    }
}

@keyframes sbLogoHover {
    0%, 100% {
        transform: translateY(0) scale(1);
    }
    50% {
        transform: translateY(-8px) scale(1.025);
    }
}

@keyframes sbSoftLine {
    0%, 100% {
        transform: scaleX(.28);
        opacity: .24;
    }
    50% {
        transform: scaleX(1);
        opacity: .6;
    }
}

@keyframes sbPulseDot {
    0%, 100% {
        opacity: .55;
        transform: scale(.86);
    }
    50% {
        opacity: 1;
        transform: scale(1.08);
    }
}

@keyframes sbConstellation {
    0%, 100% {
        transform: translateY(0);
        opacity: .42;
    }
    50% {
        transform: translateY(-8px);
        opacity: .78;
    }
}

@media (max-width: 640px) {
    .sb-life-layer {
        opacity: .48;
    }

    .sb-life-particle:nth-child(n + 26) {
        display: none;
    }

    .sb-apps-landing::after,
    .sb-detail-hero::after {
        inset: 14px;
        border-radius: 28px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sb-life-layer,
    .sb-life-particle,
    .sb-connected-media::before,
    .sb-connected-media img,
    .sb-detail-art .main-art,
    .sb-detail-art::after,
    .sb-connected-card-body::after,
    .sb-connected-topline span::before,
    .sb-apps-landing::after,
    .sb-detail-hero::after {
        animation: none !important;
    }

    .sb-life-layer {
        opacity: .18;
    }

    .sb-connected-media img,
    .sb-detail-art .main-art {
        transform: none !important;
    }
}
/* === End StudyBuddy app pages alive polish === */
'''

life_js = r'''
// === StudyBuddy app pages alive polish ===
(() => {
    const root = document.querySelector('.sb-connected-apps, .sb-app-detail-world');
    if (!root) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const readTheme = () => {
        const styles = getComputedStyle(root);
        return {
            one: styles.getPropertyValue('--app-one').trim() || '#7c3cff',
            two: styles.getPropertyValue('--app-two').trim() || '#246bff',
            three: styles.getPropertyValue('--app-three').trim() || '#22d3ee'
        };
    };

    const theme = readTheme();

    if (!document.querySelector('.sb-life-layer')) {
        const layer = document.createElement('div');
        layer.className = 'sb-life-layer';
        layer.setAttribute('aria-hidden', 'true');

        const count = reduceMotion ? 14 : 52;

        for (let i = 0; i < count; i++) {
            const particle = document.createElement('span');
            particle.className = 'sb-life-particle';

            const size = Math.random() * 7 + 3;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 70 - 35).toFixed(1);
            const dy = (Math.random() * 70 - 35).toFixed(1);
            const duration = Math.random() * 7 + 6;
            const delay = Math.random() * -8;
            const opacity = Math.random() * .38 + .28;
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
            particle.style.setProperty('--app-one', theme.one);
            particle.style.setProperty('--app-two', theme.two);
            particle.style.setProperty('--app-three', theme.three);

            layer.appendChild(particle);
        }

        document.body.prepend(layer);
    }

    document.querySelectorAll('.sb-apps-landing, .sb-detail-hero').forEach((hero) => {
        hero.addEventListener('pointermove', (event) => {
            const rect = hero.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width * 100).toFixed(1);
            const y = ((event.clientY - rect.top) / rect.height * 100).toFixed(1);
            hero.style.setProperty('--hero-x', `${x}%`);
            hero.style.setProperty('--hero-y', `${y}%`);
        });
    });

    document.querySelectorAll('.sb-connected-card, .sb-detail-outcomes article, .sb-detail-missions article, .sb-related-grid a, .sb-apps-count-card, .sb-detail-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });
})();
// === End StudyBuddy app pages alive polish ===
'''

css_path.write_text(css.strip() + "\n\n" + life_css.strip() + "\n")
js_path.write_text(js.strip() + "\n\n" + life_js.strip() + "\n")

print("✅ Apps pages brought to life without decorative sparkle images.")
print(f"CSS backup: {css_backup}")
print(f"JS backup: {js_backup}")
