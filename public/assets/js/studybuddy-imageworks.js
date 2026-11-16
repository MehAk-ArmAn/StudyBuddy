/* StudyBuddy image-led homepage behavior. Works without React; genuine W8 ReactBits mounts remain owned by the existing React bridge. */
(() => {
  'use strict';
  const page = document.querySelector('[data-imageworks-home]');
  if (!page || page.dataset.iwInitialized === 'true') return;
  page.dataset.iwInitialized = 'true';
  const gallery = page.querySelector('[data-iw-gallery]');
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const move = (direction) => {
    if (!gallery) return;
    const card = gallery.querySelector('.iw-world');
    const pixels = (card?.getBoundingClientRect().width || 330) + 18;
    gallery.scrollBy({left: direction * pixels, behavior: reduced.matches ? 'instant' : 'smooth'});
  };
  page.querySelector('[data-iw-prev]')?.addEventListener('click', () => move(-1));
  page.querySelector('[data-iw-next]')?.addEventListener('click', () => move(1));
  gallery?.addEventListener('keydown', event => {
    if (event.target.closest('a, button')) return;
    if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
      event.preventDefault();
      move(event.key === 'ArrowRight' ? 1 : -1);
    }
  });
  const pictures = [...page.querySelectorAll('[data-iw-image]')];
  const steps = [...page.querySelectorAll('[data-iw-step]')];
  const counter = page.querySelector('[data-iw-counter]');
  const activate = index => {
    pictures.forEach((picture, i) => picture.classList.toggle('is-active', i === index));
    steps.forEach((step, i) => step.classList.toggle('is-active', i === index));
    if (counter) counter.textContent = String(index + 1).padStart(2,'0')+' / '+String(pictures.length).padStart(2,'0');
  };
  activate(0);
  if ('IntersectionObserver' in window && !reduced.matches) {
    const ratios = new Map();
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => ratios.set(e.target,e.intersectionRatio));
      let best = 0, value = -1;
      steps.forEach((step,i) => {
        if ((ratios.get(step) || 0) > value) {value=ratios.get(step) || 0;best=i;}
      });
      activate(best);
    }, {rootMargin:'-16% 0px -16% 0px',threshold:[0,.1,.25,.5,.75]});
    steps.forEach(step => observer.observe(step));
  }
  window.StudyBuddyImageworksLanding = Object.freeze({mounted:true,images:pictures.length,appCards:gallery?.querySelectorAll('.iw-world').length || 0});
})();
