(() => {
    const root = document.querySelector('[data-home-page]');

    if (!root) {
        return;
    }

    const reduceMotion = window
        .matchMedia('(prefers-reduced-motion: reduce)')
        .matches;

    const finePointer = window
        .matchMedia('(pointer: fine)')
        .matches;

    const greeting = root.querySelector('[data-home-greeting]');
    const dateLine = root.querySelector('[data-home-date]');

    if (greeting) {
        const hour = new Date().getHours();
        const fullName = (greeting.dataset.user || '').trim();
        const firstName = fullName.split(/\s+/)[0];

        let period = 'Good evening';

        if (hour < 12) {
            period = 'Good morning';
        } else if (hour < 18) {
            period = 'Good afternoon';
        }

        greeting.textContent = firstName
            ? `${period}, ${firstName}. Ready for one useful step?`
            : `${period}. Ready for one useful step?`;
    }

    if (dateLine) {
        const date = new Intl.DateTimeFormat(undefined, {
            weekday: 'long',
            month: 'long',
            day: 'numeric',
        }).format(new Date());

        dateLine.textContent =
            `${date} — choose a starting point that matches what you need.`;
    }

    const roleButton = root.querySelector('[data-scroll-role]');
    const roleSection = root.querySelector('#choose-role');

    roleButton?.addEventListener('click', () => {
        roleSection?.scrollIntoView({
            behavior: reduceMotion ? 'auto' : 'smooth',
            block: 'start',
        });

        window.setTimeout(() => {
            roleSection
                ?.querySelector('[data-switch-tab]')
                ?.focus();
        }, reduceMotion ? 0 : 450);
    });

    root.querySelectorAll('[data-home-switcher]').forEach((switcher) => {
        const tabs = Array.from(
            switcher.querySelectorAll('[data-switch-tab]')
        );

        const label = switcher.querySelector('[data-switch-label]');
        const title = switcher.querySelector('[data-switch-title]');
        const text = switcher.querySelector('[data-switch-text]');
        const image = switcher.querySelector('[data-switch-image]');
        const link = switcher.querySelector('[data-switch-link]');
        const panel =
            switcher.querySelector('.sb-home-switch-panel')
            || switcher;

        const activate = (tab, focus = false) => {
            if (!tab) {
                return;
            }

            tabs.forEach((item) => {
                const active = item === tab;

                item.classList.toggle('is-active', active);
                item.setAttribute(
                    'aria-selected',
                    active ? 'true' : 'false'
                );
                item.tabIndex = active ? 0 : -1;
            });

            if (focus) {
                tab.focus();
            }

            panel.classList.add('is-changing');

            window.setTimeout(() => {
                if (label) {
                    label.textContent = tab.dataset.label || '';
                }

                if (title) {
                    title.textContent = tab.dataset.title || '';
                }

                if (text) {
                    text.textContent = tab.dataset.text || '';
                }

                if (image && tab.dataset.image) {
                    image.src = tab.dataset.image;
                    image.alt = tab.dataset.title || '';
                }

                if (link) {
                    link.href = tab.dataset.link || '#';
                    link.textContent =
                        tab.dataset.linkText || 'Open';
                }

                panel.classList.remove('is-changing');
            }, reduceMotion ? 0 : 130);
        };

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                activate(tab);
            });

            tab.addEventListener('keydown', (event) => {
                const keys = [
                    'ArrowRight',
                    'ArrowLeft',
                    'ArrowDown',
                    'ArrowUp',
                    'Home',
                    'End',
                ];

                if (!keys.includes(event.key)) {
                    return;
                }

                event.preventDefault();

                let nextIndex = index;

                if (event.key === 'Home') {
                    nextIndex = 0;
                } else if (event.key === 'End') {
                    nextIndex = tabs.length - 1;
                } else if (
                    event.key === 'ArrowRight'
                    || event.key === 'ArrowDown'
                ) {
                    nextIndex = (index + 1) % tabs.length;
                } else {
                    nextIndex =
                        (index - 1 + tabs.length) % tabs.length;
                }

                activate(tabs[nextIndex], true);
            });
        });
    });

    const appTrack = root.querySelector('[data-app-track]');
    const previous = root.querySelector('[data-app-prev]');
    const next = root.querySelector('[data-app-next]');

    const moveApps = (direction) => {
        if (!appTrack) {
            return;
        }

        const card = appTrack.querySelector('.sb-home-app');

        const distance = card
            ? card.getBoundingClientRect().width + 16
            : appTrack.clientWidth * .8;

        appTrack.scrollBy({
            left: direction * distance,
            behavior: reduceMotion ? 'auto' : 'smooth',
        });
    };

    previous?.addEventListener('click', () => moveApps(-1));
    next?.addEventListener('click', () => moveApps(1));

    const reveals = Array.from(
        root.querySelectorAll('[data-home-reveal]')
    );

    if (
        reduceMotion
        || !('IntersectionObserver' in window)
    ) {
        reveals.forEach((element) => {
            element.classList.add('is-visible');
        });
    } else {
        const observer = new IntersectionObserver(
            (entries, activeObserver) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');
                    activeObserver.unobserve(entry.target);
                });
            },
            {
                threshold: .12,
                rootMargin: '0px 0px -7% 0px',
            }
        );

        reveals.forEach((element) => {
            observer.observe(element);
        });
    }

    if (!reduceMotion && finePointer) {
        root.querySelectorAll('[data-home-lift]').forEach((card) => {
            card.addEventListener('pointermove', (event) => {
                const rectangle = card.getBoundingClientRect();

                const x =
                    (event.clientX - rectangle.left)
                    / rectangle.width
                    - .5;

                const y =
                    (event.clientY - rectangle.top)
                    / rectangle.height
                    - .5;

                card.style.transform =
                    `perspective(900px) `
                    + `rotateX(${(-y * 2.4).toFixed(2)}deg) `
                    + `rotateY(${(x * 2.4).toFixed(2)}deg) `
                    + `translateY(-2px)`;
            });

            card.addEventListener('pointerleave', () => {
                card.style.transform = '';
            });
        });
    }
})();
