(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('[data-sb-universe-nav]');
        const toggle = document.querySelector('.sb-universe-toggle');

        if (nav && toggle) {
            toggle.addEventListener('click', () => {
                const open = nav.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.addEventListener('click', (event) => {
            document.querySelectorAll('.sb-universe-more[open]').forEach((details) => {
                if (!details.contains(event.target)) {
                    details.removeAttribute('open');
                }
            });
        });

        document.querySelectorAll('.sb-footer-column a, .sb-footer-pills a, .sb-footer-socials a').forEach((link) => {
            link.addEventListener('pointermove', (event) => {
                const rect = link.getBoundingClientRect();
                link.style.setProperty('--mx', `${event.clientX - rect.left}px`);
                link.style.setProperty('--my', `${event.clientY - rect.top}px`);
            });
        });
    });
})();
