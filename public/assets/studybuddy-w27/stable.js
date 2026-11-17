/* StudyBuddy W27: existing UI guard only; no data or navigation mutation. */
(() => {
  'use strict';
  if (window.__SB27Booted) return;
  window.__SB27Booted = true;
  const BASE = '/assets/';
  function patchHeader() {
    if (/^\/admin(?:\/|$)/.test(location.pathname)) return;
    const header = [...document.querySelectorAll('header')].find(h => h.querySelector('a[href="/"]') && /StudyBuddy/i.test(h.textContent));
    if (!header) return;
    header.classList.add('sb27-header');
    const brand = header.querySelector('a[href="/"]');
    brand?.classList.add('sb27-brand');
    const animated = header.querySelector('.gooey-nav');
    // Identify navigation parent — the original React Bits component owns its own canvas.
    if (animated) {
      const host = animated.closest('[data-sb24-kind],.sb24-nav-host,[data-sb25-gooey],.sb25-gooey-host') || animated.parentElement;
      if (host && header.contains(host)) host.classList.add('sb27-center');
    }
    const navs = [...header.querySelectorAll('nav')];
    navs.filter(nav => nav !== animated && !nav.contains(animated) && !animated?.contains(nav))
      .filter(nav => nav.querySelectorAll('a[href]').length >= 2)
      .forEach(nav => nav.classList.add('sb27-legacy-nav'));
    // React may mount after DOM ready. Never suppress fallback links before Gooey exists.
    header.classList.toggle('sb27-gooey-ready', Boolean(animated?.querySelector('canvas')));
    const children = [...(header.firstElementChild?.children || [])];
    const actions = children.find(n => n.querySelector?.('a[href="/login"],a[href="/register"],button[title*="Appearance"],button[aria-label*="menu"]'));
    if (actions && actions !== brand && !actions.contains(brand) && !actions.contains(animated)) actions.classList.add('sb27-actions');
  }
  let queued = false;
  const schedule = () => {
    if (queued) return;
    queued = true;
    requestAnimationFrame(() => { queued = false; patchHeader(); });
  };
  function init() {
    patchHeader();
    // Only monitor the header, because React mounts GooeyNav asynchronously.
    const h = document.querySelector('header');
    if (h && 'MutationObserver' in window) {
      const obs = new MutationObserver(schedule);
      obs.observe(h,{childList:true,subtree:true});
      setTimeout(() => obs.disconnect(),10000);
    }
    window.StudyBuddyStability27 = {
      diagnostics: async () => {
        const urls = [
          BASE+'studybuddy-base44-exact/base44-original.css',
          BASE+'studybuddy-base44-exact/bridge.css',
          BASE+'studybuddy-w25/shell.css',
          BASE+'studybuddy-w25/gooey.css',
          BASE+'studybuddy-w25/studybuddy-visuals.css',
          BASE+'studybuddy-w25/studybuddy-visuals.js',
          BASE+'studybuddy-w26/studybuddy-w26.css',
          BASE+'studybuddy-w26/studybuddy-w26.js'
        ];
        const assets = await Promise.all(urls.map(async path => {
          try { const res = await fetch(path,{method:'HEAD',cache:'no-store'}); return {path,status:res.status,type:res.headers.get('content-type')}; }
          catch(e){ return {path,error:String(e)}; }
        }));
        return {version:'W27',headerCount:document.querySelectorAll('.sb27-header').length,
          gooeyMounted:!!document.querySelector('.sb27-header .gooey-nav canvas'),
          originalFallbackHidden:!!document.querySelector('.sb27-header.sb27-gooey-ready .sb27-legacy-nav'),
          galaxyPreserved:!!document.querySelector('#sb19-galaxy-host canvas'),
          assets};
      }
    };
    console.info('[StudyBuddy W27] Stable header/asset checks available: await window.StudyBuddyStability27.diagnostics()');
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',init,{once:true}); else init();
})();
