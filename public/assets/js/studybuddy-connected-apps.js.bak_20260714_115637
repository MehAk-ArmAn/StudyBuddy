(() => {
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
            const matchesRole = selectedRole === 'all' || (card.dataset.roles || '').split(' ').includes(selectedRole);
            const show = matchesSearch && matchesCategory && matchesRole;

            card.hidden = !show;
            if (show) visible++;
        });

        if (empty) empty.hidden = visible !== 0;
    };

    [search, category, role].forEach((el) => {
        if (el) el.addEventListener('input', applyFilters);
        if (el) el.addEventListener('change', applyFilters);
    });

    applyFilters();

    document.querySelectorAll('[data-app-card], [data-world-card], [data-world-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    document.querySelectorAll('[data-world-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - .5) * 8;
            const y = ((event.clientY - rect.top) / rect.height - .5) * -8;
            card.style.transform = `translateY(-6px) rotateX(${y}deg) rotateY(${x}deg)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
})();

// === StudyBuddy app pages alive polish ===
(() => {
    const root = document.querySelector('.sb-connected-apps, .sb-app-detail-world');
    if (!root) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const readTheme = () => {
        const styles = getComputedStyle(root);
        return {
            one: styles.getPropertyValue('--app-one').trim() || '#7c3cff',
            two: styles.getPropertyValue('--app-two').trim() || '#246bff',
            three: styles.getPropertyValue('--app-three').trim() || '#22d3ee'
        };
    };

    const theme = readTheme();

    if (!document.querySelector('.sb-life-layer')) {
        const layer = document.createElement('div');
        layer.className = 'sb-life-layer';
        layer.setAttribute('aria-hidden', 'true');

        const count = reduceMotion ? 14 : 52;

        for (let i = 0; i < count; i++) {
            const particle = document.createElement('span');
            particle.className = 'sb-life-particle';

            const size = Math.random() * 7 + 3;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 70 - 35).toFixed(1);
            const dy = (Math.random() * 70 - 35).toFixed(1);
            const duration = Math.random() * 7 + 6;
            const delay = Math.random() * -8;
            const opacity = Math.random() * .38 + .28;
            const rotate = Math.random() * 360;

            particle.style.setProperty('--x', `${x}%`);
            particle.style.setProperty('--y', `${y}%`);
            particle.style.setProperty('--s', `${size}px`);
            particle.style.setProperty('--dx', `${dx}px`);
            particle.style.setProperty('--dy', `${dy}px`);
            particle.style.setProperty('--d', `${duration}s`);
            particle.style.setProperty('--delay', `${delay}s`);
            particle.style.setProperty('--o', opacity.toFixed(2));
            particle.style.setProperty('--r', `${rotate}deg`);
            particle.style.setProperty('--app-one', theme.one);
            particle.style.setProperty('--app-two', theme.two);
            particle.style.setProperty('--app-three', theme.three);

            layer.appendChild(particle);
        }

        document.body.prepend(layer);
    }

    document.querySelectorAll('.sb-apps-landing, .sb-detail-hero').forEach((hero) => {
        hero.addEventListener('pointermove', (event) => {
            const rect = hero.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width * 100).toFixed(1);
            const y = ((event.clientY - rect.top) / rect.height * 100).toFixed(1);
            hero.style.setProperty('--hero-x', `${x}%`);
            hero.style.setProperty('--hero-y', `${y}%`);
        });
    });

    document.querySelectorAll('.sb-connected-card, .sb-detail-outcomes article, .sb-detail-missions article, .sb-related-grid a, .sb-apps-count-card, .sb-detail-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });
})();
// === End StudyBuddy app pages alive polish ===


// === StudyBuddy no-glitch magical user polish ===
(() => {
    const root = document.querySelector('.sb-connected-apps, .sb-app-detail-world');
    if (!root) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Remove old body-level magic layer if it exists from previous version
    document.querySelectorAll('body > .sb-life-layer').forEach((oldLayer) => oldLayer.remove());

    // Put magic inside the actual app page only
    let layer = root.querySelector(':scope > .sb-life-layer');
    if (!layer) {
        layer = document.createElement('div');
        layer.className = 'sb-life-layer';
        layer.setAttribute('aria-hidden', 'true');
        root.prepend(layer);
    }

    if (!layer.dataset.ready) {
        const styles = getComputedStyle(root);
        const one = styles.getPropertyValue('--app-one').trim() || '#7c3cff';
        const two = styles.getPropertyValue('--app-two').trim() || '#246bff';
        const three = styles.getPropertyValue('--app-three').trim() || '#22d3ee';

        const count = reduceMotion ? 0 : 72;

        for (let i = 0; i < count; i++) {
            const particle = document.createElement('span');
            particle.className = 'sb-life-particle';

            const size = Math.random() * 6 + 2.5;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dx = (Math.random() * 80 - 40).toFixed(1);
            const dy = (Math.random() * 80 - 40).toFixed(1);
            const duration = Math.random() * 8 + 7;
            const delay = Math.random() * -10;
            const opacity = Math.random() * .34 + .24;
            const rotate = Math.random() * 360;

            particle.style.setProperty('--x', `${x}%`);
            particle.style.setProperty('--y', `${y}%`);
            particle.style.setProperty('--s', `${size}px`);
            particle.style.setProperty('--dx', `${dx}px`);
            particle.style.setProperty('--dy', `${dy}px`);
            particle.style.setProperty('--d', `${duration}s`);
            particle.style.setProperty('--delay', `${delay}s`);
            particle.style.setProperty('--o', opacity.toFixed(2));
            particle.style.setProperty('--r', `${rotate}deg`);
            particle.style.setProperty('--app-one', one);
            particle.style.setProperty('--app-two', two);
            particle.style.setProperty('--app-three', three);

            layer.appendChild(particle);
        }

        layer.dataset.ready = 'true';
    }

    // Gentle magnetic movement on cards, not glitchy jump
    document.querySelectorAll('.sb-connected-card, .sb-detail-art').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            if (reduceMotion) return;

            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - .5;
            const y = (event.clientY - rect.top) / rect.height - .5;

            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);

            if (card.classList.contains('sb-detail-art')) {
                card.style.transform = `translateY(-4px) rotateX(${(-y * 5).toFixed(2)}deg) rotateY(${(x * 5).toFixed(2)}deg)`;
            }
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });
})();
// === End StudyBuddy no-glitch magical user polish ===
