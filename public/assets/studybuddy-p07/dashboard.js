/* Pack 07: progressive, non-essential client-side dashboard enhancements. */
(() => {
  const root = document.getElementById('sbp07-dashboard');
  if (!root) return;
  const key = 'sbp07-density';
  const settings = document.querySelector('[data-sbp07-density]');
  let stored = 'comfortable';
  try { stored = sessionStorage.getItem(key) || stored; } catch (_) {}
  const select = settings?.querySelector('select');
  function apply(v) {
    const density = v === 'compact' ? 'compact' : 'comfortable';
    root.dataset.density = density;
    if (select) select.value = density;
    try { sessionStorage.setItem(key, density); } catch (_) {}
  }
  apply(stored);
  select?.addEventListener('change', e => apply(e.target.value));
  const nav = root.querySelector('.sbp07-nav');
  nav?.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', () => {
      nav.querySelectorAll('[aria-current]').forEach(x => x.removeAttribute('aria-current'));
      a.setAttribute('aria-current', 'page');
    });
  });
})();
