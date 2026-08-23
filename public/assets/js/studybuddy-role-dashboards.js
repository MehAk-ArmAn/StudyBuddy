(() => {
    document.querySelectorAll('[data-role-card]').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    // Copying the Connect Code by hand is the step people get wrong, so the
    // dashboard offers it as an action rather than asking them to select it.
    const copyButton = document.querySelector('[data-copy-connect-code]');
    const codeEl = document.querySelector('[data-connect-code]');

    if (copyButton && codeEl) {
        const original = copyButton.textContent;
        let resetTimer;

        copyButton.addEventListener('click', async () => {
            const code = (codeEl.textContent || '').trim();

            try {
                await navigator.clipboard.writeText(code);
                copyButton.textContent = 'Copied';
            } catch (error) {
                // Clipboard access can be refused. Select the code instead so
                // it can still be copied with the keyboard.
                const range = document.createRange();
                range.selectNodeContents(codeEl);
                const selection = window.getSelection();
                selection?.removeAllRanges();
                selection?.addRange(range);
                copyButton.textContent = 'Press Ctrl+C';
            }

            window.clearTimeout(resetTimer);
            resetTimer = window.setTimeout(() => {
                copyButton.textContent = original;
            }, 2400);
        });
    }
})();
