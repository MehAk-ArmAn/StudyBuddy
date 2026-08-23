(() => {
    const buttons = document.querySelectorAll('[data-copy-path]');

    if (!buttons.length) return;

    const copyText = async (value) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(value);
            return;
        }

        const input = document.createElement('textarea');
        input.value = value;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
    };

    buttons.forEach((button) => {
        button.addEventListener('click', async () => {
            const path = button.dataset.copyPath || '';
            if (!path) return;

            const original = button.querySelector('span')?.textContent || path;

            try {
                await copyText(path);
                button.classList.add('is-copied');
                const label = button.querySelector('span');
                if (label) label.textContent = 'Copied — paste into an Image Path field';

                window.setTimeout(() => {
                    button.classList.remove('is-copied');
                    if (label) label.textContent = original;
                }, 1800);
            } catch (error) {
                window.prompt('Copy this image path:', path);
            }
        });
    });
})();
