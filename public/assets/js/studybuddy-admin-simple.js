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

    const setSidebar = (open) => {
        shell.classList.toggle(
            'is-sidebar-open',
            open
        );

        openButton?.setAttribute(
            'aria-expanded',
            open ? 'true' : 'false'
        );

        if (overlay) {
            overlay.hidden = !open;
        }

        document.body.style.overflow =
            open ? 'hidden' : '';

        if (open) {
            closeButton?.focus();
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
                openButton?.focus();
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
                        setSidebar(false);
                    }
                }
            );
        });

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
