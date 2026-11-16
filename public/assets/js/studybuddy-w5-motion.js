/* StudyBuddy W5: opt-in, progressive reveal layer. No history hijack, no form interceptions. */
(() => {
  'use strict';
  if (window.__studyBuddyW5MotionInstalled) return;
  window.__studyBuddyW5MotionInstalled = true;
  const start = () => {
    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return;
    document.documentElement.classList.add('sb-motion-ready');
    const selectors = [
      '.sbw4-page .sbw4-chapter',
      '.sbw4-page .sb7-heading',
      '.sbw4-page .sbw4-role-pathways__card',
      '.sbw4-page .sb7-what__card',
      '.sbw4-page .sbw3-selected',
      '.sbw4-page .sbw4-personal-wrap',
      '.sbw4-page .sb7-school',
      '.sbw4-page .sb7-last'
    ];
    const nodes = [...document.querySelectorAll(selectors.join(','))].filter(el => !el.hasAttribute('data-sbm-reveal'));
    if (!('IntersectionObserver' in window)) return;
    const observer = new IntersectionObserver((entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        entry.target.classList.remove('sbm-pending');
        entry.target.classList.add('sbm-revealed');
        observer.unobserve(entry.target);
      }
    }, {threshold:0.06,rootMargin:'0px 0px -24px 0px'});
    for (const [i,el] of nodes.entries()) {
      // Elements already visible remain visible: no flash / hydration mismatch.
      const box = el.getBoundingClientRect();
      el.setAttribute('data-sbm-reveal','');
      if (box.top < window.innerHeight * 0.83) {el.classList.add('sbm-revealed');continue;}
      el.style.setProperty('--sbm-delay', `${(i%4)*55}ms`);
      el.classList.add('sbm-pending');
      observer.observe(el);
    }
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) for (const el of nodes) {el.classList.remove('sbm-pending');el.classList.add('sbm-revealed');}
    },{once:true});
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, {once:true});
  else start();
})();
