(() => {
  'use strict';
  document.querySelectorAll('[data-sb33-showcase]').forEach(root => {
    const dialog = root.querySelector('[data-sb33-lightbox]');
    if (!dialog) return;
    root.querySelectorAll('[data-sb33-shot]').forEach(button => {
      button.addEventListener('click', () => {
        const picture = dialog.querySelector('img');
        picture.src = button.dataset.src || '';
        picture.alt = button.dataset.alt || 'Learning app screenshot';
        dialog.querySelector('p').textContent = button.dataset.title || '';
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else window.open(picture.src, '_blank', 'noopener,noreferrer');
      });
    });
    dialog.addEventListener('click', event => {
      if (event.target === dialog) dialog.close();
    });
  });
})();
