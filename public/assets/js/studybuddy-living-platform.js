(() => {
    document.querySelectorAll('[data-search-root]').forEach((root) => {
        const input = root.querySelector('[data-search-input]');
        const panel = root.querySelector('[data-search-results]');
        const endpoint = root.dataset.searchEndpoint;

        if (!input || !panel || !endpoint) {
            return;
        }

        let timer;

        const escapeHtml = (value = '') => {
            return String(value).replace(
                /[&<>'"]/g,
                (character) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#039;',
                    '"': '&quot;',
                })[character]
            );
        };

        const render = (items) => {
            if (!items.length) {
                panel.innerHTML =
                    '<div class="sb-search-empty-mini">'
                    + 'No quick results yet. Press Enter to search.'
                    + '</div>';

                panel.hidden = false;
                return;
            }

            panel.innerHTML = items.map((item) => {
                const media = item.image
                    ? `<img src="${escapeHtml(item.image)}" alt="">`
                    : '<img src="/assets/studybuddy-brand/icon-world.svg" alt="">';

                return `
                    <a
                        class="sb-search-hit"
                        href="${escapeHtml(item.url || '#')}"
                    >
                        ${media}
                        <div>
                            <small>
                                ${escapeHtml(item.type || 'Result')}
                            </small>
                            <strong>
                                ${escapeHtml(item.title || 'StudyBuddy result')}
                            </strong>
                            <p>
                                ${escapeHtml(item.description || 'Open this result.')}
                            </p>
                        </div>
                    </a>
                `;
            }).join('');

            panel.hidden = false;
        };

        const search = () => {
            const query = input.value.trim();

            if (!query) {
                panel.hidden = true;
                panel.innerHTML = '';
                return;
            }

            fetch(
                `${endpoint}?q=${encodeURIComponent(query)}`,
                {
                    headers: {
                        Accept: 'application/json',
                    },
                }
            )
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Search failed');
                    }

                    return response.json();
                })
                .then((data) => {
                    render(data.results || []);
                })
                .catch(() => {
                    panel.innerHTML =
                        '<div class="sb-search-empty-mini">'
                        + 'Search is unavailable right now. '
                        + 'Press Enter for the full search page.'
                        + '</div>';

                    panel.hidden = false;
                });
        };

        input.addEventListener('input', () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(search, 140);
        });

        input.addEventListener('focus', search);

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                panel.hidden = true;
            }
        });
    });

    const cards = Array.from(
        document.querySelectorAll('[data-community-card]')
    );

    const searchField =
        document.querySelector('[data-community-search]');

    const roleField =
        document.querySelector('[data-community-role]');

    const themeField =
        document.querySelector('[data-community-theme]');

    const empty =
        document.querySelector('[data-community-empty]');

    const applyFilters = () => {
        const query =
            (searchField?.value || '')
                .trim()
                .toLowerCase();

        const role = roleField?.value || 'all';
        const theme = themeField?.value || 'all';

        let visible = 0;

        cards.forEach((card) => {
            const matchesSearch =
                !query
                || (card.dataset.search || '').includes(query);

            const matchesRole =
                role === 'all'
                || card.dataset.role === role;

            const matchesTheme =
                theme === 'all'
                || card.dataset.theme === theme;

            const show =
                matchesSearch
                && matchesRole
                && matchesTheme;

            card.hidden = !show;

            if (show) {
                visible += 1;
            }
        });

        if (empty) {
            empty.hidden = visible !== 0;
        }
    };

    [
        searchField,
        roleField,
        themeField,
    ].forEach((field) => {
        field?.addEventListener('input', applyFilters);
        field?.addEventListener('change', applyFilters);
    });

    applyFilters();
})();
