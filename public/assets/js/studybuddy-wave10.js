/* StudyBuddy W10 — progressively enhanced catalogue, story & measured scroll.
   DOM-only enhancement: the real React Bits Galaxy/Antigravity canvases are mounted
   separately by the existing Laravel/Vite React bridge. */
(() => {
  'use strict';
  const home = document.querySelector('[data-sbx-home]');
  if (!home) return;
  const reel = home.querySelector('[data-sbx-reel]');
  const buttons = home.querySelectorAll('[data-sbx-category]');
  const appCards = [...home.querySelectorAll('[data-sbx-app]')];
  const live = (key, value) => { window.StudyBuddyWave10 = {...(window.StudyBuddyWave10 || {}), [key]: value}; };
  const move = (direction) => {
    if (!reel) return;
    const first = reel.querySelector('[data-sbx-app]:not([hidden])');
    const width = first ? first.getBoundingClientRect().width + 20 : 310;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    reel.scrollBy({ left: width * direction, behavior: reduced ? 'instant' : 'smooth' });
  };
  home.querySelector('[data-sbx-next]')?.addEventListener('click', () => move(1));
  home.querySelector('[data-sbx-previous]')?.addEventListener('click', () => move(-1));
  reel?.addEventListener('keydown', event => {
    if (event.key === 'ArrowRight') {event.preventDefault();move(1);}
    if (event.key === 'ArrowLeft') {event.preventDefault();move(-1);}
  });
  buttons.forEach(button => button.addEventListener('click', () => {
    const category = button.dataset.sbxCategory;
    buttons.forEach(b => { const active = b === button; b.classList.toggle('is-selected', active); b.setAttribute('aria-pressed', String(active)); });
    appCards.forEach(card => { card.hidden = category !== 'all' && card.dataset.sbxAppCategory !== category; });
    if (reel) reel.scrollLeft = 0;
    live('filteredCategory', category);
  }));
  const storySteps = [...home.querySelectorAll('[data-sbx-story-step]')];
  const images = [...home.querySelectorAll('[data-sbx-story-image]')];
  const setStory = index => {
    storySteps.forEach((step, i) => step.classList.toggle('is-current', i === index));
    images.forEach((img, i) => img.classList.toggle('is-current', i === index));
  };
  if (window.IntersectionObserver && storySteps.length) {
    const storyObserver = new IntersectionObserver(entries => {
      const candidate = entries.filter(x => x.isIntersecting).sort((a,b)=> b.intersectionRatio-a.intersectionRatio)[0];
      if (candidate) setStory(Number(candidate.target.dataset.sbxStoryStep));
    }, {rootMargin:'-20% 0px -40% 0px', threshold:[0,.25,.5,.75]});
    storySteps.forEach(step=>storyObserver.observe(step));
  }
  const progress = home.querySelector('[data-sbx-progress]');
  let ticking = false;
  const updateScroll = () => {
    ticking=false;
    if (!progress) return;
    const rect=home.getBoundingClientRect();
    const scrollable=Math.max(1,rect.height-window.innerHeight);
    const ratio=Math.max(0,Math.min(1,-rect.top / scrollable));
    progress.style.width=(ratio*100).toFixed(2)+'%';
  };
  const handleScroll = () => {if(!ticking){ticking=true;requestAnimationFrame(updateScroll);}};
  window.addEventListener('scroll',handleScroll,{passive:true});
  window.addEventListener('resize',handleScroll,{passive:true});
  handleScroll();
  live('status','initialised');
  live('publishedFeaturedCards',appCards.length);
  live('reactBridgePresent',Boolean(window.StudyBuddyReactBridge)); // canvas mounting must still be checked
})();
