document.addEventListener('DOMContentLoaded', () => {
    const THEME_KEY = 'studybuddy_theme';
    const QUEST_KEY = 'studybuddy_saved_quest';
    const themeSlugs = [
        'cosmic-dolphin',
        'bts-purple-galaxy',
        'ocean-focus',
        'candy-pop',
        'forest-calm',
        'night-study',
        'solar-gold',
        'neon-gamer',
    ];

    const normalizeTheme = (theme) => themeSlugs.includes(theme) ? theme : 'cosmic-dolphin';

    const applyTheme = (theme) => {
        const nextTheme = normalizeTheme(theme);
        document.body.classList.forEach((className) => {
            if (className.startsWith('theme-')) {
                document.body.classList.remove(className);
            }
        });

        document.body.classList.add(`theme-${nextTheme}`);
        document.body.dataset.studybuddyTheme = nextTheme;
        localStorage.setItem(THEME_KEY, nextTheme);
    };

    const serverTheme = normalizeTheme(document.body.dataset.studybuddyTheme || '');
    const savedTheme = normalizeTheme(localStorage.getItem(THEME_KEY) || serverTheme);
    applyTheme(savedTheme);

    const themeSelect = document.querySelector('[data-theme-select]');
    const themeCards = document.querySelectorAll('[data-theme-choice]');

    const setThemeChoice = (theme) => {
        const nextTheme = normalizeTheme(theme);

        if (themeSelect) {
            themeSelect.value = nextTheme;
        }

        themeCards.forEach((card) => {
            card.classList.toggle('active', card.dataset.themeChoice === nextTheme);
        });

        applyTheme(nextTheme);
    };

    if (themeSelect) {
        setThemeChoice(themeSelect.value || savedTheme);

        themeSelect.addEventListener('change', () => {
            setThemeChoice(themeSelect.value);
        });
    }

    themeCards.forEach((card) => {
        card.addEventListener('click', () => {
            setThemeChoice(card.dataset.themeChoice);
        });
    });

    const showToast = (message) => {
        let toast = document.querySelector('[data-studybuddy-toast]');

        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'studybuddy-toast';
            toast.setAttribute('data-studybuddy-toast', '');
            toast.setAttribute('role', 'status');
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.add('show');

        window.clearTimeout(toast._hideTimer);
        toast._hideTimer = window.setTimeout(() => {
            toast.classList.remove('show');
        }, 2600);
    };

    const renderSavedQuest = () => {
        const dashboard = document.querySelector('.dash-wrap');
        if (!dashboard || dashboard.querySelector('[data-saved-quest-card]')) {
            return;
        }

        let savedQuest = null;

        try {
            savedQuest = JSON.parse(localStorage.getItem(QUEST_KEY) || 'null');
        } catch (error) {
            savedQuest = null;
        }

        if (!savedQuest || !savedQuest.title) {
            return;
        }

        const card = document.createElement('article');
        card.className = 'auth-panel saved-quest-card';
        card.setAttribute('data-saved-quest-card', '');

        card.innerHTML = `
            <div>
                <p class="eyebrow">My Quest</p>
                <h2>${savedQuest.title}</h2>
                <p>${savedQuest.subtitle || 'Saved from the StudyBuddy launcher.'}</p>
            </div>
            <div class="saved-quest-actions">
                <a class="btn" href="${savedQuest.url || '/apps'}">Open quest</a>
                <button class="btn btn-ghost" type="button" data-clear-saved-quest>Clear</button>
            </div>
        `;

        const hero = dashboard.querySelector('.dash-hero');
        if (hero && hero.nextSibling) {
            hero.parentNode.insertBefore(card, hero.nextSibling);
        } else {
            dashboard.prepend(card);
        }

        card.querySelector('[data-clear-saved-quest]')?.addEventListener('click', () => {
            localStorage.removeItem(QUEST_KEY);
            card.remove();
            showToast('Saved quest cleared.');
        });
    };

    renderSavedQuest();

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
        const saveButton = modal?.querySelector('[data-save-mission]');
        const saveNote = modal?.querySelector('[data-mission-save-note]');

        let activeMission = null;

        const closeMission = () => {
            if (!modal) {
                return;
            }

            modal.hidden = true;
            document.body.classList.remove('modal-open');

            if (saveButton) {
                saveButton.textContent = 'Save to My Quest';
                saveButton.disabled = false;
            }

            if (saveNote) {
                saveNote.hidden = true;
                saveNote.textContent = '';
            }
        };

        launcher.querySelectorAll('[data-open-app]').forEach((button) => {
            button.addEventListener('click', () => {
                const card = button.closest('[data-app-card]');

                if (!card || !modal) {
                    return;
                }

                activeMission = {
                    title: card.dataset.title || 'StudyBuddy Mission',
                    subtitle: card.dataset.subtitle || 'Choose a focused learning moment.',
                    body: card.dataset.body || 'Preview this learning world and save it to your daily quest.',
                    image: card.dataset.image || '',
                    url: card.dataset.url || '/apps',
                    savedAt: new Date().toISOString(),
                };

                if (title) title.textContent = activeMission.title;
                if (subtitle) subtitle.textContent = activeMission.subtitle;
                if (body) body.textContent = activeMission.body;

                if (image) {
                    if (activeMission.image) {
                        image.src = activeMission.image;
                        image.hidden = false;
                    } else {
                        image.hidden = true;
                    }
                }

                modal.hidden = false;
                document.body.classList.add('modal-open');
            });
        });

        saveButton?.addEventListener('click', () => {
            if (!activeMission) {
                return;
            }

            localStorage.setItem(QUEST_KEY, JSON.stringify(activeMission));
            saveButton.textContent = 'Saved to My Quest ✓';
            saveButton.disabled = true;

            if (saveNote) {
                saveNote.hidden = false;
                saveNote.textContent = 'Saved. Open your dashboard to see this quest at the top.';
            }

            showToast(`${activeMission.title} saved to My Quest.`);
        });

        modal?.querySelectorAll('[data-close-mission]').forEach((button) => {
            button.addEventListener('click', closeMission);
        });

        modal?.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeMission();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal && !modal.hidden) {
                closeMission();
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
            independent: 'Your dashboard becomes a self-guided study cockpit with goals, focus mode, notes, and smart recommendations.',
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
            const pick = role === 'parent'
                ? 'Focus Forest routine'
                : role === 'teacher'
                    ? 'Quiz Galaxy review room'
                    : role === 'independent'
                        ? 'Planner City study sprint'
                        : 'Math Quest warm-up';

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
