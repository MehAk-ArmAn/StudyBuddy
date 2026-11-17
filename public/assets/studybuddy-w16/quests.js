(() => {
  'use strict';
  const root = document.querySelector('[data-sb16-quests]');
  if (!root) return;
  const composer = root.querySelector('[data-sb16-composer]');
  const openButtons = [...root.querySelectorAll('[data-sb16-open]')];
  const closeButton = root.querySelector('[data-sb16-close]');
  const composerTitle = root.querySelector('[data-sb16-title]');
  const openComposer = () => {
    if (!composer) return;
    composer.hidden = false;
    composer.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
    composerTitle?.focus({ preventScroll: true });
  };
  openButtons.forEach(button => button.addEventListener('click', openComposer));
  closeButton?.addEventListener('click', () => { composer.hidden = true; openButtons[0]?.focus(); });
  if (root.dataset.sb16Errors === '1') openComposer();
  const cards = [...root.querySelectorAll('[data-sb16-quest]')];
  const tabs = [...root.querySelectorAll('[data-sb16-filter]')];
  const search = root.querySelector('[data-sb16-search]');
  const output = root.querySelector('[data-sb16-count]');
  const empty = root.querySelector('[data-sb16-empty]');
  let selected = 'all';
  const filter = () => {
    const query = (search?.value || '').trim().toLocaleLowerCase();
    let visible = 0;
    cards.forEach(card => {
      const statusMatch = selected === 'all' || card.dataset.status === selected;
      const searchMatch = !query || (card.dataset.search || '').toLocaleLowerCase().includes(query);
      const show = statusMatch && searchMatch;
      card.hidden = !show;
      if (show) visible += 1;
    });
    tabs.forEach(tab => tab.setAttribute('aria-pressed', String(tab.dataset.sb16Filter === selected)));
    if (output) output.textContent = `Showing ${visible} of ${cards.length} quests on this page`;
    if (empty) empty.hidden = visible !== 0;
  };
  tabs.forEach(tab => tab.addEventListener('click', () => { selected = tab.dataset.sb16Filter; filter(); }));
  search?.addEventListener('input', filter);
  filter();
  root.querySelectorAll('[data-sb16-range]').forEach(slider => {
    const form = slider.closest('form');
    const out = form?.querySelector('[data-sb16-output]');
    const update = () => { if (out) out.textContent = `${slider.value}%`; };
    slider.addEventListener('input', update);
    update();
  });
  root.querySelectorAll('[data-sb16-delete]').forEach(form => form.addEventListener('submit', event => {
    if (!window.confirm('Permanently delete this quest?')) event.preventDefault();
  }));
  window.StudyBuddyW16 = { mounted: true, questsOnPage: cards.length, version: '16.0' };
})();
