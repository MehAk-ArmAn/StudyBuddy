(() => {
    const root = document.querySelector('[data-app-catalog]');

    if (!root) {
        return;
    }

    const search = root.querySelector('[data-app-search]');
    const category = root.querySelector('[data-app-category]');
    const role = root.querySelector('[data-app-role]');
    const grid = root.querySelector('[data-app-grid]');
    const cards = Array.from(
        root.querySelectorAll('[data-app-card]')
    );
    const count = root.querySelector('[data-app-count]');
    const empty = root.querySelector('[data-app-empty]');
    const resetButtons = root.querySelectorAll('[data-app-reset]');
    const viewButtons = root.querySelectorAll('[data-app-view]');

    const normalize = (value = '') => {
        return String(value)
            .trim()
            .toLowerCase()
            .replace(/\s+/g, ' ');
    };

    const updateUrl = () => {
        const url = new URL(window.location.href);

        const values = {
            q: search?.value.trim() || '',
            category: category?.value || '',
            role: role?.value || '',
        };

        Object.entries(values).forEach(([key, value]) => {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        window.history.replaceState({}, '', url);
    };

    const filterCards = () => {
        const query = normalize(search?.value);
        const selectedCategory = normalize(category?.value);
        const selectedRole = normalize(role?.value);

        let visible = 0;

        cards.forEach((card) => {
            const matchesSearch =
                !query
                || normalize(card.dataset.search).includes(query);

            const matchesCategory =
                !selectedCategory
                || normalize(card.dataset.category)
                    === selectedCategory;

            const matchesRole =
                !selectedRole
                || normalize(card.dataset.roles)
                    .split(' ')
                    .includes(selectedRole);

            const show =
                matchesSearch
                && matchesCategory
                && matchesRole;

            card.hidden = !show;

            if (show) {
                visible += 1;
            }
        });

        if (count) {
            count.textContent = String(visible);
        }

        if (empty) {
            empty.hidden = visible !== 0;
        }

        if (grid) {
            grid.hidden = visible === 0;
        }

        updateUrl();
    };

    let timer;

    search?.addEventListener('input', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(filterCards, 90);
    });

    category?.addEventListener('change', filterCards);
    role?.addEventListener('change', filterCards);

    resetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            if (search) {
                search.value = '';
            }

            if (category) {
                category.value = '';
            }

            if (role) {
                role.value = '';
            }

            filterCards();
            search?.focus();
        });
    });

    const setView = (view) => {
        const list = view === 'list';

        grid?.classList.toggle('is-list', list);

        viewButtons.forEach((button) => {
            const active = button.dataset.appView === view;

            button.classList.toggle('is-active', active);
            button.setAttribute(
                'aria-pressed',
                active ? 'true' : 'false'
            );
        });

        try {
            window.localStorage.setItem(
                'studybuddy-app-view',
                view
            );
        } catch (error) {
            // Storage is optional.
        }
    };

    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setView(button.dataset.appView || 'grid');
        });
    });

    let initialView = 'grid';

    try {
        initialView =
            window.localStorage.getItem('studybuddy-app-view')
            || 'grid';
    } catch (error) {
        initialView = 'grid';
    }

    setView(initialView);
    filterCards();
})();
