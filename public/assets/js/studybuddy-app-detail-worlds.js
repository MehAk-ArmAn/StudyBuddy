(() => {
    document.querySelectorAll('[data-world-card], [data-world-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    document.querySelectorAll('[data-world-tilt]').forEach((card) => {
        card.addEventListener('mousemove', (event) => {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - .5) * 8;
            const y = ((event.clientY - rect.top) / rect.height - .5) * -8;
            card.style.transform = `translateY(-6px) rotateX(${y}deg) rotateY(${x}deg)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
})();
