from pathlib import Path
from datetime import datetime
import re

css_path = Path("public/assets/css/studybuddy-connected-apps.css")

if not css_path.exists():
    raise SystemExit("❌ Missing public/assets/css/studybuddy-connected-apps.css")

stamp = datetime.now().strftime("%Y%m%d_%H%M%S")
backup = css_path.with_suffix(css_path.suffix + f".bak_{stamp}")
css = css_path.read_text()
backup.write_text(css)

css = re.sub(
    r"/\* === StudyBuddy premium apps layout refinement === \*/.*?/\* === End StudyBuddy premium apps layout refinement === \*/",
    "",
    css,
    flags=re.S,
)

refine = r'''
/* === StudyBuddy premium apps layout refinement === */

/* Page rhythm */
.sb-connected-apps,
.sb-app-detail-world {
    padding-bottom: clamp(46px, 7vw, 90px);
}

.sb-apps-landing,
.sb-detail-hero {
    width: min(100% - 24px, 1220px);
    padding-inline: clamp(18px, 4vw, 34px);
    margin-top: clamp(12px, 2vw, 22px);
}

.sb-apps-toolbar,
.sb-apps-note,
.sb-connected-grid,
.sb-detail-strip,
.sb-detail-section,
.sb-empty-apps {
    width: min(100% - 24px, 1180px);
    padding-inline: 0;
}

/* Prevent decorative layer from visually fighting the layout */
.sb-life-layer {
    z-index: 0 !important;
    opacity: .42;
}

.sb-connected-apps > *,
.sb-app-detail-world > * {
    position: relative;
    z-index: 2;
}

/* Hero: premium, contained, balanced */
.sb-apps-landing,
.sb-detail-hero {
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: clamp(30px, 4vw, 46px);
    background:
        radial-gradient(circle at 18% 10%, color-mix(in srgb, var(--app-one, #7c3cff) 16%, transparent), transparent 34%),
        radial-gradient(circle at 84% 16%, rgba(34, 211, 238, .10), transparent 32%),
        rgba(255,255,255,.035);
    box-shadow: 0 30px 90px rgba(2, 6, 23, .28);
    overflow: clip;
}

.sb-apps-landing::before,
.sb-detail-hero::before {
    inset: 0;
    border: 0;
    border-radius: inherit;
    background:
        linear-gradient(135deg, rgba(255,255,255,.055), transparent 42%),
        radial-gradient(circle at var(--hero-x, 25%) var(--hero-y, 20%), color-mix(in srgb, var(--app-three, #22d3ee) 12%, transparent), transparent 34%);
}

.sb-apps-landing::after,
.sb-detail-hero::after {
    inset: 20px;
    opacity: .45;
}

.sb-apps-landing h1,
.sb-detail-copy h1 {
    max-width: 760px;
    text-wrap: balance;
}

.sb-apps-landing p,
.sb-detail-copy p {
    max-width: 690px;
}

/* Count card alignment */
.sb-apps-count-card {
    align-self: stretch;
    min-height: 260px;
    display: grid;
    place-items: center;
    align-content: center;
    gap: 8px;
    overflow: hidden;
}

.sb-apps-count-card strong,
.sb-apps-count-card p {
    margin: 0;
}

/* Toolbar: cleaner, proper margins, no trapped fields */
.sb-apps-toolbar {
    margin-top: clamp(22px, 4vw, 42px);
    margin-bottom: 16px;
    align-items: end;
}

.sb-apps-toolbar input,
.sb-apps-toolbar select {
    width: 100%;
    height: 54px;
    appearance: auto;
}

.sb-apps-note {
    margin-top: 0;
    margin-bottom: clamp(20px, 3vw, 30px);
    padding: 16px 18px;
}

/* Grid: consistent premium card layout */
.sb-connected-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: clamp(18px, 2.4vw, 26px);
    align-items: stretch;
}

.sb-connected-card {
    display: flex;
    flex-direction: column;
    min-height: 100%;
    overflow: clip;
    border-radius: 34px;
    contain: layout paint;
}

.sb-connected-media {
    min-height: 238px;
    aspect-ratio: 16 / 10;
    padding: clamp(22px, 3vw, 30px);
    overflow: hidden;
}

.sb-connected-media img {
    width: min(78%, 220px);
    height: 200px;
    max-height: 76%;
    object-fit: contain;
}

.sb-connected-card-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 22px;
}

.sb-connected-topline {
    min-height: 32px;
    gap: 8px;
}

.sb-connected-card h2 {
    min-height: 1.95em;
    margin-top: 16px;
    margin-bottom: 8px;
    line-height: .98;
    text-wrap: balance;
}

.sb-connected-card .tagline {
    min-height: 2.6em;
    line-height: 1.3;
}

.sb-connected-card p:not(.tagline) {
    min-height: 5.1em;
    margin-bottom: 0;
}

.sb-role-chips {
    min-height: 34px;
    margin-top: 14px;
}

.sb-connected-meta {
    min-height: 34px;
    margin-top: 14px;
}

.sb-connected-links {
    margin-top: auto;
    padding-top: 18px;
}

.sb-connected-links a,
.sb-connected-links span {
    min-width: 0;
    flex: 1 1 135px;
}

/* Keep hover premium, not jumpy/glitchy */
.sb-connected-card:hover {
    transform: translateY(-5px);
}

.sb-connected-card:hover .sb-connected-media img {
    transform: translateY(-4px) scale(1.025);
}

.sb-connected-media::before {
    opacity: .34;
}

.sb-connected-card-body::after {
    margin-top: 16px;
}

/* Empty states and hidden cards */
.sb-empty-apps {
    padding: 22px;
    text-align: center;
}

.sb-connected-card[hidden] {
    display: none !important;
}

/* Detail pages: better spacing and no overcrowding */
.sb-detail-hero {
    width: min(100% - 24px, 1220px);
}

.sb-detail-art {
    min-height: clamp(360px, 40vw, 510px);
    overflow: clip;
    contain: layout paint;
}

.sb-detail-art .main-art {
    width: min(82%, 410px);
    height: clamp(250px, 32vw, 390px);
}

.sb-detail-strip {
    margin-top: clamp(22px, 3vw, 36px);
    margin-bottom: clamp(22px, 3vw, 34px);
    gap: clamp(14px, 2vw, 22px);
}

.sb-detail-strip article {
    min-height: 116px;
}

.sb-detail-section {
    margin-bottom: clamp(20px, 3vw, 30px);
    overflow: clip;
}

.sb-detail-section.split {
    align-items: stretch;
}

.sb-detail-section.split > div:first-child {
    min-width: 0;
}

.sb-detail-section h2 {
    text-wrap: balance;
}

.sb-detail-outcomes,
.sb-detail-missions,
.sb-related-grid {
    gap: clamp(14px, 2vw, 22px);
}

.sb-detail-outcomes article,
.sb-detail-missions article,
.sb-related-grid a {
    overflow: clip;
}

.sb-detail-missions article {
    min-height: 220px;
}

.sb-related-grid a {
    display: flex;
    flex-direction: column;
    min-height: 270px;
}

.sb-related-grid img {
    height: 150px;
    margin-bottom: 10px;
}

/* Text and overflow safety */
.sb-connected-card,
.sb-detail-section,
.sb-detail-strip article,
.sb-apps-note,
.sb-empty-apps,
.sb-apps-count-card {
    min-width: 0;
}

.sb-connected-card h2,
.sb-connected-card p,
.sb-connected-card span,
.sb-connected-card strong,
.sb-detail-section h2,
.sb-detail-section h3,
.sb-detail-section p,
.sb-detail-strip strong,
.sb-related-grid strong,
.sb-related-grid span {
    overflow-wrap: anywhere;
}

.sb-connected-card img,
.sb-detail-art img,
.sb-related-grid img {
    max-width: 100%;
}

/* Better keyboard focus */
.sb-connected-card a:focus-visible,
.sb-apps-actions a:focus-visible,
.sb-back-link:focus-visible,
.sb-apps-toolbar input:focus-visible,
.sb-apps-toolbar select:focus-visible,
.sb-related-grid a:focus-visible {
    outline: 3px solid color-mix(in srgb, var(--app-three, #22d3ee) 70%, white);
    outline-offset: 4px;
}

/* Responsive polish */
@media (max-width: 1120px) {
    .sb-connected-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .sb-apps-landing,
    .sb-detail-hero {
        grid-template-columns: minmax(0, 1fr);
    }

    .sb-apps-count-card,
    .sb-detail-art {
        max-width: 640px;
        width: 100%;
        justify-self: center;
    }
}

@media (max-width: 760px) {
    .sb-apps-landing,
    .sb-detail-hero {
        width: min(100% - 16px, 1220px);
        border-radius: 28px;
        padding-top: 34px;
        padding-bottom: 34px;
    }

    .sb-apps-toolbar,
    .sb-apps-note,
    .sb-connected-grid,
    .sb-detail-strip,
    .sb-detail-section,
    .sb-empty-apps {
        width: min(100% - 16px, 1180px);
    }

    .sb-connected-grid,
    .sb-detail-strip,
    .sb-detail-outcomes,
    .sb-detail-missions,
    .sb-related-grid {
        grid-template-columns: 1fr;
    }

    .sb-connected-media {
        min-height: 220px;
    }

    .sb-connected-card h2,
    .sb-connected-card .tagline,
    .sb-connected-card p:not(.tagline) {
        min-height: 0;
    }

    .sb-connected-links {
        display: grid;
    }

    .sb-apps-actions {
        display: grid;
    }

    .sb-apps-actions a,
    .sb-connected-links a,
    .sb-connected-links span {
        width: 100%;
    }

    .sb-detail-section.split {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 420px) {
    .sb-connected-card-body {
        padding: 18px;
    }

    .sb-connected-media {
        min-height: 190px;
    }

    .sb-connected-media img {
        height: 170px;
    }

    .sb-detail-art {
        min-height: 300px;
    }

    .sb-detail-art .main-art {
        height: 230px;
    }
}

/* Motion calm mode */
@media (prefers-reduced-motion: reduce) {
    .sb-connected-card:hover,
    .sb-connected-card:hover .sb-connected-media img {
        transform: none !important;
    }
}
/* === End StudyBuddy premium apps layout refinement === */
'''

css_path.write_text(css.strip() + "\n\n" + refine.strip() + "\n")

print("✅ Premium apps layout refinement applied.")
print(f"Backup saved: {backup}")
