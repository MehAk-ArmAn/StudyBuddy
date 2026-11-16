(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('.sb-consistent-nav');
        const button = document.querySelector('.sb-consistent-menu');

        if (nav && button) {
            button.addEventListener('click', () => {
                const open = nav.classList.toggle('is-open');
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.addEventListener('click', (event) => {
            document.querySelectorAll('.sb-consistent-more[open]').forEach((details) => {
                if (!details.contains(event.target)) {
                    details.removeAttribute('open');
                }
            });
        });
    });
})();
