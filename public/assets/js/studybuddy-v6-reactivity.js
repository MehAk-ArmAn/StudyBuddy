/* StudyBuddy V6 — original vanilla-JS interactions, no React/route/database mutations. */
(() => {
  'use strict';
  const root = document.documentElement;
  const lowMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const finePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  const isMotionOn = () => !lowMotion && !root.classList.contains('sb-u-motion-off');
  const $$ = (selector, parent = document) => Array.from(parent.querySelectorAll(selector));

  // Compact appearance palette. Theme choice is handled by the existing V5 theme engine.
  $$('[data-sb-v6-appearance]').forEach((control) => {
    const trigger = control.querySelector('[data-sb-v6-appearance-trigger]');
    const panel = control.querySelector('[data-sb-v6-appearance-menu]');
    if (!trigger || !panel) return;
    const close = (focus = false) => {
      panel.hidden = true;
      trigger.setAttribute('aria-expanded', 'false');
      if (focus) trigger.focus();
    };
    trigger.addEventListener('click', () => {
      const willOpen = panel.hidden;
      $$('[data-sb-v6-appearance-menu]').forEach((other) => { other.hidden = true; });
      $$('[data-sb-v6-appearance-trigger]').forEach((other) => other.setAttribute('aria-expanded','false'));
      panel.hidden = !willOpen;
      trigger.setAttribute('aria-expanded', willOpen ? 'true':'false');
      if (willOpen) panel.querySelector('[data-sb-v5-theme]')?.focus();
    });
    panel.addEventListener('click', (event) => { if (event.target.closest('[data-sb-v5-theme]')) close(); });
    control.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') { event.preventDefault(); close(true); }
    });
    document.addEventListener('pointerdown', (event) => { if (!control.contains(event.target)) close(); });
  });

  // Reveal only content sections; no auth, control-room, modal or form is hidden.
  if ('IntersectionObserver' in window && isMotionOn()) {
    const elements = $$('body.studybuddy-site :is(.sb-next-story-intro,.what-card,.feature-card,.preview-card,.sb-v5-world-chooser,.sb-v6-discovery-hero,.sb-schools-value-grid>article)');
    const visible = new IntersectionObserver((entries) => {
      entries.forEach(({target,isIntersecting}) => {
        if (isIntersecting) { target.classList.remove('sb-v6-before'); target.classList.add('sb-v6-in'); visible.unobserve(target); }
      });
    }, {threshold:0.04,rootMargin:'0px 0px 60px 0px'});
    elements.forEach((element) => {
      if (element.getBoundingClientRect().top < window.innerHeight * 0.87) return;
      element.classList.add('sb-v6-reveal','sb-v6-before'); visible.observe(element);
    });
    // The original V4 effects toggle should immediately restore hidden material.
    const onMotionChange = new MutationObserver(() => {
      if (!isMotionOn()) elements.forEach(el => { el.classList.remove('sb-v6-before');el.classList.add('sb-v6-in'); });
    });
    onMotionChange.observe(root,{attributes:true,attributeFilter:['class']});
  }

  // Lightweight reactive topographic contours. Inspired by the *idea* of a topographic flow,
  // independently authored in Canvas2D for Laravel. Render only when visible; 24 FPS maximum.
  $$('[data-sb-v6-flow]').forEach((canvas) => {
    const box = canvas.parentElement;
    const ctx = canvas.getContext?.('2d', {alpha:true});
    if (!box || !ctx) return;
    let w=0,h=0,dpr=1,hoverX=-9999,hoverY=-9999,viewable=true,frame=0,last=0;
    const resize = () => {
      const rect=box.getBoundingClientRect(); w=Math.max(1,Math.round(rect.width));h=Math.max(1,Math.round(rect.height));
      dpr=Math.min(window.devicePixelRatio||1,1.5);
      canvas.width=Math.round(w*dpr);canvas.height=Math.round(h*dpr);
      canvas.style.width=w+'px';canvas.style.height=h+'px';ctx.setTransform(dpr,0,0,dpr,0,0);
    };
    resize();
    if ('ResizeObserver' in window) new ResizeObserver(resize).observe(box); else window.addEventListener('resize',resize,{passive:true});
    if ('IntersectionObserver' in window) new IntersectionObserver(entries => {
      entries.forEach(e => {viewable=e.isIntersecting;});
    }, {threshold:0}).observe(box);
    if (finePointer) {
      box.addEventListener('pointermove', e => {const r=box.getBoundingClientRect();hoverX=e.clientX-r.left;hoverY=e.clientY-r.top;},{passive:true});
      box.addEventListener('pointerleave',()=>{hoverX=-9999;hoverY=-9999;},{passive:true});
    }
    const paint=(ms)=>{
      frame=requestAnimationFrame(paint);
      if (ms-last<42 || !viewable || document.hidden || !isMotionOn()) return;
      last=ms; ctx.clearRect(0,0,w,h);
      const dark=root.dataset.sbColorTheme !== 'light';
      const base=dark ? [114,173,247] : [78,92,192];
      const wave=ms*.00023;
      const lines=Math.max(9,Math.min(16,Math.floor(h/36)));
      for(let i=0;i<lines;i++) {
        const yy=(i+1)*h/(lines+1);
        ctx.beginPath();
        for(let x=0;x<=w+14;x+=14) {
          let y=yy+Math.sin(x*.010+wave+i*.54)*(14+i*.25)+Math.sin(x*.018-wave*.6+i*.3)*6;
          const distance=Math.hypot(x-hoverX,yy-hoverY);
          if(distance<170) y-=Math.exp(-Math.pow(distance/118,2))*21;
          if(x===0) ctx.moveTo(x,y); else ctx.lineTo(x,y);
        }
        ctx.strokeStyle=`rgba(${base.join(',')},${(dark ? .11 : .15)*(1-Math.abs(i-lines*.55)/lines*.58)})`;
        ctx.lineWidth=(i%4===0)?1.3:.85;ctx.stroke();
      }
    };
    frame=requestAnimationFrame(paint);
    canvas.addEventListener('DOMNodeRemoved',()=>cancelAnimationFrame(frame),{once:true});
  });

  // Responsive pointer spotlight, with no touch listeners and no global mouse trail.
  if (finePointer) {
    $$('[data-app-card]').forEach((card) => {
      let raf=0;
      const update=(event)=>{
        if (!isMotionOn()) return;
        const r=card.getBoundingClientRect();
        const x=(event.clientX-r.left)/r.width, y=(event.clientY-r.top)/r.height;
        if(raf) cancelAnimationFrame(raf);
        raf=requestAnimationFrame(()=>{
          card.style.setProperty('--v6-px',(x*100).toFixed(2)+'%');
          card.style.setProperty('--v6-py',(y*100).toFixed(2)+'%');
          card.style.setProperty('--v6-tx',((x-.5)*4).toFixed(2)+'deg');
          card.style.setProperty('--v6-ty',((.5-y)*3).toFixed(2)+'deg');
        });
      };
      card.addEventListener('pointermove',update,{passive:true});
      card.addEventListener('pointerleave',()=>{
        if(raf) cancelAnimationFrame(raf);
        card.style.setProperty('--v6-tx','0deg');card.style.setProperty('--v6-ty','0deg');
      },{passive:true});
    });
    $$('[data-sb-v6-stage]').forEach((stage) => {
      const parent=stage.closest('[data-sb-v6-hero]'); if(!parent)return;
      parent.addEventListener('pointermove',e=>{
        if(!isMotionOn()) return;
        const r=parent.getBoundingClientRect();
        const dx=Math.max(-1,Math.min(1,(e.clientX-r.left)/r.width*2-1));
        const dy=Math.max(-1,Math.min(1,(e.clientY-r.top)/r.height*2-1));
        stage.style.transform=`perspective(1000px) rotateY(${(dx*3).toFixed(1)}deg) rotateX(${(-dy*3).toFixed(1)}deg)`;
      },{passive:true});
      parent.addEventListener('pointerleave',()=>{stage.style.transform='';},{passive:true});
    });
  }
})();
