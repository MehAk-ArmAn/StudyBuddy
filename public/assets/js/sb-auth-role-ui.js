(() => {
    function normalizeRole(value) { return String(value || 'student').toLowerCase().replace(/\s+/g, '_').replace(/-/g, '_'); }
    function setRoleUI(role) {
        const cleanRole = normalizeRole(role);
        document.querySelectorAll('[data-panel]').forEach((panel) => panel.classList.toggle('is-hidden', panel.dataset.panel !== cleanRole));
        document.querySelectorAll('[data-student-field]').forEach((field) => {
            field.classList.toggle('is-hidden', cleanRole === 'parent');
            field.querySelectorAll('input, select, textarea').forEach((input) => { if (cleanRole === 'parent') { input.removeAttribute('required'); input.value = ''; } });
        });
        document.querySelectorAll('[data-role-preview]').forEach((card) => card.classList.toggle('is-active', card.dataset.rolePreview === cleanRole));
    }
    document.addEventListener('DOMContentLoaded', () => {
        const roleSelect = document.getElementById('sb-auth-role') || document.querySelector('select[name="role"], select[name="account_type"]');
        if (!roleSelect) return;
        setRoleUI(roleSelect.value);
        roleSelect.addEventListener('change', () => setRoleUI(roleSelect.value));
    });
})();
