(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-cinematic-stage]').forEach((stage) => {
        const movers = stage.querySelectorAll('[data-depth]');

        if (reducedMotion) return;

        stage.addEventListener('pointermove', (event) => {
            const rect = stage.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) - 0.5;
            const y = ((event.clientY - rect.top) / rect.height) - 0.5;

            movers.forEach((item) => {
                const depth = Number(item.dataset.depth || 0);
                item.style.transform = `translate(${x * depth}px, ${y * depth}px)`;
            });
        });

        stage.addEventListener('pointerleave', () => {
            movers.forEach((item) => {
                item.style.transform = '';
            });
        });
    });

    document.querySelectorAll('[data-more]').forEach((wrap) => {
        const button = wrap.querySelector('[data-more-button]');
        if (!button) return;

        button.addEventListener('click', () => {
            const isOpen = wrap.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', (event) => {
            if (!wrap.contains(event.target)) {
                wrap.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    });

    document.querySelectorAll('[data-account-menu]').forEach((wrap) => {
        const button = wrap.querySelector('[data-account-button]');
        if (!button) return;

        button.addEventListener('click', () => {
            const isOpen = wrap.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', (event) => {
            if (!wrap.contains(event.target)) {
                wrap.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    });

    if (!reducedMotion && !document.querySelector('.sb-play-bubble')) {
        const bubble = document.createElement('aside');
        bubble.className = 'sb-play-bubble';
        bubble.innerHTML = `
            <div class="sb-play-bubble-head">
                <span class="sb-play-bubble-avatar">🎮</span>
                <strong>Pick a tiny quest?</strong>
                <button type="button" aria-label="Close StudyBuddy helper">×</button>
            </div>
            <p>Try one learning world and make one tiny win today.</p>
            <div class="sb-play-bubble-links">
                <a href="/apps/math-quest">Math</a>
                <a href="/apps/reading-garden">Reading</a>
                <a href="/apps/focus-forest">Focus</a>
                <a href="/community">Community</a>
            </div>
        `;

        document.body.appendChild(bubble);

        if (sessionStorage.getItem('studybuddy_play_bubble_closed') === '1') {
            bubble.hidden = true;
        }

        bubble.querySelector('button')?.addEventListener('click', () => {
            bubble.hidden = true;
            sessionStorage.setItem('studybuddy_play_bubble_closed', '1');
        });
    }
})();
