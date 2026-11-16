(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('.sb-shell-nav');
        const button = document.querySelector('.sb-shell-menu');

        if (nav && button) {
            button.addEventListener('click', () => {
                const open = nav.classList.toggle('is-open');
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

        document.addEventListener('click', (event) => {
            document.querySelectorAll('.sb-shell-more[open]').forEach((details) => {
                if (!details.contains(event.target)) {
                    details.removeAttribute('open');
                }
            });
        });
    });
})();
