(() => {
    document.querySelectorAll('[data-hub-card], .sb-hub-hero, .sb-page-hero, .public-profile-hero').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });
})();
