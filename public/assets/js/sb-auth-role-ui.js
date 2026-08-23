(() => {
    const roleSelect = document.querySelector('#sb-auth-role');
    if (!roleSelect) return;

    const sync = () => {
        const cleanRole = roleSelect.value || 'student';

        document.querySelectorAll('[data-panel]').forEach((panel) => {
            panel.classList.toggle('is-hidden', panel.dataset.panel !== cleanRole);
        });

        document.querySelectorAll('[data-role-preview]').forEach((card) => {
            card.classList.toggle('is-active', card.dataset.rolePreview === cleanRole);
        });

        document.querySelectorAll('[data-student-field]').forEach((field) => {
            field.style.display = cleanRole === 'parent' ? 'none' : '';
        });
    };

    roleSelect.addEventListener('change', sync);
    sync();
})();
