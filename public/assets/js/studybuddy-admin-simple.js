(() => {
    const shell = document.querySelector(
        '[data-admin-shell]'
    );

    if (!shell) {
        return;
    }

    const sidebar = shell.querySelector(
        '[data-admin-sidebar]'
    );

    const openButton = shell.querySelector(
        '[data-admin-sidebar-open]'
    );

    const closeButton = shell.querySelector(
        '[data-admin-sidebar-close]'
    );

    const overlay = shell.querySelector(
        '[data-admin-overlay]'
    );

    const page = shell.querySelector(
        '.sb-admin-page'
    );

    const mobileNavigation = window.matchMedia(
        '(max-width: 900px)'
    );

    let returnFocus = null;

    const focusableInSidebar = () => Array.from(
        sidebar?.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        ) || []
    ).filter((element) => !element.hidden && element.getClientRects().length > 0);

    const setSidebar = (open, restoreFocus = true) => {
        const useDrawer = mobileNavigation.matches;
        const drawerOpen = useDrawer && open;

        if (drawerOpen) {
            returnFocus = document.activeElement;
        }

        shell.classList.toggle(
            'is-sidebar-open',
            drawerOpen
        );

        openButton?.setAttribute(
            'aria-expanded',
            drawerOpen ? 'true' : 'false'
        );

        if (overlay) {
            overlay.hidden = !drawerOpen;
        }

        document.body.style.overflow =
            drawerOpen ? 'hidden' : '';

        if (sidebar) {
            sidebar.toggleAttribute(
                'inert',
                useDrawer && !drawerOpen
            );
            sidebar.setAttribute(
                'aria-hidden',
                useDrawer && !drawerOpen
                    ? 'true'
                    : 'false'
            );
        }

        if (page) {
            page.toggleAttribute(
                'inert',
                drawerOpen
            );
        }

        if (drawerOpen) {
            closeButton?.focus();
        } else if (
            restoreFocus
            && useDrawer
            && returnFocus instanceof HTMLElement
        ) {
            returnFocus.focus();
            returnFocus = null;
        }
    };

    openButton?.addEventListener(
        'click',
        () => setSidebar(true)
    );

    closeButton?.addEventListener(
        'click',
        () => setSidebar(false)
    );

    overlay?.addEventListener(
        'click',
        () => setSidebar(false)
    );

    window.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key === 'Escape'
                && shell.classList.contains(
                    'is-sidebar-open'
                )
            ) {
                setSidebar(false);
            }
        }
    );

    window.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key !== 'Tab'
                || !shell.classList.contains(
                    'is-sidebar-open'
                )
            ) {
                return;
            }

            const focusable = focusableInSidebar();
            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (!first || !last) {
                return;
            }

            if (
                event.shiftKey
                && document.activeElement === first
            ) {
                event.preventDefault();
                last.focus();
            } else if (
                !event.shiftKey
                && document.activeElement === last
            ) {
                event.preventDefault();
                first.focus();
            }
        }
    );

    sidebar
        ?.querySelectorAll('a')
        .forEach((link) => {
            link.addEventListener(
                'click',
                () => {
                    if (
                        window.matchMedia(
                            '(max-width: 900px)'
                        ).matches
                    ) {
                        setSidebar(false, false);
                    }
                }
            );
        });

    mobileNavigation.addEventListener(
        'change',
        () => setSidebar(false, false)
    );

    setSidebar(false, false);

    const search = document.querySelector(
        '[data-admin-tool-search]'
    );

    const cards = Array.from(
        document.querySelectorAll(
            '[data-admin-tool-card]'
        )
    );

    const filters = Array.from(
        document.querySelectorAll(
            '[data-admin-tool-filter]'
        )
    );

    const empty = document.querySelector(
        '[data-admin-tool-empty]'
    );

    const reset = document.querySelector(
        '[data-admin-tool-reset]'
    );

    if (!cards.length) {
        return;
    }

    let activeCategory = 'all';

    const normalize = (value = '') => {
        return String(value)
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ');
    };

    const updateTools = () => {
        const query = normalize(
            search?.value
        );

        let visible = 0;

        cards.forEach((card) => {
            const matchesSearch =
                !query
                || normalize(
                    card.dataset.search
                ).includes(query);

            const matchesCategory =
                activeCategory === 'all'
                || card.dataset.category
                    === activeCategory;

            const show =
                matchesSearch
                && matchesCategory;

            card.hidden = !show;

            if (show) {
                visible += 1;
            }
        });

        if (empty) {
            empty.hidden = visible !== 0;
        }
    };

    let searchTimer;

    search?.addEventListener(
        'input',
        () => {
            window.clearTimeout(searchTimer);

            searchTimer = window.setTimeout(
                updateTools,
                80
            );
        }
    );

    filters.forEach((button) => {
        button.addEventListener(
            'click',
            () => {
                activeCategory =
                    button.dataset.adminToolFilter
                    || 'all';

                filters.forEach((item) => {
                    const active =
                        item === button;

                    item.classList.toggle(
                        'is-active',
                        active
                    );

                    item.setAttribute(
                        'aria-pressed',
                        active
                            ? 'true'
                            : 'false'
                    );
                });

                updateTools();
            }
        );
    });

    reset?.addEventListener(
        'click',
        () => {
            activeCategory = 'all';

            if (search) {
                search.value = '';
            }

            filters.forEach((button) => {
                const active =
                    button.dataset.adminToolFilter
                    === 'all';

                button.classList.toggle(
                    'is-active',
                    active
                );

                button.setAttribute(
                    'aria-pressed',
                    active
                        ? 'true'
                        : 'false'
                );
            });

            updateTools();
            search?.focus();
        }
    );

    updateTools();
})();
