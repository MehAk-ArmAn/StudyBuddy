/* W4 progressive enhancement; old Blade remains whenever React does not mount. */
(() => {
  'use strict';
  if (!document.querySelector('.sbw4-page')) return;
  const update = () => {
    const world = document.querySelector('.sbw4-page .sbw3-home-flex[data-sb-react-active="mounted"]');
    const roles = document.querySelector('.sbw4-page .sbw3-role-mount[data-sb-react-active="mounted"]');
    document.body.classList.toggle('sbw4-world-mounted', !!world);
    document.body.classList.toggle('sbw4-roles-mounted', !!roles);
  };
  update();
  let queued = false;
  const schedule = () => { if (queued) return; queued = true; queueMicrotask(() => { queued = false; update(); }); };
  document.addEventListener('studybuddy:react-mounted', schedule);
  const observer = new MutationObserver(mutations => {
    if (mutations.some(m => (m.type === 'attributes' && m.attributeName === 'data-sb-react-active') || m.type === 'childList')) schedule();
  });
  const page = document.querySelector('.sbw4-page');
  if (page) observer.observe(page, { attributes: true, subtree: true, childList: true, attributeFilter: ['data-sb-react-active'] });
  window.addEventListener('pagehide', () => observer.disconnect(), { once: true });
})();
