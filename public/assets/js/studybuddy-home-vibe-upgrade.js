(() => {
    const data = {
        learner: {
            title: 'Learner Profile',
            text: 'Build a profile around favourite app worlds, learning goals, progress style, badges, colours, and public showcase settings.',
            image: 'https://github.com/MehAk-ArmAn/StudyBuddy-Imgs/blob/main/hero/hero-dolphin-book.png?raw=true'
        },
        parent: {
            title: 'Parent Support',
            text: 'Parents can support connected learners through consent-based progress views, recent activity, and calm safety-first guidance.',
            image: 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-parents.png'
        },
        teacher: {
            title: 'Teacher Studio',
            text: 'Teachers get readable classrooms, verified student rosters, assignments, quizzes, and activity signals in one place.',
            image: 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-teachers.png'
        },
        independent: {
            title: 'Independent Mode',
            text: 'Self-paced learners can set goals, use app worlds, track tiny wins, and grow a profile without classroom pressure.',
            image: 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-learning.png'
        },
        rewards: {
            title: 'Badges and Progress',
            text: 'Points, badges, favourite worlds, profile colours, and community showcases make learning feel personal and motivating.',
            image: 'https://raw.githubusercontent.com/MehAk-ArmAn/StudyBuddy-Imgs/main/homepage-paths/path-apps.png'
        }
    };

    document.querySelectorAll('[data-vibe-upgrade]').forEach((wrap) => {
        const title = wrap.querySelector('[data-vibe-title]');
        const text = wrap.querySelector('[data-vibe-text]');
        const img = wrap.querySelector('[data-vibe-image]');

        wrap.querySelectorAll('[data-vibe-tab]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.vibeTab;
                const item = data[key];
                if (!item) return;

                wrap.querySelectorAll('[data-vibe-tab]').forEach((x) => x.classList.remove('is-active'));
                btn.classList.add('is-active');

                if (title) title.textContent = item.title;
                if (text) text.textContent = item.text;
                if (img) img.src = item.image;
            });
        });
    });
})();
