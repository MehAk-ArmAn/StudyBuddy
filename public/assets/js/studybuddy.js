document.addEventListener('DOMContentLoaded', () => {
    const revealItems = document.querySelectorAll('.reveal-on-load');

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -12% 0px' });

        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }

    const nav = document.querySelector('[data-studybuddy-navbar]');
    const navToggle = document.querySelector('[data-studybuddy-nav-toggle]');

    if (nav && navToggle) {
        navToggle.addEventListener('click', () => {
            const isOpen = nav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }

    document.querySelectorAll('.tilt-card').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width - 0.5) * 9;
            const y = ((event.clientY - rect.top) / rect.height - 0.5) * -9;
            card.style.transform = `perspective(900px) rotateY(${x}deg) rotateX(${y}deg) translateY(-3px)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });

    const feedback = document.querySelector('.answer-feedback');

    document.querySelectorAll('.answer-grid button').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.answer-grid button').forEach((option) => {
                option.disabled = true;
            });

            if (button.dataset.correct === 'true') {
                button.classList.add('correct');
                if (feedback) feedback.textContent = 'Correct! Buddy earned +25 XP and a coin burst.';
            } else {
                button.classList.add('wrong');
                if (feedback) feedback.textContent = 'Almost! The portal hint is 8 groups of 7 stars.';
            }
        });
    });
});
