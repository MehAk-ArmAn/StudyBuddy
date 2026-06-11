(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const ready = (callback) => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
        } else {
            callback();
        }
    };

    ready(() => {
        const page = document.querySelector('[data-sb-page]');
        const loader = document.querySelector('[data-sb-page-loader]');
        const menuToggle = document.querySelector('[data-sb-menu-toggle]');
        const menu = document.querySelector('[data-sb-menu]');
        const navLinks = Array.from(document.querySelectorAll('[data-sb-menu] a'));
        const revealItems = Array.from(document.querySelectorAll('.sb-reveal'));
        const sparkleItems = Array.from(document.querySelectorAll('.sb-card, .sb-button, [data-sb-sparkle]'));
        const parallaxItems = Array.from(document.querySelectorAll('.sb-floating-asset'));

        requestAnimationFrame(() => {
            page?.classList.add('is-ready');
            window.setTimeout(() => loader?.classList.add('is-hidden'), reducedMotion ? 0 : 280);
        });

        if (menuToggle && menu) {
            const closeMenu = () => {
                menuToggle.setAttribute('aria-expanded', 'false');
                menu.classList.remove('is-open');
                document.body.classList.remove('sb-menu-open');
            };

            menuToggle.addEventListener('click', () => {
                const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
                menuToggle.setAttribute('aria-expanded', String(!isOpen));
                menu.classList.toggle('is-open', !isOpen);
                document.body.classList.toggle('sb-menu-open', !isOpen);
            });

            navLinks.forEach((link) => link.addEventListener('click', closeMenu));
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenu();
            });
        }

        const currentUrl = new URL(window.location.href);
        navLinks.forEach((link) => {
            const linkUrl = new URL(link.href, window.location.origin);
            const currentPath = currentUrl.pathname.replace(/\/$/, '') || '/';
            const linkPath = linkUrl.pathname.replace(/\/$/, '') || '/';

            if (currentPath === linkPath) {
                link.classList.add('is-active');
                link.setAttribute('aria-current', 'page');
            }
        });

        if (revealItems.length > 0) {
            if (!reducedMotion && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                }, { threshold: 0.16, rootMargin: '0px 0px -8% 0px' });

                revealItems.forEach((item) => observer.observe(item));
            } else {
                revealItems.forEach((item) => item.classList.add('is-visible'));
            }
        }

        sparkleItems.forEach((item) => {
            item.addEventListener('pointerenter', () => item.classList.add('is-sparkling'));
            item.addEventListener('pointerleave', () => item.classList.remove('is-sparkling'));
            item.addEventListener('pointermove', (event) => {
                const rect = item.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 100;
                const y = ((event.clientY - rect.top) / rect.height) * 100;
                item.style.setProperty('--sb-card-x', `${x}%`);
                item.style.setProperty('--sb-card-y', `${y}%`);
            });
        });

        if (!reducedMotion && parallaxItems.length > 0) {
            let pointerX = 0;
            let pointerY = 0;
            let ticking = false;

            const applyParallax = () => {
                parallaxItems.forEach((item, index) => {
                    const depth = (index % 5) + 1;
                    item.style.setProperty('--sb-parallax-x', `${pointerX * depth}px`);
                    item.style.setProperty('--sb-parallax-y', `${pointerY * depth}px`);
                    item.style.translate = `var(--sb-parallax-x) var(--sb-parallax-y)`;
                });
                ticking = false;
            };

            window.addEventListener('pointermove', (event) => {
                pointerX = ((event.clientX / window.innerWidth) - 0.5) * 5;
                pointerY = ((event.clientY / window.innerHeight) - 0.5) * 5;

                if (!ticking) {
                    window.requestAnimationFrame(applyParallax);
                    ticking = true;
                }
            }, { passive: true });
        }
    });
})();
