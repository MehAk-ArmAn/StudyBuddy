/* StudyBuddy W6: progressive editorial interactions (site routes and auth untouched). */
(() => {
  'use strict';
  if (window.__sbW6Landing) return;
  window.__sbW6Landing = true;
  function start() {
    const root = document.querySelector('.sbw6-landing');
    if (!root) return;
    const gallery = root.querySelector('.sbw6-roles__gallery');
    const detail = root.querySelector('[data-w6-role-detail]');
    if (!gallery || !detail) return;
    const description = detail.querySelector('[data-w6-role-description]');
    const dest = detail.querySelector('[data-w6-role-cta]');
    const tiles = [...gallery.querySelectorAll('.sbw6-roles__tile')];
    function show(tile) {
      if (!tile || !description || !dest) return;
      const value = tile.getAttribute('data-w6-description');
      const label = tile.getAttribute('data-w6-label');
      if (!value || !label) return;
      description.textContent = value;
      dest.textContent = `Discover the ${label} journey ↗`;
      dest.href = tile.href;
      tiles.forEach(el => el.classList.toggle('sbw6-is-selected', el === tile));
    }
    tiles.forEach(tile => {
      tile.addEventListener('pointerenter', () => show(tile));
      tile.addEventListener('focus', () => show(tile));
    });
    gallery.addEventListener('pointerleave', () => show(tiles[0]));
    if (tiles.length) show(tiles[0]);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',start,{once:true});
  else start();
})();
