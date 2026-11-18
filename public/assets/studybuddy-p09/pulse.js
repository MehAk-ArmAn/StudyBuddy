/* StudyBuddy P09: progressive enhancements only; server-rendered forms remain usable without JS. */
(() => {
  'use strict';
  const closeNotice = document.querySelector('[data-p09-toast-close]');
  if (closeNotice) closeNotice.addEventListener('click', () => closeNotice.closest('[data-p09-toast]')?.remove());
  for (const popup of document.querySelectorAll('[data-sbp09-bell]')) {
    popup.addEventListener('keydown', event => {
      if (event.key === 'Escape') { popup.open = false; popup.querySelector('summary')?.focus(); }
    });
    document.addEventListener('pointerdown', event => {
      if (popup.open && !popup.contains(event.target)) popup.open = false;
    });
  }
})();
