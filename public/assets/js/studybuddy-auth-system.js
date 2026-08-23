(() => {
    const roleSelect = document.querySelector('#sb-auth-role');
    const panels = Array.from(document.querySelectorAll('[data-panel]'));
    const previews = Array.from(document.querySelectorAll('[data-role-preview]'));
    const studentField = document.querySelector('[data-student-field]');

    const syncRole = () => {
        const role = roleSelect?.value || 'student';

        panels.forEach((panel) => {
            panel.classList.toggle('active', panel.dataset.panel === role);
        });

        previews.forEach((preview) => {
            preview.classList.toggle('active', preview.dataset.rolePreview === role);
        });

        if (studentField) {
            studentField.style.display = role === 'parent' ? 'none' : '';
        }
    };

    if (roleSelect) {
        roleSelect.addEventListener('change', syncRole);
        syncRole();
    }
})();
