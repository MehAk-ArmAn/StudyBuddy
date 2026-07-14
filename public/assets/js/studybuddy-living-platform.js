(() => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-search-root]').forEach((root) => {
        const input = root.querySelector('[data-search-input]');
        const panel = root.querySelector('[data-search-results]');
        const endpoint = root.dataset.searchEndpoint;

        if (!input || !panel || !endpoint) return;

        let timer;

        const render = (items) => {
            if (!items.length) {
                panel.innerHTML = '<div class="sb-search-empty-mini">No quick results yet. Press Enter to search.</div>';
                panel.hidden = false;
                return;
            }

            panel.innerHTML = items.map((item) => {
                const media = item.image
                    ? `<img src="${item.image}" alt="">`
                    : `<span class="icon">${item.icon || '✨'}</span>`;

                return `
                    <a class="sb-search-hit" href="${item.url}">
                        ${media}
                        <div>
                            <small>${item.type || 'Result'}</small>
                            <strong>${item.title || 'StudyBuddy result'}</strong>
                            <p>${item.description || 'Open this result.'}</p>
                        </div>
                    </a>
                `;
            }).join('');

            panel.hidden = false;
        };

        const search = () => {
            const q = input.value.trim();

            if (q.length < 1) {
                panel.hidden = true;
                panel.innerHTML = '';
                return;
            }

            fetch(`${endpoint}?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then((res) => res.json())
                .then((data) => render(data.results || []))
                .catch(() => {
                    panel.innerHTML = '<div class="sb-search-empty-mini">Search is warming up. Press Enter.</div>';
                    panel.hidden = false;
                });
        };

        input.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(search, 120);
        });

        input.addEventListener('focus', search);

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) panel.hidden = true;
        });
    });

    const cards = document.querySelectorAll('[data-living-card], .sb-hub-card, .profile-form-card, .community-card, .mini-app-card');
    cards.forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    const sections = document.querySelectorAll('[data-animate-section]');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) entry.target.classList.add('is-visible');
            });
        }, { threshold: 0.14 });

        sections.forEach((section) => observer.observe(section));
    } else {
        sections.forEach((section) => section.classList.add('is-visible'));
    }

    const communityCards = Array.from(document.querySelectorAll('[data-community-card]'));
    const communitySearch = document.querySelector('[data-community-search]');
    const communityRole = document.querySelector('[data-community-role]');
    const communityTheme = document.querySelector('[data-community-theme]');
    const communityEmpty = document.querySelector('[data-community-empty]');

    const applyCommunityFilters = () => {
        const q = (communitySearch?.value || '').trim().toLowerCase();
        const role = communityRole?.value || 'all';
        const theme = communityTheme?.value || 'all';
        let visible = 0;

        communityCards.forEach((card) => {
            const okSearch = !q || (card.dataset.search || '').includes(q);
            const okRole = role === 'all' || card.dataset.role === role;
            const okTheme = theme === 'all' || card.dataset.theme === theme;
            const show = okSearch && okRole && okTheme;

            card.hidden = !show;
            if (show) visible++;
        });

        if (communityEmpty) communityEmpty.hidden = visible !== 0;
    };

    [communitySearch, communityRole, communityTheme].forEach((input) => {
        if (!input) return;
        input.addEventListener('input', applyCommunityFilters);
        input.addEventListener('change', applyCommunityFilters);
    });

    applyCommunityFilters();

    if (!reduceMotion && !document.querySelector('.sb-floating-buddy')) {
        const buddy = document.createElement('aside');
        buddy.className = 'sb-floating-buddy';
        buddy.innerHTML = `
            <div class="sb-floating-buddy-head">
                <span class="sb-floating-buddy-avatar">✨</span>
                <strong>Need a tiny win?</strong>
                <button type="button" aria-label="Close StudyBuddy helper">×</button>
            </div>
            <p>Pick one app world and start with a mini mission.</p>
            <div class="sb-floating-buddy-links">
                <a href="/apps/math-quest">Math Quest</a>
                <a href="/apps/reading-garden">Reading</a>
                <a href="/apps/focus-forest">Focus</a>
                <a href="/community">Community</a>
            </div>
        `;

        document.body.appendChild(buddy);

        buddy.querySelector('button')?.addEventListener('click', () => {
            buddy.hidden = true;
            sessionStorage.setItem('studybuddy_buddy_closed', '1');
        });

        if (sessionStorage.getItem('studybuddy_buddy_closed') === '1') {
            buddy.hidden = true;
        }
    }
})();
