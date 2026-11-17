/* W25: accessible shared interactions; does not intercept Laravel navigation. */
(function () {
  'use strict';
  const $ = (s, root=document) => root.querySelector(s);
  const header = $('[data-sb25-header]');
  if (!header) return;
  // One visible public navigation at all times. The server-rendered Blade menu
  // stays interactive until the existing W25 React GooeyNav has really mounted.
  // A separate React slot avoids the old root replacement flash on every page.
  const gooeySlot = $('[data-sb25-react-host]', header);
  const navRegion = gooeySlot?.closest('.sb25-primary');
  const bladeLinks = navRegion?.querySelector('.sb25-links-fallback');
  if (gooeySlot && navRegion && bladeLinks) {
    let scheduled = false;
    let mounted = false;
    let observer;
    const revealNav = () => {
      if (mounted || scheduled || !gooeySlot.isConnected) return;
      const nav = gooeySlot.querySelector('.gooey-nav');
      if (!nav || navRegion.getBoundingClientRect().width < 1) return;
      scheduled = true;
      requestAnimationFrame(() => requestAnimationFrame(() => {
        scheduled = false;
        const live = gooeySlot.querySelector('.gooey-nav');
        if (!live || !gooeySlot.isConnected || !bladeLinks.isConnected) return;
        const bounds = live.getBoundingClientRect();
        const backupBounds = bladeLinks.getBoundingClientRect();
        if (bounds.width < 200 || bounds.height < 28 || backupBounds.width < 200) return;
        navRegion.style.minWidth = Math.ceil(Math.max(bounds.width, backupBounds.width)) + 'px';
        bladeLinks.inert = true;
        bladeLinks.setAttribute('aria-hidden', 'true');
        gooeySlot.inert = false;
        gooeySlot.removeAttribute('aria-hidden');
        navRegion.dataset.sb25Ready = 'true';
        mounted = true;
        observer?.disconnect();
        window.removeEventListener('resize', revealNav);
      }));
    };
    observer = new MutationObserver(revealNav);
    observer.observe(gooeySlot, { childList:true, subtree:true });
    window.addEventListener('resize', revealNav, { passive:true });
    revealNav();
  }
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
      gooeyMounted: !!header.querySelector('[data-sb25-react-host] .gooey-nav')
    })
  };
})();
