(() => {
    const data = window.studyBuddyRolesData || {};

    document.querySelectorAll('[data-roles-universe]').forEach((wrap) => {
        const kicker = wrap.querySelector('[data-role-kicker]');
        const title = wrap.querySelector('[data-role-title]');
        const body = wrap.querySelector('[data-role-body]');
        const img = wrap.querySelector('[data-role-image]');
        const tools = wrap.querySelector('[data-role-tools]');
        const cta = wrap.querySelector('[data-role-cta]');

        wrap.querySelectorAll('[data-role-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.roleTab;
                const role = data[key];
                if (!role) return;

                wrap.querySelectorAll('[data-role-tab]').forEach((item) => item.classList.remove('active'));
                button.classList.add('active');

                if (kicker) kicker.textContent = role.kicker || '';
                if (title) title.textContent = role.title || '';
                if (body) body.textContent = role.body || '';
                if (img) {
                    img.src = role.image || '';
                    img.alt = `${role.label || 'StudyBuddy'} role visual`;
                }
                if (cta) cta.href = role.cta || '/dashboard';

                if (tools) {
                    tools.innerHTML = '';
                    (role.tools || []).forEach((tool) => {
                        const chip = document.createElement('strong');
                        chip.textContent = tool;
                        tools.appendChild(chip);
                    });
                }
            });
        });
    });
})();
