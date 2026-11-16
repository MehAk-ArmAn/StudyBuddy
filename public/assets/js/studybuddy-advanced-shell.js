(() => {
    const shell = document.querySelector('[data-shell]');
    if (!shell) return;

    const mobileToggle = shell.querySelector('[data-nav-toggle]');
    const mobilePanel = shell.querySelector('[data-mobile-panel]');
    const more = shell.querySelector('[data-more]');
    const moreButton = shell.querySelector('[data-more-button]');
    const accountMenu = shell.querySelector('[data-account-menu]');
    const accountButton = shell.querySelector('[data-account-button]');
    const nav = shell.querySelector('.sb-advanced-nav');

    const closeMore = () => {
        if (!more || !moreButton) return;
        more.classList.remove('open');
        moreButton.setAttribute('aria-expanded', 'false');
    };

    const closeAccount = () => {
        if (!accountMenu || !accountButton) return;
        accountMenu.classList.remove('open');
        accountButton.setAttribute('aria-expanded', 'false');
    };

    if (mobileToggle && mobilePanel) {
        mobileToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = mobilePanel.classList.toggle('open');
            mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    if (more && moreButton) {
        moreButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeAccount();
            const open = more.classList.toggle('open');
            moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        more.addEventListener('click', (event) => event.stopPropagation());
    }

    if (accountMenu && accountButton) {
        accountButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeMore();
            const open = accountMenu.classList.toggle('open');
            accountButton.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        accountMenu.addEventListener('click', (event) => event.stopPropagation());
    }

    document.addEventListener('click', () => { closeMore(); closeAccount(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { closeMore(); closeAccount(); } });

    if (nav) {
        nav.addEventListener('pointermove', (event) => {
            const rect = nav.getBoundingClientRect();
            nav.style.setProperty('--nav-x', `${event.clientX - rect.left}px`);
            nav.style.setProperty('--nav-y', `${event.clientY - rect.top}px`);
        });
    }
})();
