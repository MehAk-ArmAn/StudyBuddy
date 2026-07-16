(() => {
    const shell = document.querySelector('[data-shell]');
    if (!shell) return;

    const toggle = shell.querySelector('[data-nav-toggle]');
    const panel = shell.querySelector('[data-mobile-panel]');
    const more = shell.querySelector('[data-more]');
    const moreButton = shell.querySelector('[data-more-button]');

    if (toggle && panel) {
        toggle.addEventListener('click', () => {
            const open = panel.classList.toggle('open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    if (more && moreButton) {
        moreButton.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = more.classList.toggle('open');
            moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });

        document.addEventListener('click', () => {
            more.classList.remove('open');
            moreButton.setAttribute('aria-expanded', 'false');
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                more.classList.remove('open');
                moreButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();


// === StudyBuddy interactive shell polish ===
(() => {
    const shell = document.querySelector('.sb-advanced-shell');
    const nav = document.querySelector('.sb-advanced-nav');

    const setScrolled = () => {
        if (!shell) return;
        shell.classList.toggle('is-scrolled', window.scrollY > 18);
    };

    setScrolled();
    window.addEventListener('scroll', setScrolled, { passive: true });

    document.querySelectorAll('.sb-footer-action-card, .sb-footer-value-strip article, .sb-footer-group').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    document.querySelectorAll('.sb-nav-search button, .sb-footer-newsletter button, .sb-footer-action-card a, .sb-nav-actions a, .sb-mobile-actions a').forEach((button) => {
        button.addEventListener('click', (event) => {
            const oldRipple = button.querySelector('.sb-ripple');
            if (oldRipple) oldRipple.remove();

            const rect = button.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'sb-ripple';
            ripple.style.left = `${event.clientX - rect.left}px`;
            ripple.style.top = `${event.clientY - rect.top}px`;

            button.appendChild(ripple);
            setTimeout(() => ripple.remove(), 650);
        });
    });

    if (nav) {
        nav.addEventListener('pointermove', (event) => {
            const rect = nav.getBoundingClientRect();
            nav.style.setProperty('--nav-x', `${event.clientX - rect.left}px`);
            nav.style.setProperty('--nav-y', `${event.clientY - rect.top}px`);
        });
    }
})();
// === End StudyBuddy interactive shell polish ===
