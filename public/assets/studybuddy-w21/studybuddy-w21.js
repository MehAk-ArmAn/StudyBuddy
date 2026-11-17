/* StudyBuddy W21 — native-scroll, requestAnimationFrame-coordinated cinematic entrance.
   The existing Base44 hero/illustrations and genuine React Bits canvases are not replaced. */
(() => {
  'use strict';
  if (window.StudyBuddyJourney21) return;
  const track = document.querySelector('[data-sb21-journey]');
  const hero = track?.querySelector('[data-sb21-hero]');
  if (!track || !hero) return;
  const grid = hero.querySelector('.sb21-hero-grid');
  const copy = grid?.children[0];
  const art = grid?.children[1];
  if (!grid || !copy || !art) {
    console.warn('[StudyBuddy W21] Hero grid not found; zoom disabled without touching layout.');
    return;
  }
  const portal = document.createElement('div');
  portal.className = 'sb21-portal';
  portal.setAttribute('aria-hidden', 'true');
  const fade = document.createElement('div');
  fade.className = 'sb21-portal-fade';
  fade.setAttribute('aria-hidden', 'true');
  const progress = document.createElement('div');
  progress.className = 'sb21-scroll-progress';
  progress.setAttribute('aria-hidden', 'true');
  hero.append(portal, fade, progress);
  const cue = [...hero.querySelectorAll('div')].find(node =>
    node.textContent?.trim().toLowerCase() === 'scroll to travel' && !!node.querySelector('span'));
  cue?.classList.add('sb21-cue');
  const reduced = matchMedia('(prefers-reduced-motion: reduce)');
  const pinned = matchMedia('(min-width: 900px) and (min-height: 620px)');
  const clamp = (v, a = 0, b = 1) => Math.max(a, Math.min(b, v));
  const smooth = (a, b, x) => { const t = clamp((x - a) / (b - a)); return t*t*(3-2*t); };
  let raf = 0;
  let current = 0;
  let target = 0;
  let active = true;
  const set = (k, v) => hero.style.setProperty(k, v);
  const paint = p => {
    const desktop = pinned.matches && !reduced.matches;
    const focus = desktop ? smooth(0.02, 1, p) : 0;
    const travel = smooth(0.03, 0.8, focus);
    const vanish = smooth(0.50, 0.97, focus);
    set('--sb21-copy-y', `${Math.round(-36 * travel)}px`);
    set('--sb21-copy-scale', (1 - .085 * travel).toFixed(4));
    set('--sb21-copy-opacity', (1 - smooth(.11, .63, focus)).toFixed(4));
    set('--sb21-art-y', `${Math.round(-30 * travel)}px`);
    set('--sb21-art-scale', (1 + 1.48 * travel).toFixed(4));
    set('--sb21-art-opacity', (1 - vanish).toFixed(4));
    set('--sb21-portal-opacity', (smooth(.30, .73, focus) * (1 - smooth(.81, 1, focus))).toFixed(4));
    set('--sb21-portal-scale', (.48 + 3.1 * smooth(.22, 1, focus)).toFixed(4));
    set('--sb21-fade-opacity', (smooth(.76, 1, focus) * .78).toFixed(4));
    set('--sb21-progress', p.toFixed(4));
    set('--sb21-progress-opacity', desktop && p > .02 && p < .95 ? '1' : '0');
    set('--sb21-cue-opacity', (1 - smooth(0, .22, p)).toFixed(4));
    if (desktop && p > .66) hero.classList.add('sb21-going-in');
    else hero.classList.remove('sb21-going-in');
  };
  const measure = () => {
    if (reduced.matches) { target = current = 0; paint(0); return; }
    if (pinned.matches) {
      const span = Math.max(1, track.getBoundingClientRect().height - hero.getBoundingClientRect().height);
      target = clamp(-track.getBoundingClientRect().top / span);
    } else {
      // On phone/tablet there is no pinning or forced zoom, so content remains readable.
      target = 0;
    }
  };
  const tick = () => {
    raf = 0;
    if (!active) return;
    measure();
    current += (target - current) * .22;
    if (Math.abs(current - target) < .001) current = target;
    paint(current);
    if (Math.abs(current - target) >= .001) schedule();
  };
  const schedule = () => { if (!raf) raf = requestAnimationFrame(tick); };
  const onVisibility = () => { active = !document.hidden; if (active) schedule(); };
  addEventListener('scroll', schedule, { passive:true });
  addEventListener('resize', schedule, { passive:true });
  document.addEventListener('visibilitychange', onVisibility);
  reduced.addEventListener?.('change', schedule);
  pinned.addEventListener?.('change', schedule);
  const ro = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(schedule) : null;
  ro?.observe(track);
  ro?.observe(hero);
  schedule();
  window.StudyBuddyJourney21 = {
    diagnostics: () => ({ version:'W21', viewport:[innerWidth, innerHeight],
      hero:[Math.round(hero.getBoundingClientRect().width),Math.round(hero.getBoundingClientRect().height)],
      galaxyCanvas:!!document.querySelector('#sb19-galaxy-host canvas'),
      pinned:pinned.matches && !reduced.matches, scrollProgress:Number(current.toFixed(3)),
      reducedMotion:reduced.matches, source:'Existing Base44 + React Bits' }),
    refresh:schedule
  };
})();
