document.addEventListener('DOMContentLoaded', () => {
    const launcher = document.querySelector('[data-app-launcher]');

    if (launcher) {
        const filterButtons = launcher.querySelectorAll('[data-app-filter]');
        const cards = launcher.querySelectorAll('[data-app-card]');

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const filter = button.dataset.appFilter;

                filterButtons.forEach((item) => item.classList.remove('active'));
                button.classList.add('active');

                cards.forEach((card) => {
                    const visible = filter === 'all' || card.dataset.category === filter;
                    card.toggleAttribute('hidden', !visible);
                });
            });
        });

        const modal = document.querySelector('[data-mission-modal]');
        const title = modal?.querySelector('[data-mission-title]');
        const subtitle = modal?.querySelector('[data-mission-subtitle]');
        const body = modal?.querySelector('[data-mission-body]');
        const image = modal?.querySelector('[data-mission-image]');

        launcher.querySelectorAll('[data-open-app]').forEach((button) => {
            button.addEventListener('click', () => {
                const card = button.closest('[data-app-card]');
                if (!card || !modal) return;

                title.textContent = card.dataset.title || 'StudyBuddy Mission';
                subtitle.textContent = card.dataset.subtitle || 'Choose a focused learning moment.';
                body.textContent = card.dataset.body || 'Preview this learning world and save it to your daily quest.';

                if (card.dataset.image) {
                    image.src = card.dataset.image;
                    image.hidden = false;
                } else {
                    image.hidden = true;
                }

                modal.hidden = false;
                document.body.classList.add('modal-open');
            });
        });

        modal?.querySelectorAll('[data-close-mission]').forEach((button) => {
            button.addEventListener('click', () => {
                modal.hidden = true;
                document.body.classList.remove('modal-open');
            });
        });

        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.hidden = true;
                document.body.classList.remove('modal-open');
            }
        });
    }

    const roleDemo = document.querySelector('[data-role-demo]');
    if (roleDemo) {
        const output = roleDemo.querySelector('[data-role-output]');
        const roleText = {
            student: 'Your dashboard becomes a daily quest board with practice, focus, streaks, and friendly progress.',
            parent: 'Your dashboard becomes a calm family support hub with approvals, routines, and learning summaries.',
            teacher: 'Your dashboard becomes a classroom-friendly planning space with verified, limited learner support.',
            independent: 'Your dashboard becomes a self-guided study cockpit with goals, focus mode, notes, and smart recommendations.'
        };

        roleDemo.querySelectorAll('[data-role-choice]').forEach((button) => {
            button.addEventListener('click', () => {
                roleDemo.querySelectorAll('[data-role-choice]').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');
                output.textContent = roleText[button.dataset.roleChoice] || roleText.student;
            });
        });
    }

    const recommender = document.querySelector('[data-launcher-recommender]');
    if (recommender) {
        const copy = recommender.querySelector('[data-recommendation-copy]');
        let role = 'student';
        let time = '10';

        const render = () => {
            const pick = role === 'parent' ? 'Focus Forest routine' : role === 'teacher' ? 'Quiz Galaxy review room' : role === 'independent' ? 'Planner City study sprint' : 'Math Quest warm-up';
            copy.textContent = `Recommended: ${pick}. Start with a ${time}-minute mission and save it to your StudyBuddy path.`;
        };

        recommender.querySelectorAll('[data-recommend-role]').forEach((button) => {
            button.addEventListener('click', () => {
                role = button.dataset.recommendRole;
                recommender.querySelectorAll('[data-recommend-role]').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');
                render();
            });
        });

        recommender.querySelectorAll('[data-recommend-time]').forEach((button) => {
            button.addEventListener('click', () => {
                time = button.dataset.recommendTime;
                recommender.querySelectorAll('[data-recommend-time]').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');
                render();
            });
        });
    }
});
