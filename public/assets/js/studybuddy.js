document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.app-card, .dashboard-card, .reward-card, .glass-card');

    cards.forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - 0.5) * 8;
            const y = ((event.clientY - rect.top) / rect.height - 0.5) * -8;
            card.style.transform = `perspective(900px) rotateY(${x}deg) rotateX(${y}deg) translateY(-2px)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });

    document.querySelectorAll('.answer-grid button').forEach((button) => {
        button.addEventListener('click', () => {
            button.classList.add('selected-answer');
            button.textContent = button.textContent === '56' ? '56 ✨' : `${button.textContent} ↺`;
        }, { once: true });
    });
});
