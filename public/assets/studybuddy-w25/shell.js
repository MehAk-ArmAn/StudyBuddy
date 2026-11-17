/* W25: accessible shared interactions; does not intercept Laravel navigation. */
(function () {
  'use strict';
  const $ = (s, root=document) => root.querySelector(s);
  const header = $('[data-sb25-header]');
  if (!header) return;
  const mobile = $('[data-sb25-mobile-toggle]', header);
  const menu = $('#sb25-mobile-menu', header);
  const searchButton = $('[data-sb25-search-open]', header);
  const search = $('#sb25-search-box', header);
  const accountButton = $('[data-sb25-account-toggle]', header);
  const account = $('#sb25-account-menu', header);
  const setPanel = (button, panel, open) => {
    if (!button || !panel) return;
    panel.hidden = !open;
    button.setAttribute('aria-expanded', String(open));
  };
  mobile?.addEventListener('click', () => {
    const show = menu?.hidden;
    setPanel(searchButton, search, false);
    setPanel(accountButton, account, false);
    setPanel(mobile, menu, !!show);
  });
  searchButton?.addEventListener('click', () => {
    const show = search?.hidden;
    setPanel(mobile, menu, false);
    setPanel(accountButton, account, false);
    setPanel(searchButton, search, !!show);
    if (show) $('#sb25-search-input', header)?.focus();
  });
  accountButton?.addEventListener('click', () => {
    const show = account?.hidden;
    setPanel(searchButton, search, false);
    setPanel(mobile, menu, false);
    setPanel(accountButton, account, !!show);
  });
  document.addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    setPanel(mobile, menu, false);
    setPanel(searchButton, search, false);
    setPanel(accountButton, account, false);
  });
  document.addEventListener('click', e => {
    if (header.contains(e.target)) {
      if (menu && !menu.hidden && !menu.contains(e.target) && !mobile?.contains(e.target)) setPanel(mobile, menu, false);
      if (search && !search.hidden && !search.contains(e.target) && !searchButton?.contains(e.target)) setPanel(searchButton, search, false);
      if (account && !account.hidden && !account.contains(e.target) && !accountButton?.contains(e.target)) setPanel(accountButton, account, false);
      return;
    }
    setPanel(mobile, menu, false); setPanel(searchButton, search, false); setPanel(accountButton, account, false);
  });
  window.StudyBuddyShell25 = {
    diagnostics: () => ({
      headerCount: document.querySelectorAll('[data-sb25-header]').length,
      legacyHeaderCount: document.querySelectorAll('.sb-advanced-shell').length,
      universeToolbarCount: document.querySelectorAll('.sb-u-toolbar').length,
      homeTabInMainNav: [...document.querySelectorAll('[data-sb25-react-host] a')].some(a => /^(home)$/i.test(a.textContent.trim())),
      current: header.getAttribute('data-sb25-current'),
      gooeyMounted: !!header.querySelector('.gooey-nav-container')
    })
  };
})();
