(function () {
  const DEFAULT_THEME = 'cosmic-dolphin';
  const themeKeys = ['studybuddy.theme', 'studybuddyTheme', 'sb_theme', 'dashboard_style'];
  const body = document.body;

  function clearTheme() {
    themeKeys.forEach((key) => {
      try { localStorage.removeItem(key); } catch (e) {}
    });
    if (body) {
      body.dataset.sbTheme = DEFAULT_THEME;
      body.dataset.studybuddyTheme = DEFAULT_THEME;
      Array.from(body.classList).forEach((name) => {
        if (name.startsWith('theme-') || name.startsWith('sb-theme-')) body.classList.remove(name);
      });
      body.classList.add('theme-cosmic-dolphin', 'sb-theme-cosmic-dolphin');
    }
  }

  function bootNav() {
    const nav = document.querySelector('[data-sb-polished-nav]');
    if (!nav) return;
    const toggle = nav.querySelector('[data-nav-toggle]');
    const panel = nav.querySelector('[data-nav-links]');
    if (toggle && panel) {
      toggle.addEventListener('click', () => {
        const open = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      });
    }
  }

  function bootGuestThemeReset() {
    if (body && body.dataset.sbAuth === '0') clearTheme();
    document.addEventListener('submit', function (event) {
      if (event.target && event.target.matches('[data-sb-logout-form], form[action*="logout"]')) clearTheme();
    }, true);
  }

  function bootAppFilters() {
    const search = document.querySelector('[data-sb-app-search]');
    const category = document.querySelector('[data-sb-app-filter]');
    const role = document.querySelector('[data-sb-role-filter]');
    const cards = Array.from(document.querySelectorAll('[data-app-card]'));
    if (!cards.length) return;

    function apply() {
      const q = (search && search.value || '').trim().toLowerCase();
      const cat = category && category.value || 'all';
      const roleVal = role && role.value || 'all';
      cards.forEach((card) => {
        const matchesText = !q || (card.dataset.search || '').includes(q);
        const matchesCat = cat === 'all' || card.dataset.category === cat;
        const matchesRole = roleVal === 'all' || (card.dataset.roles || '').split(' ').includes(roleVal);
        card.hidden = !(matchesText && matchesCat && matchesRole);
      });
    }
    [search, category, role].filter(Boolean).forEach((el) => el.addEventListener('input', apply));
    apply();
  }

  function bootImages() {
    document.querySelectorAll('img').forEach((img) => {
      img.loading = img.loading || 'lazy';
      img.decoding = img.decoding || 'async';
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    bootNav();
    bootGuestThemeReset();
    bootAppFilters();
    bootImages();
  });
})();
