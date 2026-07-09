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