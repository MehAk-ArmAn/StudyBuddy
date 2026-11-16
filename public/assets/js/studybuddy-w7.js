/* StudyBuddy W7 original scroll gallery enhancer. No React reinitialization; Laravel stays in charge. */
(() => {
  'use strict';
  const root = document.querySelector('[data-sbw7-page]');
  if (!root || root.dataset.sbw7Init === '1') return;
  root.dataset.sbw7Init = '1';
  root.classList.add('js-enhanced');
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const reel = root.querySelector('[data-sbw7-track]');
  const next = root.querySelector('[data-sbw7-next]');
  const prev = root.querySelector('[data-sbw7-prev]');
  function shift(dir) {
    if (!reel) return;
    const item = reel.querySelector('.sbw7-world');
    const step = (item?.getBoundingClientRect().width || 350) + 20;
    reel.scrollBy({left:dir * step, behavior: reduced.matches ? 'instant' : 'smooth'});
  }
  next?.addEventListener('click', () => shift(1));
  prev?.addEventListener('click', () => shift(-1));
  root.querySelector('[data-sbw7-reel]')?.addEventListener('keydown', event => {
    if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
      if (event.target.closest('a,button')) return;
      event.preventDefault(); shift(event.key === 'ArrowRight' ? 1 : -1);
    }
  });
  const pictures = [...root.querySelectorAll('[data-sbw7-story-image]')];
  const steps = [...root.querySelectorAll('[data-sbw7-story-step]')];
  const number = root.querySelector('[data-sbw7-caption-number]');
  const label = root.querySelector('[data-sbw7-caption]');
  const labels = ['BE CURIOUS','FIND YOUR PLACE','GROW TOGETHER'];
  function active(index) {
    pictures.forEach((p, i) => p.classList.toggle('is-active', i === index));
    steps.forEach((p, i) => p.classList.toggle('is-active', i === index));
    if (number) number.textContent = String(index + 1).padStart(2,'0') + ' / ' + String(pictures.length).padStart(2,'0');
    if (label) label.textContent = labels[index] || 'STUDYBUDDY';
  }
  active(0);
  if (steps.length && pictures.length && 'IntersectionObserver' in window && !reduced.matches) {
    const visible = new Map();
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => visible.set(entry.target, entry.intersectionRatio));
      let i = 0, max = -1;
      steps.forEach((step, ix) => { const r = visible.get(step) || 0; if (r > max) {max=r;i=ix;} });
      active(i);
    }, {threshold:[0,0.1,0.25,0.4,0.6,0.85],rootMargin:'-15% 0px -15% 0px'});
    steps.forEach(el => observer.observe(el));
  }
  const entrance = [...root.querySelectorAll('.sbw7-inview')];
  if ('IntersectionObserver' in window && !reduced.matches) {
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {if(e.isIntersecting){e.target.classList.add('is-inview');io.unobserve(e.target);}});
    }, {threshold:0.08});
    entrance.forEach(el => io.observe(el));
  } else entrance.forEach(el => el.classList.add('is-inview'));
  window.StudyBuddyW7 = Object.freeze({status:'mounted',galleryCount:pictures.length,worldCards:reel?.querySelectorAll('.sbw7-world').length || 0});
})();
