(() => {
    document.querySelectorAll('[data-info-card]').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
            card.style.setProperty('--my', `${event.clientY - rect.top}px`);
        });
    });

    const tabs = Array.from(document.querySelectorAll('[data-role-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-role-panel]'));

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const key = tab.dataset.roleTab;

            tabs.forEach((item) => {
                item.classList.toggle('active', item.dataset.roleTab === key);
            });

            panels.forEach((panel) => {
                panel.classList.toggle('active', panel.dataset.rolePanel === key);
            });
        });
    });
})();
