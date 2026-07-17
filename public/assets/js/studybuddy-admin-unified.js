(() => {
    const pathname =
        window.location.pathname.replace(/\/+$/, '');

    if (
        pathname === '/admin/login'
        || pathname.endsWith('/admin/login')
    ) {
        return;
    }

    const root =
        document.querySelector('#admin-main-content')
        || document.querySelector('.sb-simple-admin-content')
        || document.querySelector('.admin-main')
        || document.querySelector('main');

    if (!root) {
        return;
    }

    document.body.classList.add('sb-admin-unified');
    root.classList.add('sb-admin-unified-root');

    const profiles = [
        {
            match: '/final-platform',
            title: 'Apps and Launcher',
            description:
                'Manage this page in four clear areas: public platform settings, app records, launch checklist, and point adjustments.',
            steps: [
                'Open only the block you need.',
                'Edit the fields and save that block.',
                'Preview the public Apps page or launcher.',
            ],
            placeholder:
                'Search app name, setting, checklist item or points...',
        },
        {
            match: '/content-studio',
            title: 'Content Studio',
            description:
                'Pages, reusable cards, and app-catalog content are separated into searchable editing blocks.',
            steps: [
                'Search for the page or card name.',
                'Open its editing block.',
                'Save and preview the public page.',
            ],
            placeholder:
                'Search pages, cards, content or apps...',
        },
        {
            match: '/homepage-cms',
            title: 'Homepage Editor',
            description:
                'Each homepage section is now its own collapsible block, with the cards belonging to that section kept together.',
            steps: [
                'Find the homepage section.',
                'Open and edit its content.',
                'Save, then refresh the homepage.',
            ],
            placeholder:
                'Search hero, apps, profile, trust, footer...',
        },
        {
            match: '/users',
            title: 'User Accounts',
            description:
                'Search and manage learner, parent, teacher, independent learner, and Admin accounts.',
            steps: [
                'Search by name, email or role.',
                'Open the account you need.',
                'Save the account changes.',
            ],
            placeholder:
                'Search name, email, role or learning stage...',
        },
        {
            match: '/shell',
            title: 'Header and Footer',
            description:
                'Shared navigation, header links, footer groups, social links, and platform-wide shell settings are organised here.',
            steps: [
                'Open the navigation or footer block.',
                'Change only the required links.',
                'Save and preview the public website.',
            ],
            placeholder:
                'Search navigation, footer, social or link...',
        },
        {
            match: '/messages',
            title: 'Contact Messages',
            description:
                'Search public contact submissions, open a message, and update its status or Admin notes.',
            steps: [
                'Search the sender or subject.',
                'Open the message.',
                'Update its status after review.',
            ],
            placeholder:
                'Search sender, email, subject or status...',
        },
        {
            match: '/mailing-list',
            title: 'Mailing List',
            description:
                'Find subscribers, review status, export the list, or safely remove an address.',
            steps: [
                'Search the email address.',
                'Review the subscription status.',
                'Reactivate, unsubscribe, export or delete.',
            ],
            placeholder:
                'Search subscriber email or status...',
        },
        {
            match: '/verifications',
            title: 'Verification Review',
            description:
                'Review role-verification requests one case at a time, without losing the applicant context.',
            steps: [
                'Find the verification case.',
                'Review the role and submitted details.',
                'Set the status and save notes.',
            ],
            placeholder:
                'Search user, role, method or status...',
        },
        {
            match: '/health',
            title: 'Website Health',
            description:
                'Review platform checks by system area and focus first on failed or warning results.',
            steps: [
                'Review failed checks first.',
                'Resolve warnings before publishing.',
                'Run the health check again.',
            ],
            placeholder:
                'Search database, routes, storage or forms...',
        },
        {
            match: '/site-settings',
            title: 'Site Settings',
            description:
                'Global brand values, links, labels, and website configuration are organised into consistent editing blocks.',
            steps: [
                'Search the setting name.',
                'Edit only the required value.',
                'Save and verify the public result.',
            ],
            placeholder:
                'Search setting, brand, URL or label...',
        },
        {
            match: '/pages-legal',
            title: 'Pages and Legal',
            description:
                'Manage public information, support, privacy, terms, and legal-page content from clear page blocks.',
            steps: [
                'Find the page.',
                'Edit its title or content.',
                'Save and preview the public page.',
            ],
            placeholder:
                'Search page title, slug or legal content...',
        },
        {
            match: '/account',
            title: 'Admin Account',
            description:
                'Manage your administrator account information in one focused form.',
            steps: [
                'Review the current information.',
                'Change only what is needed.',
                'Save the account.',
            ],
            placeholder:
                'Search account fields...',
        },
    ];

    const profile =
        profiles.find((item) =>
            pathname.includes(item.match)
        )
        || {
            title:
                document.title
                    .replace('· StudyBuddy', '')
                    .replace('StudyBuddy ·', '')
                    .trim()
                || 'Admin Section',

            description:
                'Use the search and collapsible blocks below to manage this Admin section without scanning one long page.',

            steps: [
                'Search for the record or setting.',
                'Open the block you need.',
                'Save and verify the result.',
            ],

            placeholder:
                'Search this Admin page...',
        };

    const svg = (paths, viewBox = '0 0 24 24') => {
        const element =
            document.createElementNS(
                'http://www.w3.org/2000/svg',
                'svg'
            );

        element.setAttribute(
            'viewBox',
            viewBox
        );

        element.setAttribute(
            'aria-hidden',
            'true'
        );

        element.setAttribute('fill', 'none');
        element.setAttribute(
            'stroke',
            'currentColor'
        );

        element.setAttribute(
            'stroke-width',
            '2'
        );

        element.setAttribute(
            'stroke-linecap',
            'round'
        );

        element.setAttribute(
            'stroke-linejoin',
            'round'
        );

        paths.forEach((definition) => {
            const path =
                document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    definition.tag || 'path'
                );

            Object.entries(
                definition.attributes
            ).forEach(([name, value]) => {
                path.setAttribute(name, value);
            });

            element.appendChild(path);
        });

        return element;
    };

    const searchIcon = () =>
        svg([
            {
                tag: 'circle',
                attributes: {
                    cx: '11',
                    cy: '11',
                    r: '7',
                },
            },
            {
                attributes: {
                    d: 'm20 20-3.5-3.5',
                },
            },
        ]);

    const closeIcon = () =>
        svg([
            {
                attributes: {
                    d: 'm6 6 12 12M18 6 6 18',
                },
            },
        ]);

    const chevronIcon = () =>
        svg([
            {
                attributes: {
                    d: 'm8 10 4 4 4-4',
                },
            },
        ]);

    const guide =
        document.createElement('section');

    guide.className =
        'sb-admin-unified-guide';

    guide.dataset.adminUnifiedGenerated = '1';

    const guideCopy =
        document.createElement('div');

    const eyebrow =
        document.createElement('p');

    eyebrow.className =
        'sb-admin-unified-guide__eyebrow';

    eyebrow.textContent =
        'Clear editing mode';

    const guideTitle =
        document.createElement('h2');

    guideTitle.textContent =
        profile.title;

    const guideDescription =
        document.createElement('p');

    guideDescription.className =
        'sb-admin-unified-guide__description';

    guideDescription.textContent =
        profile.description;

    guideCopy.append(
        eyebrow,
        guideTitle,
        guideDescription
    );

    const steps =
        document.createElement('ol');

    steps.className =
        'sb-admin-unified-guide__steps';

    profile.steps.forEach(
        (instruction, index) => {
            const item =
                document.createElement('li');

            const number =
                document.createElement('span');

            number.textContent =
                String(index + 1);

            const text =
                document.createElement('div');

            text.textContent =
                instruction;

            item.append(number, text);
            steps.appendChild(item);
        }
    );

    guide.append(guideCopy, steps);

    root.insertBefore(
        guide,
        root.firstChild
    );

    const toolbar =
        document.createElement('section');

    toolbar.className =
        'sb-admin-unified-toolbar';

    toolbar.dataset.adminUnifiedGenerated = '1';

    const searchLabel =
        document.createElement('label');

    searchLabel.className =
        'sb-admin-unified-search';

    searchLabel.appendChild(searchIcon());

    const search =
        document.createElement('input');

    search.type = 'search';
    search.placeholder =
        profile.placeholder;

    search.autocomplete = 'off';

    search.setAttribute(
        'aria-label',
        'Search this Admin page'
    );

    const clearSearch =
        document.createElement('button');

    clearSearch.type = 'button';
    clearSearch.hidden = true;

    clearSearch.setAttribute(
        'aria-label',
        'Clear search'
    );

    clearSearch.appendChild(closeIcon());

    searchLabel.append(
        search,
        clearSearch
    );

    const toolbarActions =
        document.createElement('div');

    toolbarActions.className =
        'sb-admin-unified-actions';

    const expandAll =
        document.createElement('button');

    expandAll.type = 'button';
    expandAll.textContent =
        'Expand all';

    const collapseAll =
        document.createElement('button');

    collapseAll.type = 'button';
    collapseAll.textContent =
        'Collapse all';

    const compact =
        document.createElement('button');

    compact.type = 'button';
    compact.textContent =
        'Compact view';

    toolbarActions.append(
        expandAll,
        collapseAll,
        compact
    );

    toolbar.append(
        searchLabel,
        toolbarActions
    );

    guide.insertAdjacentElement(
        'afterend',
        toolbar
    );

    const results =
        document.createElement('div');

    results.className =
        'sb-admin-unified-results';

    results.dataset.adminUnifiedGenerated = '1';

    const resultText =
        document.createElement('span');

    const saveReminder =
        document.createElement('span');

    saveReminder.textContent =
        'Save each block before opening another page.';

    results.append(
        resultText,
        saveReminder
    );

    toolbar.insertAdjacentElement(
        'afterend',
        results
    );

    const destructiveMethod = (form) => {
        const value =
            form.querySelector(
                'input[name="_method"]'
            )?.value?.toUpperCase();

        return value === 'DELETE';
    };

    const visibleFieldCount = (form) =>
        form.querySelectorAll(
            [
                'input:not([type="hidden"])',
                'select',
                'textarea',
            ].join(',')
        ).length;

    const formValues = (element) =>
        Array.from(
            element.querySelectorAll(
                'input, select, textarea'
            )
        )
            .map((field) => field.value || '')
            .join(' ');

    const textFor = (element) =>
        (
            element.textContent
            + ' '
            + formValues(element)
        )
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ');

    const forms = Array.from(
        root.querySelectorAll('form')
    ).filter((form) => {
        if (
            form.closest(
                [
                    '.sb-simple-admin-sidebar',
                    '.sb-simple-admin-topbar',
                    '[data-admin-unified-generated]',
                    'table',
                ].join(',')
            )
        ) {
            return false;
        }

        return true;
    });

    forms.forEach((form) => {
        const count =
            visibleFieldCount(form);

        if (
            destructiveMethod(form)
            || count <= 2
        ) {
            form.classList.add(
                'sb-admin-unified-inline-form'
            );

            return;
        }

        form.classList.add(
            'sb-admin-unified-form'
        );
    });

    const targets = [];
    const seen = new Set();

    forms.forEach((form) => {
        if (
            destructiveMethod(form)
            || visibleFieldCount(form) < 3
            || form.closest(
                'details[data-admin-unified-disclosure]'
            )
        ) {
            return;
        }

        let target = form;

        const possibleContainer =
            form.closest(
                [
                    'article',
                    '.admin-card',
                    '.content-card',
                    '.settings-card',
                    '.platform-card',
                    '.app-card',
                    '.cms-card',
                    '.health-card',
                    '.message-card',
                    '.user-card',
                    '.panel',
                ].join(',')
            );

        if (
            possibleContainer
            && possibleContainer !== root
            && possibleContainer
                .querySelectorAll('form')
                .length === 1
        ) {
            target = possibleContainer;
        }

        if (
            seen.has(target)
            || target.closest(
                'details[data-admin-unified-disclosure]'
            )
        ) {
            return;
        }

        seen.add(target);

        targets.push({
            form,
            target,
        });
    });

    const disclosureHost =
        document.createElement('div');

    disclosureHost.className =
        'sb-admin-unified-disclosures';

    disclosureHost.dataset.adminUnifiedGenerated =
        '1';

    if (targets.length) {
        results.insertAdjacentElement(
            'afterend',
            disclosureHost
        );
    }

    const disclosures = [];

    targets.forEach(
        ({ form, target }, index) => {
            const heading =
                target.querySelector(
                    'h1, h2, h3, h4'
                );

            const namedField =
                form.querySelector(
                    [
                        'input[name="name"]',
                        'input[name="title"]',
                        'input[name="label"]',
                        'input[name="subject"]',
                        'input[name="email"]',
                    ].join(',')
                );

            const title =
                heading?.textContent?.trim()
                || namedField?.value?.trim()
                || `Editing block ${index + 1}`;

            if (heading) {
                heading.classList.add(
                    'sb-admin-unified-original-heading'
                );
            }

            const details =
                document.createElement('details');

            details.className =
                'sb-admin-unified-disclosure';

            details.dataset.adminUnifiedDisclosure =
                '1';

            details.dataset.search =
                textFor(target);

            details.id =
                `admin-edit-block-${index + 1}`;

            details.open =
                index < 2
                || Boolean(
                    form.querySelector(
                        [
                            '.is-invalid',
                            '[aria-invalid="true"]',
                            '.error',
                        ].join(',')
                    )
                );

            const summary =
                document.createElement('summary');

            const marker =
                document.createElement('span');

            marker.className =
                'sb-admin-unified-disclosure__marker';

            marker.setAttribute(
                'aria-hidden',
                'true'
            );

            const titleBox =
                document.createElement('span');

            titleBox.className =
                'sb-admin-unified-disclosure__title';

            const strong =
                document.createElement('strong');

            strong.textContent = title;

            const helper =
                document.createElement('small');

            helper.textContent =
                visibleFieldCount(form)
                + (
                    visibleFieldCount(form) === 1
                        ? ' editable field'
                        : ' editable fields'
                );

            titleBox.append(
                strong,
                helper
            );

            const count =
                document.createElement('span');

            count.className =
                'sb-admin-unified-disclosure__count';

            count.textContent =
                index === 0
                    ? 'Start here'
                    : 'Edit block';

            const chevron =
                chevronIcon();

            chevron.classList.add(
                'sb-admin-unified-disclosure__chevron'
            );

            summary.append(
                marker,
                titleBox,
                count,
                chevron
            );

            const body =
                document.createElement('div');

            body.className =
                'sb-admin-unified-disclosure__body';

            target.parentNode.insertBefore(
                details,
                target
            );

            body.appendChild(target);
            details.append(
                summary,
                body
            );

            disclosureHost.appendChild(
                details
            );

            disclosures.push(details);
        }
    );

    const cardSelectors = [
        '.admin-card',
        '.content-card',
        '.settings-card',
        '.platform-card',
        '.app-card',
        '.cms-card',
        '.health-card',
        '.message-card',
        '.user-card',
        '.panel',
        '.stat-card',
        '.summary-card',
    ];

    const standaloneCards =
        Array.from(
            root.querySelectorAll(
                cardSelectors.join(',')
            )
        ).filter((card) => {
            return (
                !card.closest(
                    'details[data-admin-unified-disclosure]'
                )
                && !card.closest(
                    '[data-admin-unified-generated]'
                )
            );
        });

    standaloneCards.forEach((card) => {
        card.classList.add(
            'sb-admin-unified-card'
        );

        card.dataset.search =
            textFor(card);
    });

    const tables = Array.from(
        root.querySelectorAll('table')
    );

    tables.forEach((table) => {
        if (
            !table.parentElement.matches(
                [
                    '.admin-table-wrap',
                    '.table-wrap',
                    '.responsive-table',
                    '.sb-mail-admin__table-wrap',
                ].join(',')
            )
        ) {
            const wrapper =
                document.createElement('div');

            wrapper.className =
                'admin-table-wrap';

            table.parentNode.insertBefore(
                wrapper,
                table
            );

            wrapper.appendChild(table);
        }
    });

    const pageHeadings =
        Array.from(
            root.querySelectorAll(
                [
                    'h2:not(.sb-admin-unified-guide h2)',
                    'h3',
                ].join(',')
            )
        ).filter((heading) => {
            return (
                !heading.closest(
                    '[data-admin-unified-generated]'
                )
                && !heading.classList.contains(
                    'sb-admin-unified-original-heading'
                )
            );
        });

    if (
        disclosures.length > 1
        || pageHeadings.length > 1
    ) {
        const navigation =
            document.createElement('nav');

        navigation.className =
            'sb-admin-unified-nav';

        navigation.setAttribute(
            'aria-label',
            'Jump to an editing section'
        );

        const navigationItems =
            disclosures.length
                ? disclosures.slice(0, 20)
                : pageHeadings.slice(0, 20);

        navigationItems.forEach(
            (element, index) => {
                let id = element.id;

                if (!id) {
                    id =
                        `admin-page-section-${index + 1}`;

                    element.id = id;
                }

                const label =
                    element.querySelector(
                        'summary strong'
                    )?.textContent?.trim()
                    || element.textContent?.trim()
                    || `Section ${index + 1}`;

                const link =
                    document.createElement('a');

                link.href = `#${id}`;

                link.textContent =
                    label.slice(0, 55);

                link.addEventListener(
                    'click',
                    (event) => {
                        event.preventDefault();

                        if (
                            element instanceof
                            HTMLDetailsElement
                        ) {
                            element.open = true;
                        }

                        element.scrollIntoView({
                            behavior:
                                window.matchMedia(
                                    '(prefers-reduced-motion: reduce)'
                                ).matches
                                    ? 'auto'
                                    : 'smooth',

                            block: 'start',
                        });
                    }
                );

                navigation.appendChild(link);
            }
        );

        results.insertAdjacentElement(
            'afterend',
            navigation
        );
    }

    const empty =
        document.createElement('section');

    empty.className =
        'sb-admin-unified-empty';

    empty.hidden = true;

    empty.dataset.adminUnifiedGenerated = '1';

    empty.appendChild(searchIcon());

    const emptyTitle =
        document.createElement('h3');

    emptyTitle.textContent =
        'No Admin record matches that search.';

    const emptyText =
        document.createElement('p');

    emptyText.textContent =
        'Try a page name, app name, user, email, status, label, or setting.';

    const emptyButton =
        document.createElement('button');

    emptyButton.type = 'button';
    emptyButton.textContent =
        'Clear search';

    empty.append(
        emptyTitle,
        emptyText,
        emptyButton
    );

    root.appendChild(empty);

    const tableRows = () =>
        Array.from(
            root.querySelectorAll(
                'tbody tr'
            )
        );

    const searchableItems = () => [
        ...disclosures,
        ...standaloneCards,
    ];

    const updateResults = () => {
        const query =
            search.value
                .trim()
                .toLowerCase()
                .replace(/\s+/g, ' ');

        let visibleBlocks = 0;
        let visibleRows = 0;

        searchableItems().forEach(
            (item) => {
                const haystack =
                    item.dataset.search
                    || textFor(item);

                const visible =
                    !query
                    || haystack.includes(query);

                item.hidden = !visible;

                if (visible) {
                    visibleBlocks += 1;

                    if (
                        query
                        && item instanceof
                        HTMLDetailsElement
                    ) {
                        item.open = true;
                    }
                }
            }
        );

        tableRows().forEach((row) => {
            const visible =
                !query
                || textFor(row).includes(query);

            row.hidden = !visible;

            if (visible) {
                visibleRows += 1;
            }
        });

        const totalVisible =
            visibleBlocks + visibleRows;

        const totalAvailable =
            searchableItems().length
            + tableRows().length;

        resultText.innerHTML =
            `Showing <strong>${totalVisible}</strong>`
            + ` of ${totalAvailable}`
            + ` editable records`;

        clearSearch.hidden =
            query.length === 0;

        empty.hidden =
            totalAvailable === 0
            || totalVisible !== 0;
    };

    let searchTimer;

    search.addEventListener(
        'input',
        () => {
            window.clearTimeout(
                searchTimer
            );

            searchTimer =
                window.setTimeout(
                    updateResults,
                    70
                );
        }
    );

    const resetSearch = () => {
        search.value = '';
        updateResults();
        search.focus();
    };

    clearSearch.addEventListener(
        'click',
        resetSearch
    );

    emptyButton.addEventListener(
        'click',
        resetSearch
    );

    expandAll.addEventListener(
        'click',
        () => {
            disclosures.forEach(
                (details) => {
                    if (!details.hidden) {
                        details.open = true;
                    }
                }
            );
        }
    );

    collapseAll.addEventListener(
        'click',
        () => {
            disclosures.forEach(
                (details) => {
                    details.open = false;
                }
            );
        }
    );

    const COMPACT_KEY =
        'studybuddy-admin-compact-view';

    const setCompact = (enabled) => {
        document.body.classList.toggle(
            'is-admin-compact',
            enabled
        );

        compact.classList.toggle(
            'is-active',
            enabled
        );

        compact.textContent =
            enabled
                ? 'Comfortable view'
                : 'Compact view';

        try {
            window.localStorage.setItem(
                COMPACT_KEY,
                enabled ? '1' : '0'
            );
        } catch (error) {
            // Compact preference is optional.
        }
    };

    let compactEnabled = false;

    try {
        compactEnabled =
            window.localStorage.getItem(
                COMPACT_KEY
            ) === '1';
    } catch (error) {
        compactEnabled = false;
    }

    compact.addEventListener(
        'click',
        () => {
            setCompact(
                !document.body.classList
                    .contains(
                        'is-admin-compact'
                    )
            );
        }
    );

    setCompact(compactEnabled);
    updateResults();
})();
