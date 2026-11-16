<?php

$root = dirname(__DIR__);

$cssPath = $root . '/public/assets/css/studybuddy-advanced-shell.css';
$jsPath = $root . '/public/assets/js/studybuddy-advanced-shell.js';

if (!file_exists($cssPath)) {
    echo "ERROR: CSS file not found: public/assets/css/studybuddy-advanced-shell.css\n";
    exit(1);
}

if (!file_exists($jsPath)) {
    echo "ERROR: JS file not found: public/assets/js/studybuddy-advanced-shell.js\n";
    exit(1);
}

copy($cssPath, $cssPath . '.bak_' . date('Ymd_His'));
copy($jsPath, $jsPath . '.bak_' . date('Ymd_His'));

$css = file_get_contents($cssPath);
$js = file_get_contents($jsPath);

$cssBlock = <<<'CSS'

/* === StudyBuddy interactive shell polish === */
.sb-advanced-shell {
    transition: padding .25s ease, transform .25s ease;
}

.sb-advanced-shell.is-scrolled {
    padding-top: 7px;
    padding-bottom: 7px;
}

.sb-advanced-shell.is-scrolled .sb-advanced-nav {
    border-color: rgba(34, 211, 238, .22);
    box-shadow: 0 20px 70px rgba(2, 6, 23, .38);
}

.sb-nav-brand {
    position: relative;
    isolation: isolate;
    transition: transform .22s ease;
}

.sb-nav-brand:hover {
    transform: translateY(-1px) scale(1.01);
}

.sb-nav-brand img,
.sb-footer-brand img {
    transition: transform .28s ease, box-shadow .28s ease, filter .28s ease;
}

.sb-nav-brand:hover img,
.sb-footer-brand:hover img {
    transform: rotate(-4deg) scale(1.06);
    box-shadow: 0 14px 32px rgba(34, 211, 238, .18);
    filter: drop-shadow(0 8px 18px rgba(34, 211, 238, .20));
}

.sb-nav-links > a,
.sb-nav-more > button,
.sb-mobile-links a {
    position: relative;
    overflow: hidden;
}

.sb-nav-links > a::before,
.sb-nav-more > button::before,
.sb-mobile-links a::before {
    content: "";
    position: absolute;
    inset: auto 13px 6px;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--sb-shell-cyan), var(--sb-shell-purple));
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .22s ease;
}

.sb-nav-links > a:hover::before,
.sb-nav-links > a.active::before,
.sb-nav-more > button:hover::before,
.sb-mobile-links a:hover::before,
.sb-mobile-links a.active::before {
    transform: scaleX(1);
}

.sb-nav-links > a::after,
.sb-nav-more > button::after,
.sb-mobile-links a::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.16) 45%, transparent 75%);
    transform: translateX(-120%);
    transition: transform .55s ease;
}

.sb-nav-links > a:hover::after,
.sb-nav-more > button:hover::after,
.sb-mobile-links a:hover::after {
    transform: translateX(120%);
}

.sb-nav-search {
    transition: transform .22s ease, border-color .22s ease, background .22s ease, box-shadow .22s ease;
}

.sb-nav-search:focus-within,
.sb-nav-search:hover {
    transform: translateY(-1px);
    border-color: rgba(34, 211, 238, .36);
    background: rgba(255,255,255,.12);
    box-shadow: 0 12px 28px rgba(34, 211, 238, .10);
}

.sb-nav-search button,
.sb-footer-newsletter button,
.sb-footer-action-card a,
.sb-nav-actions a,
.sb-mobile-actions a {
    position: relative;
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
}

.sb-nav-search button:hover,
.sb-footer-newsletter button:hover,
.sb-footer-action-card a:hover,
.sb-nav-actions a:hover,
.sb-mobile-actions a:hover {
    transform: translateY(-2px);
    filter: brightness(1.08);
    box-shadow: 0 16px 36px rgba(34, 211, 238, .20);
}

.sb-ripple {
    position: absolute;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    pointer-events: none;
    background: rgba(255,255,255,.45);
    transform: translate(-50%, -50%) scale(1);
    animation: sbRipple .55s ease-out forwards;
}

@keyframes sbRipple {
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(16);
    }
}

.sb-nav-more-menu {
    transform-origin: top right;
}

.sb-nav-more.open .sb-nav-more-menu {
    animation: sbMenuPop .18s ease-out both;
}

@keyframes sbMenuPop {
    from {
        opacity: 0;
        transform: translateY(8px) scale(.96);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.sb-footer-action-card,
.sb-footer-value-strip article,
.sb-footer-group {
    position: relative;
    isolation: isolate;
    transition: transform .24s ease, border-color .24s ease, background .24s ease, box-shadow .24s ease;
}

.sb-footer-action-card::before,
.sb-footer-value-strip article::before,
.sb-footer-group::before {
    content: "";
    position: absolute;
    inset: -1px;
    border-radius: inherit;
    z-index: -1;
    background:
        radial-gradient(
            260px circle at var(--mx, 50%) var(--my, 50%),
            rgba(34, 211, 238, .20),
            transparent 42%
        );
    opacity: 0;
    transition: opacity .24s ease;
}

.sb-footer-action-card:hover,
.sb-footer-value-strip article:hover,
.sb-footer-group:hover {
    transform: translateY(-5px);
    border-color: rgba(34, 211, 238, .26);
    background: rgba(255,255,255,.085);
    box-shadow: 0 22px 54px rgba(2, 6, 23, .22);
}

.sb-footer-action-card:hover::before,
.sb-footer-value-strip article:hover::before,
.sb-footer-group:hover::before {
    opacity: 1;
}

.sb-footer-group a {
    transition: background .18s ease, color .18s ease, transform .18s ease;
}

.sb-footer-group a:hover {
    transform: translateX(3px);
}

.sb-footer-value-strip article strong {
    position: relative;
    display: inline-block;
}

.sb-footer-value-strip article strong::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -5px;
    height: 2px;
    border-radius: 999px;
    background: linear-gradient(90deg, var(--sb-shell-cyan), transparent);
    transform: scaleX(.35);
    transform-origin: left;
    transition: transform .2s ease;
}

.sb-footer-value-strip article:hover strong::after {
    transform: scaleX(1);
}

.sb-footer-newsletter input {
    transition: background .18s ease, box-shadow .18s ease;
}

.sb-footer-newsletter form:focus-within {
    box-shadow: 0 0 0 3px rgba(34, 211, 238, .16);
}

.sb-footer-bottom a {
    position: relative;
}

.sb-footer-bottom a::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: -3px;
    height: 2px;
    border-radius: 999px;
    background: var(--sb-shell-cyan);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform .18s ease;
}

.sb-footer-bottom a:hover::after {
    transform: scaleX(1);
}

@media (hover: none) {
    .sb-footer-action-card:hover,
    .sb-footer-value-strip article:hover,
    .sb-footer-group:hover,
    .sb-nav-brand:hover,
    .sb-nav-search button:hover,
    .sb-footer-newsletter button:hover,
    .sb-footer-action-card a:hover,
    .sb-nav-actions a:hover,
    .sb-mobile-actions a:hover {
        transform: none;
    }
}

@media (prefers-reduced-motion: reduce) {
    .sb-ripple {
        display: none;
    }

    .sb-nav-more.open .sb-nav-more-menu {
        animation: none;
    }
}
/* === End StudyBuddy interactive shell polish === */
CSS;

$jsBlock = <<<'JS'

// === StudyBuddy interactive shell polish ===
(() => {
    const shell = document.querySelector('.sb-advanced-shell');
    const nav = document.querySelector('.sb-advanced-nav');

    const setScrolled = () => {
        if (!shell) return;
        shell.classList.toggle('is-scrolled', window.scrollY > 18);
    };

    setScrolled();
    window.addEventListener('scroll', setScrolled, { passive: true });

    document.querySelectorAll('.sb-footer-action-card, .sb-footer-value-strip article, .sb-footer-group').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    document.querySelectorAll('.sb-nav-search button, .sb-footer-newsletter button, .sb-footer-action-card a, .sb-nav-actions a, .sb-mobile-actions a').forEach((button) => {
        button.addEventListener('click', (event) => {
            const oldRipple = button.querySelector('.sb-ripple');
            if (oldRipple) oldRipple.remove();

            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'sb-ripple';
            ripple.style.left = `${event.clientX - rect.left}px`;
            ripple.style.top = `${event.clientY - rect.top}px`;

            button.appendChild(ripple);
            setTimeout(() => ripple.remove(), 650);
        });
    });

    if (nav) {
        nav.addEventListener('pointermove', (event) => {
            const rect = nav.getBoundingClientRect();
            nav.style.setProperty('--nav-x', `${event.clientX - rect.left}px`);
            nav.style.setProperty('--nav-y', `${event.clientY - rect.top}px`);
        });
    }
})();
// === End StudyBuddy interactive shell polish ===
JS;

$css = preg_replace('/\/\* === StudyBuddy interactive shell polish === \*\/.*?\/\* === End StudyBuddy interactive shell polish === \*\//s', '', $css);
$js = preg_replace('/\/\/ === StudyBuddy interactive shell polish ===.*?\/\/ === End StudyBuddy interactive shell polish ===/s', '', $js);

file_put_contents($cssPath, trim($css) . "\n\n" . $cssBlock . "\n");
file_put_contents($jsPath, trim($js) . "\n\n" . $jsBlock . "\n");

echo "DONE ✅ Added interactive hover effects, ripples, spotlight cards, and scroll polish.\n";
