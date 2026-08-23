(() => {
    const root = document.querySelector(
        '[data-admin-block-dashboard]'
    );

    if (!root) {
        return;
    }

    const search = root.querySelector(
        '[data-admin-block-search]'
    );

    const searchClear = root.querySelector(
        '[data-admin-block-search-clear]'
    );

    const tools = Array.from(
        root.querySelectorAll(
            '[data-admin-block-tool]'
        )
    );

    const zones = Array.from(
        root.querySelectorAll(
            '[data-admin-zone]'
        )
    );

    const empty = root.querySelector(
        '[data-admin-block-empty]'
    );

    const reset = root.querySelector(
        '[data-admin-block-reset]'
    );

    const recentSection = root.querySelector(
        '[data-admin-recent-section]'
    );

    const recentGrid = root.querySelector(
        '[data-admin-recent-grid]'
    );

    const recentClear = root.querySelector(
        '[data-admin-recent-clear]'
    );

    const RECENT_KEY =
        'studybuddy-admin-recent-tools-v1';

    const normalize = (value = '') => {
        return String(value)
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ');
    };

    const escapeId = (value = '') => {
        return String(value)
            .replace(/[^a-zA-Z0-9_-]/g, '');
    };

    const updateSearch = () => {
        const query = normalize(
            search?.value
        );

        let totalVisible = 0;

        tools.forEach((card) => {
            const visible =
                !query
                || normalize(
                    card.dataset.search
                ).includes(query);

            card.hidden = !visible;

            if (visible) {
                totalVisible += 1;
            }
        });

        zones.forEach((zone) => {
            const visibleTools = Array.from(
                zone.querySelectorAll(
                    '[data-admin-block-tool]'
                )
            ).filter((card) => !card.hidden);

            zone.hidden =
                visibleTools.length === 0;

            const count = zone.querySelector(
                '[data-admin-zone-count]'
            );

            if (count) {
                count.textContent =
                    `${visibleTools.length} `
                    + (
                        visibleTools.length === 1
                            ? 'tool'
                            : 'tools'
                    );
            }

            if (
                query
                && visibleTools.length > 0
            ) {
                zone.open = true;
            }
        });

        if (empty) {
            empty.hidden =
                totalVisible !== 0;
        }

        if (searchClear) {
            searchClear.hidden =
                query.length === 0;
        }
    };

    let searchTimer;

    search?.addEventListener(
        'input',
        () => {
            window.clearTimeout(searchTimer);

            searchTimer = window.setTimeout(
                updateSearch,
                70
            );
        }
    );

    const clearSearch = () => {
        if (search) {
            search.value = '';
        }

        updateSearch();
        search?.focus();
    };

    searchClear?.addEventListener(
        'click',
        clearSearch
    );

    reset?.addEventListener(
        'click',
        clearSearch
    );

    root
        .querySelectorAll(
            '[data-admin-zone-jump]'
        )
        .forEach((link) => {
            link.addEventListener(
                'click',
                (event) => {
                    const zoneId =
                        link.dataset.adminZoneJump;

                    const zone =
                        root.querySelector(
                            `[data-admin-zone="${zoneId}"]`
                        );

                    if (!zone) {
                        return;
                    }

                    event.preventDefault();
                    zone.open = true;

                    zone.scrollIntoView({
                        behavior:
                            window.matchMedia(
                                '(prefers-reduced-motion: reduce)'
                            ).matches
                                ? 'auto'
                                : 'smooth',
                        block: 'start',
                    });

                    window.setTimeout(
                        () => zone
                            .querySelector('summary')
                            ?.focus(),
                        350
                    );
                }
            );
        });

    const readRecent = () => {
        try {
            const value =
                window.localStorage.getItem(
                    RECENT_KEY
                );

            const parsed = value
                ? JSON.parse(value)
                : [];

            return Array.isArray(parsed)
                ? parsed
                : [];
        } catch (error) {
            return [];
        }
    };

    const writeRecent = (items) => {
        try {
            window.localStorage.setItem(
                RECENT_KEY,
                JSON.stringify(items)
            );
        } catch (error) {
            // Recent shortcuts remain optional.
        }
    };

    const createSvg = (symbolId) => {
        const svg = document.createElementNS(
            'http://www.w3.org/2000/svg',
            'svg'
        );

        svg.setAttribute(
            'aria-hidden',
            'true'
        );

        const use = document.createElementNS(
            'http://www.w3.org/2000/svg',
            'use'
        );

        use.setAttribute(
            'href',
            `#sb-admin-icon-${escapeId(symbolId)}`
        );

        svg.appendChild(use);

        return svg;
    };

    const renderRecent = () => {
        if (!recentGrid || !recentSection) {
            return;
        }

        const items = readRecent()
            .filter((item) => {
                return (
                    item
                    && item.id
                    && item.title
                    && item.url
                );
            })
            .slice(0, 4);

        recentGrid.replaceChildren();

        if (!items.length) {
            recentSection.hidden = true;
            return;
        }

        items.forEach((item) => {
            const link =
                document.createElement('a');

            link.className =
                'sb-admin-recent-card';

            link.href = item.url;

            link.dataset.adminToolLink = '';
            link.dataset.toolId = item.id;
            link.dataset.toolTitle = item.title;
            link.dataset.toolDescription =
                item.description || '';
            link.dataset.toolUrl = item.url;
            link.dataset.toolIcon =
                item.icon || 'edit';
            link.dataset.toolCategory =
                item.category || 'Admin';

            const icon =
                document.createElement('span');

            icon.className =
                'sb-admin-recent-card__icon';

            icon.appendChild(
                createSvg(item.icon || 'edit')
            );

            const copy =
                document.createElement('span');

            const title =
                document.createElement('strong');

            title.textContent = item.title;

            const category =
                document.createElement('small');

            category.textContent =
                item.category || 'Admin';

            copy.append(title, category);

            link.append(
                icon,
                copy,
                createSvg('arrow')
            );

            recentGrid.appendChild(link);
        });

        recentSection.hidden = false;
    };

    const saveRecentLink = (link) => {
        const item = {
            id:
                link.dataset.toolId
                || link.href,

            title:
                link.dataset.toolTitle
                || link.textContent.trim(),

            description:
                link.dataset.toolDescription
                || '',

            url:
                link.dataset.toolUrl
                || link.href,

            icon:
                link.dataset.toolIcon
                || 'edit',

            category:
                link.dataset.toolCategory
                || 'Admin',
        };

        const existing = readRecent()
            .filter(
                (entry) =>
                    entry.id !== item.id
            );

        writeRecent([
            item,
            ...existing,
        ].slice(0, 4));
    };

    root.addEventListener(
        'click',
        (event) => {
            const link = event.target.closest(
                '[data-admin-tool-link]'
            );

            if (!link) {
                return;
            }

            saveRecentLink(link);
        }
    );

    recentClear?.addEventListener(
        'click',
        () => {
            writeRecent([]);
            renderRecent();
        }
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const id =
                    entry.target.dataset.adminZone;

                root
                    .querySelectorAll(
                        '[data-admin-zone-jump]'
                    )
                    .forEach((link) => {
                        link.classList.toggle(
                            'is-active',
                            link.dataset.adminZoneJump
                                === id
                        );
                    });
            });
        },
        {
            rootMargin:
                '-25% 0px -65% 0px',
            threshold: 0,
        }
    );

    zones.forEach((zone) => {
        observer.observe(zone);
    });

    updateSearch();
    renderRecent();
})();
