(() => {
    const path = window.location.pathname.replace(/\/+$/, '') || '/';
    const isAppsPage = path === '/apps';
    const isRolesPage = path === '/roles';

    if (!isAppsPage && !isRolesPage) {
        return;
    }

    const body = document.body;
    const reduceMotion = window
        .matchMedia('(prefers-reduced-motion: reduce)')
        .matches;

    const finePointer = window
        .matchMedia('(pointer: fine)')
        .matches;

    body.classList.add(
        isAppsPage
            ? 'sb-apps-color-page'
            : 'sb-roles-color-page'
    );

    const main = document.querySelector('main');

    if (!main) {
        return;
    }

    const root = isAppsPage
        ? (
            main.querySelector(
                '[data-app-catalog], '
                + '.sb-catalog-v3, '
                + '.apps-page-shell, '
                + '.app-universe-shell'
            )
            || main
        )
        : (
            main.querySelector(
                '[data-role-page], '
                + '.sb-roles-page, '
                + '.roles-page, '
                + '.roles-shell'
            )
            || main
        );

    root.classList.add('sb-color-page-root');

    const firstSection =
        root.querySelector(
            isAppsPage
                ? (
                    '.sb-catalog-v3__hero, '
                    + '.apps-hero, '
                    + '.app-universe-hero, '
                    + 'section:first-of-type'
                )
                : (
                    '.roles-hero, '
                    + '.sb-roles-hero, '
                    + '.role-hero, '
                    + 'section:first-of-type'
                )
        );

    firstSection?.classList.add('sb-color-hero');

    if (
        isAppsPage
        && firstSection
        && !firstSection.querySelector(
            '.sb-color-hero__art'
        )
    ) {
        const art = document.createElement('div');
        art.className = 'sb-color-hero__art';
        art.setAttribute('aria-hidden', 'false');

        const orbit = document.createElement('span');
        orbit.className = 'sb-color-hero__orbit';
        orbit.setAttribute('aria-hidden', 'true');

        const image = document.createElement('img');
        image.src =
            '/assets/images/pages/path-apps-main.png';

        image.alt =
            'StudyBuddy learning apps including books, science, art, geography, ideas, maths, and audio activities';

        image.decoding = 'async';
        image.fetchPriority = 'high';

        image.addEventListener(
            'error',
            () => {
                const remote =
                    'https://raw.githubusercontent.com/'
                    + 'MehAk-ArmAn/StudyBuddy-Imgs/main/'
                    + 'homepage-paths/path-apps.png';

                if (image.src !== remote) {
                    image.src = remote;
                }
            },
            { once: true }
        );

        art.append(orbit, image);
        firstSection.appendChild(art);
    }

    const toolbar = isAppsPage
        ? root.querySelector(
            '.sb-catalog-v3__toolbar, '
            + '.apps-toolbar, '
            + '.sb-apps-filter-form, '
            + '[data-app-filters]'
        )
        : null;

    toolbar?.classList.add('sb-color-toolbar');

    const cardSelector = isAppsPage
        ? (
            '[data-app-card], '
            + '.sb-app-card-v3, '
            + '.app-card, '
            + '.mini-app-card, '
            + '.apps-grid > article, '
            + '.app-grid > article'
        )
        : (
            '[data-role-card], '
            + '.sb-role-card, '
            + '.role-card, '
            + '.roles-grid > article, '
            + '.roles-grid > a, '
            + '.role-grid > article, '
            + '.role-grid > a'
        );

    let cards = Array.from(
        root.querySelectorAll(cardSelector)
    );

    if (isRolesPage && cards.length === 0) {
        cards = Array.from(
            root.querySelectorAll('article')
        ).filter((card) => {
            return Boolean(
                card.querySelector('h2, h3')
            );
        });
    }

    cards.forEach((card, index) => {
        card.classList.add(
            'sb-color-card',
            'sb-color-reveal'
        );

        card.dataset.colorIndex = String(index % 6);

        if (isRolesPage) {
            card.tabIndex = card.tabIndex >= 0
                ? card.tabIndex
                : 0;

            card.addEventListener('mouseenter', () => {
                activateRoleCard(card);
            });

            card.addEventListener('focusin', () => {
                activateRoleCard(card);
            });

            card.addEventListener('keydown', (event) => {
                if (
                    event.key !== 'Enter'
                    && event.key !== ' '
                ) {
                    return;
                }

                if (
                    event.target.closest(
                        'a, button, input, select, textarea'
                    )
                ) {
                    return;
                }

                const link = card.querySelector('a[href]');

                if (link) {
                    event.preventDefault();
                    link.click();
                }
            });
        }

        if (
            finePointer
            && !reduceMotion
        ) {
            card.addEventListener(
                'pointermove',
                (event) => {
                    const rectangle =
                        card.getBoundingClientRect();

                    const x =
                        (
                            event.clientX
                            - rectangle.left
                        )
                        / rectangle.width
                        - .5;

                    const y =
                        (
                            event.clientY
                            - rectangle.top
                        )
                        / rectangle.height
                        - .5;

                    card.style.transform =
                        `perspective(950px) `
                        + `rotateX(${(-y * 2.4).toFixed(2)}deg) `
                        + `rotateY(${(x * 2.4).toFixed(2)}deg) `
                        + `translateY(-4px)`;
                }
            );

            card.addEventListener(
                'pointerleave',
                () => {
                    card.style.transform = '';
                }
            );
        }
    });

    function cardTitle(card, index) {
        const heading = card.querySelector(
            'h1, h2, h3, strong'
        );

        const value =
            heading?.textContent?.trim();

        return value || `Role ${index + 1}`;
    }

    let switcherButtons = [];

    function activateRoleCard(card) {
        if (!isRolesPage) {
            return;
        }

        cards.forEach((item) => {
            item.classList.toggle(
                'is-active',
                item === card
            );
        });

        switcherButtons.forEach((button) => {
            button.classList.toggle(
                'is-active',
                Number(button.dataset.roleIndex)
                    === cards.indexOf(card)
            );

            button.setAttribute(
                'aria-pressed',
                Number(button.dataset.roleIndex)
                    === cards.indexOf(card)
                    ? 'true'
                    : 'false'
            );
        });
    }

    if (
        isRolesPage
        && cards.length > 1
        && !root.querySelector(
            '.sb-role-color-switcher'
        )
    ) {
        const switcher =
            document.createElement('div');

        switcher.className =
            'sb-role-color-switcher';

        switcher.setAttribute(
            'aria-label',
            'Choose a StudyBuddy role'
        );

        cards.forEach((card, index) => {
            const button =
                document.createElement('button');

            button.type = 'button';
            button.textContent =
                cardTitle(card, index);

            button.dataset.roleIndex =
                String(index);

            button.setAttribute(
                'aria-pressed',
                index === 0
                    ? 'true'
                    : 'false'
            );

            if (index === 0) {
                button.classList.add(
                    'is-active'
                );
            }

            button.addEventListener(
                'click',
                () => {
                    activateRoleCard(card);

                    card.scrollIntoView({
                        behavior: reduceMotion
                            ? 'auto'
                            : 'smooth',
                        block: 'center',
                    });

                    window.setTimeout(
                        () => card.focus(),
                        reduceMotion
                            ? 0
                            : 420
                    );
                }
            );

            button.addEventListener(
                'keydown',
                (event) => {
                    if (
                        event.key !== 'ArrowRight'
                        && event.key !== 'ArrowLeft'
                    ) {
                        return;
                    }

                    event.preventDefault();

                    const direction =
                        event.key === 'ArrowRight'
                            ? 1
                            : -1;

                    const nextIndex =
                        (
                            index
                            + direction
                            + cards.length
                        )
                        % cards.length;

                    switcherButtons[
                        nextIndex
                    ]?.focus();
                }
            );

            switcher.appendChild(button);
            switcherButtons.push(button);
        });

        const cardContainer =
            cards[0].parentElement;

        cardContainer?.parentElement
            ?.insertBefore(
                switcher,
                cardContainer
            );

        activateRoleCard(cards[0]);
    }

    const revealItems = [
        firstSection,
        toolbar,
        ...cards,
    ].filter(Boolean);

    if (
        reduceMotion
        || !('IntersectionObserver' in window)
    ) {
        revealItems.forEach((item) => {
            item.classList.add('is-visible');
        });
    } else {
        revealItems.forEach((item) => {
            item.classList.add(
                'sb-color-reveal'
            );
        });

        const observer =
            new IntersectionObserver(
                (entries, activeObserver) => {
                    entries.forEach((entry) => {
                        if (
                            !entry.isIntersecting
                        ) {
                            return;
                        }

                        entry.target.classList.add(
                            'is-visible'
                        );

                        activeObserver.unobserve(
                            entry.target
                        );
                    });
                },
                {
                    threshold: .1,
                    rootMargin:
                        '0px 0px -7% 0px',
                }
            );

        revealItems.forEach((item) => {
            observer.observe(item);
        });
    }
})();
