(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const shell = document.querySelector('.sb-bt-nav');
        const button = document.querySelector('.sb-bt-menu-btn');
        if (shell && button) {
            button.addEventListener('click', () => {
                const open = shell.classList.toggle('is-open');
                button.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }
        document.addEventListener('click', (event) => {
            document.querySelectorAll('.sb-bt-more[open]').forEach((details) => {
                if (!details.contains(event.target)) details.removeAttribute('open');
            });
        });
    });
})();
