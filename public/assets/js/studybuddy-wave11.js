/* StudyBuddy W11: original, standalone interaction layer. No Base44 SDK or auth changes.
   Genuine installed React Bits Galaxy/Antigravity still mount through the existing React bridge. */
(() => {
  'use strict';
  const root = document.querySelector('[data-sb11-home]');
  if (!root) return;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const status = { version: 'W11', initialized: false, fetchedFromBase44: false, reactMounts: {}, cards: 0, filtered: 'all' };
  window.StudyBuddyWave11 = status;
  const reel = root.querySelector('[data-sb11-reel]');
  const cards = [...root.querySelectorAll('[data-sb11-app]')];
  status.cards = cards.length;
  const move = (direction) => {
    if (!reel) return;
    const card = reel.querySelector('[data-sb11-app]:not([hidden])');
    const width = card ? card.getBoundingClientRect().width + 19 : 315;
    reel.scrollBy({left:width * direction, behavior: reduced.matches ? 'auto':'smooth'});
  };
  root.querySelector('[data-sb11-next]')?.addEventListener('click', () => move(1));
  root.querySelector('[data-sb11-prev]')?.addEventListener('click', () => move(-1));
  reel?.addEventListener('keydown', event => {
    if (event.target !== reel) return;
    if (event.key === 'ArrowRight') {event.preventDefault();move(1);}
    if (event.key === 'ArrowLeft') {event.preventDefault();move(-1);}
  });
  const filters = [...root.querySelectorAll('[data-sb11-filter]')];
  filters.forEach(button => button.addEventListener('click', () => {
    const value = button.dataset.sb11Filter;
    filters.forEach(other => {
      const active = other === button;
      other.classList.toggle('is-selected', active);
      other.setAttribute('aria-pressed', String(active));
    });
    cards.forEach(card => {card.hidden = value !== 'all' && card.dataset.sb11Category !== value;});
    if(reel) reel.scrollLeft = 0;
    status.filtered = value;
  }));

  const steps = [...root.querySelectorAll('[data-sb11-step]')];
  const artwork = [...root.querySelectorAll('[data-sb11-story-image]')];
  const setStory = (index) => {
    steps.forEach((step,i) => step.classList.toggle('is-active',index===i));
    artwork.forEach((img,i) => img.classList.toggle('is-active',index===i));
    status.activeStory = index;
  };
  if ('IntersectionObserver' in window && steps.length) {
    const observer = new IntersectionObserver(entries => {
      const chosen = entries.filter(x=>x.isIntersecting).sort((a,b)=>b.intersectionRatio-a.intersectionRatio)[0];
      if(chosen) setStory(Number(chosen.target.dataset.sb11Step));
    },{rootMargin:'-12% 0px -43% 0px',threshold:[0,.35,.7]});
    steps.forEach(step => observer.observe(step));
  }
  // No JS-hiding of content: all sections remain readable even when JS/WebGL fails.
  const progress = root.querySelector('[data-sb11-progress]');
  let scrollQueued = false;
  const drawProgress = () => {
    scrollQueued = false;
    if (!progress) return;
    const rect = root.getBoundingClientRect();
    const max = Math.max(1,rect.height - window.innerHeight);
    progress.style.width = `${(Math.min(1,Math.max(0,-rect.top/max))*100).toFixed(1)}%`;
  };
  const onScroll = () => {
    if(scrollQueued) return;
    scrollQueued = true;
    requestAnimationFrame(drawProgress);
  };
  window.addEventListener('scroll',onScroll,{passive:true});
  window.addEventListener('resize',onScroll,{passive:true});
  drawProgress();

  // Original graceful fallback stars from the saved prototype's aesthetic, not its
  // Base44 runtime, external proprietary SDK, or minified app bundle.
  const canvas = root.querySelector('[data-sb11-stars]');
  const scene = root.querySelector('.sb11-hero');
  if(canvas && scene) {
    const ctx = canvas.getContext('2d',{alpha:true});
    if(ctx) {
      let stars = [], raf = 0, visible = true, width = 0, height = 0;
      const random = (n) => {const t=Math.sin(n * 127.1 + 83.13)*43758.5453;return t-Math.floor(t);};
      const resize = () => {
        const rect=scene.getBoundingClientRect();
        width=Math.max(1,Math.round(rect.width));height=Math.max(1,Math.round(rect.height));
        const dpr=Math.min(window.devicePixelRatio||1,1.5);
        canvas.width=Math.round(width*dpr);canvas.height=Math.round(height*dpr);
        ctx.setTransform(dpr,0,0,dpr,0,0);
        const count=Math.min(135,Math.max(45,Math.round(width*height/9300)));
        stars=Array.from({length:count},(_,i)=>({
          x:random(i*23+1)*width,y:random(i*57+2)*height,
          radius:.35+random(i*13+3)*1.5,
          phase:random(i*47+4)*Math.PI*2,
          speed:.3+random(i*9+5)*1.3,
          color:i%5===0?'145,227,255':i%3===0?'190,153,255':'245,244,255'
        }));
      };
      const render = (time) => {
        ctx.clearRect(0,0,width,height);
        stars.forEach((star,i) => {
          const pulse = reduced.matches ? .63 : .45 + .36 * (1+Math.sin(time*.0005*star.speed+star.phase))*.5;
          const alpha = Math.min(.95,pulse);
          ctx.fillStyle=`rgba(${star.color},${alpha})`;
          ctx.beginPath();ctx.arc(star.x,star.y,star.radius,0,Math.PI*2);ctx.fill();
          if(i%19===0){ctx.strokeStyle=`rgba(${star.color},${alpha*.19})`;ctx.lineWidth=.7;ctx.beginPath();ctx.moveTo(star.x-8,star.y);ctx.lineTo(star.x+8,star.y);ctx.moveTo(star.x,star.y-8);ctx.lineTo(star.x,star.y+8);ctx.stroke();}
        });
      };
      const animate=(time)=>{raf=0;if(!visible || document.hidden) return;render(time);if(!reduced.matches) raf=requestAnimationFrame(animate);};
      const start=()=>{if(raf)cancelAnimationFrame(raf);raf=0;if(visible){render(0);if(!reduced.matches)raf=requestAnimationFrame(animate);}};
      const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries=>{visible=entries[0]?.isIntersecting ?? true;start();},{rootMargin:'100px'}) : null;
      observer?.observe(scene);
      resize();start();
      window.addEventListener('resize',()=>{resize();start();},{passive:true});
      reduced.addEventListener?.('change',start);
      document.addEventListener('visibilitychange',start);
      status.fallbackStars = true;
    }
  }
  const checkReact = () => {
    for(const effect of root.querySelectorAll('[data-sb-react]')) {
      status.reactMounts[effect.dataset.sbReact] = !!effect.querySelector('canvas');
    }
  };
  checkReact();
  // The existing Vite bridge may mount asynchronously. Observe only twice without polling forever.
  for(const effect of root.querySelectorAll('[data-sb-react]')) {
    const observer=new MutationObserver(checkReact);
    observer.observe(effect,{childList:true,subtree:true});
    window.addEventListener('pagehide',()=>observer.disconnect(),{once:true});
  }
  status.initialized = true;
})();
