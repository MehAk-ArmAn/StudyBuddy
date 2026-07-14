
(() => {
    const page = document.querySelector('.sb-apps-final, .sb-app-detail-final, .sb-app-play-final');
    if (!page) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('body > .sb-life-layer').forEach((el) => el.remove());

    if (!reduceMotion && !page.querySelector('.sb-app-magic-layer')) {
        const layer = document.createElement('div');
        layer.className = 'sb-app-magic-layer';
        layer.setAttribute('aria-hidden', 'true');

        const styles = getComputedStyle(page);
        const one = styles.getPropertyValue('--app-one').trim() || '#7c3cff';
        const two = styles.getPropertyValue('--app-two').trim() || '#246bff';
        const three = styles.getPropertyValue('--app-three').trim() || '#22d3ee';

        const count = window.innerWidth < 640 ? 24 : 54;

        for (let i = 0; i < count; i++) {
            const dot = document.createElement('span');
            const size = Math.random() * 6 + 2.5;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 80 - 40).toFixed(1);
            const dy = (Math.random() * 80 - 40).toFixed(1);
            const duration = Math.random() * 8 + 7;
            const delay = Math.random() * -10;
            const opacity = Math.random() * .34 + .24;

            dot.style.setProperty('--x', `${x}%`);
            dot.style.setProperty('--y', `${y}%`);
            dot.style.setProperty('--s', `${size}px`);
            dot.style.setProperty('--dx', `${dx}px`);
            dot.style.setProperty('--dy', `${dy}px`);
            dot.style.setProperty('--d', `${duration}s`);
            dot.style.setProperty('--delay', `${delay}s`);
            dot.style.setProperty('--o', opacity.toFixed(2));
            dot.style.setProperty('--app-one', one);
            dot.style.setProperty('--app-two', two);
            dot.style.setProperty('--app-three', three);

            layer.appendChild(dot);
        }

        page.prepend(layer);
    }

    const cards = Array.from(document.querySelectorAll('[data-app-card]'));
    const search = document.querySelector('[data-sb-app-search]');
    const category = document.querySelector('[data-sb-app-filter]');
    const role = document.querySelector('[data-sb-role-filter]');
    const empty = document.querySelector('[data-sb-empty]');

    const applyFilters = () => {
        const q = (search?.value || '').trim().toLowerCase();
        const cat = category?.value || 'all';
        const selectedRole = role?.value || 'all';
        let visible = 0;

        cards.forEach((card) => {
            const matchesSearch = !q || (card.dataset.search || '').includes(q);
            const matchesCategory = cat === 'all' || card.dataset.category === cat;
            const roles = (card.dataset.roles || '').split(' ');
            const matchesRole = selectedRole === 'all' || roles.includes(selectedRole);
            const show = matchesSearch && matchesCategory && matchesRole;

            card.hidden = !show;
            if (show) visible++;
        });

        if (empty) empty.hidden = visible !== 0;
    };

    [search, category, role].forEach((el) => {
        if (!el) return;
        el.addEventListener('input', applyFilters);
        el.addEventListener('change', applyFilters);
    });

    applyFilters();

    document.querySelectorAll('[data-magic-card], .sb-apps-hero-final, .sb-detail-hero-final, .play-stage').forEach((el) => {
        el.addEventListener('pointermove', (event) => {
            const rect = el.getBoundingClientRect();
            el.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            el.style.setProperty('--my', `${event.clientY - rect.top}px`);
            el.style.setProperty('--hero-x', `${((event.clientX - rect.left) / rect.width * 100).toFixed(1)}%`);
            el.style.setProperty('--hero-y', `${((event.clientY - rect.top) / rect.height * 100).toFixed(1)}%`);
        });
    });

    document.querySelectorAll('.detail-art-stage, .play-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            if (reduceMotion) return;
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - .5;
            const y = (event.clientY - rect.top) / rect.height - .5;
            card.style.transform = `translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });
})();
