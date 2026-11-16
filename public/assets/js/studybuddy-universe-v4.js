/* StudyBuddy Universe v4. All existing links/forms/data remain native. */
(() => {
  'use strict';
  const root=document.documentElement, body=document.body;
  if(!body)return;
  const reduced=window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches??false;
  const hover=window.matchMedia?.('(hover:hover) and (pointer:fine)')?.matches??false;
  const saveData=navigator.connection?.saveData??false;
  try { if(localStorage.getItem('studybuddy-universe-motion')==='off')root.classList.add('sb-u-motion-off');}catch(_){ }
  const noMotion=()=>reduced||saveData||root.classList.contains('sb-u-motion-off');
  const toggle=document.querySelector('[data-sb-motion-toggle]');
  const renderMotion=()=>{
    if(!toggle)return;
    const active=!noMotion();toggle.setAttribute('aria-pressed',String(active));
    toggle.setAttribute('aria-label',active?'Turn off Universe visual effects':'Turn on Universe visual effects');
    toggle.title=active?'Turn off Universe visual effects':'Turn on Universe visual effects';
    const label=toggle.querySelector('[data-sb-motion-label]');if(label)label.textContent=active?'Effects on':'Effects off';
  };
  toggle?.addEventListener('click',()=>{
    const turnOff=!root.classList.contains('sb-u-motion-off');
    root.classList.toggle('sb-u-motion-off',turnOff);
    try{localStorage.setItem('studybuddy-universe-motion',turnOff?'off':'on');}catch(_){ }
    if(turnOff) document.querySelectorAll('.sb-u-reveal-ready').forEach(el=>el.classList.add('sb-u-visible'));
    renderMotion();
  });renderMotion();
  // Existing navigation remains in place; this quick launcher is purely optional.
  const opener=document.querySelector('[data-sb-world-open]');
  const panel=document.querySelector('[data-sb-world-panel]');
  const close=document.querySelector('[data-sb-world-close]');
  function setOpen(open,focus=true){if(!opener||!panel)return;panel.hidden=!open;opener.setAttribute('aria-expanded',String(open));if(focus)(open?close:opener)?.focus();}
  opener?.addEventListener('click',()=>setOpen(panel?.hidden??true));
  close?.addEventListener('click',()=>setOpen(false));
  document.addEventListener('keydown',e=>{if(e.key==='Escape'&&panel&&!panel.hidden)setOpen(false);});
  document.addEventListener('pointerdown',e=>{
    if(!panel||panel.hidden||panel.contains(e.target)||opener?.contains(e.target))return;
    setOpen(false,false);
  },{passive:true});
  // Scroll indicator coalesces DOM changes to 1/frame.
  const progress=document.querySelector('[data-sb-scroll-progress]');let scheduled=false;
  const update=()=>{scheduled=false;if(!progress)return;const max=document.documentElement.scrollHeight-innerHeight;progress.style.setProperty('--sbu-progress',String(max>0?Math.min(1,Math.max(0,scrollY/max)):0));};
  window.addEventListener('scroll',()=>{if(!scheduled){scheduled=true;requestAnimationFrame(update);}}, {passive:true});update();
  // Cosmetic pointer parallax. No form, school, admin, or auth interaction changes.
  if(hover&&!reduced&&!saveData&&!['admin','auth'].includes(body.dataset.sbUniverseZone)){
    let frame=0,px=0,py=0;
    document.addEventListener('pointermove',e=>{
      if(noMotion())return;
      px=(e.clientX/Math.max(1,innerWidth)-.5)*2;py=(e.clientY/Math.max(1,innerHeight)-.5)*2;
      if(frame)return;
      frame=requestAnimationFrame(()=>{frame=0;root.style.setProperty('--sbu-px',px.toFixed(3));root.style.setProperty('--sbu-py',py.toFixed(3));});
    },{passive:true});
  }
  // Content never disappears if JS is disabled: reveal classes are added only after observing.
  if('IntersectionObserver'in window&&!reduced&&!saveData&&body.classList.contains('studybuddy-site')){
    const sel='.sb-next-home > section, .sb-schools > section, .sb-catalog-v3 > section, .sb-app-detail-v3 > section';
    const io=new IntersectionObserver(items=>items.forEach(entry=>{
      if(entry.isIntersecting){entry.target.classList.add('sb-u-visible');io.unobserve(entry.target);}
    }),{threshold:.05,rootMargin:'0px 0px 65px 0px'});
    document.querySelectorAll(sel).forEach(el=>{
      if(el.matches('.sb-next-hero,.sb-schools-hero,.sb-catalog-v3__hero')||el.getBoundingClientRect().height<55)return;
      el.setAttribute('data-sb-reveal','');
      if(noMotion()||el.getBoundingClientRect().top<innerHeight-20)el.classList.add('sb-u-visible');
      el.classList.add('sb-u-reveal-ready');
      if(!el.classList.contains('sb-u-visible'))io.observe(el);
    });
  }
  if(hover&&!reduced&&!saveData&&body.classList.contains('studybuddy-site')){
    document.querySelectorAll('.lively-card,.preview-card,.path-card,.what-card,.school-preview-card,.personalization-card').forEach(card=>{
      let frame=0;
      card.addEventListener('pointermove',e=>{
        if(noMotion())return;
        if(frame)cancelAnimationFrame(frame);
        const box=card.getBoundingClientRect();
        frame=requestAnimationFrame(()=>{
          frame=0;
          card.style.setProperty('--sbu-tilt-x',((e.clientX-box.left)/Math.max(1,box.width)-.5)*4);
          card.style.setProperty('--sbu-tilt-y',(.5-(e.clientY-box.top)/Math.max(1,box.height))*4);
        });
      },{passive:true});
      card.addEventListener('pointerleave',()=>{if(frame)cancelAnimationFrame(frame);card.style.removeProperty('--sbu-tilt-x');card.style.removeProperty('--sbu-tilt-y');},{passive:true});
    });
  }
  // Explicit fictional preview: real catalogue filtering via existing ?category= endpoint.
  const gateway=document.querySelector('[data-sb-gateway]');
  if(gateway){
    const stage=gateway.querySelector('[data-sb-gateway-stage]');
    const title=gateway.querySelector('[data-sb-world-title]');
    const badge=gateway.querySelector('[data-sb-world-badge]');
    const desc=gateway.querySelector('[data-sb-world-description]');
    const link=gateway.querySelector('[data-sb-world-link]');
    const buttons=Array.from(gateway.querySelectorAll('[data-sb-world-choice]'));
    const worlds={
      maths:['WORLD 01 / MATHS','Number Odyssey','Little challenges. Big discoveries. Find learning activities that help numbers make sense.','/apps?category=Mathematics','Explore maths apps'],
      science:['WORLD 02 / SCIENCE','Science Sprouts','From living things to big questions, follow your curiosity through discovery and imagination.','/apps?category=Science','Explore science apps'],
      reading:['WORLD 03 / READING','Word Whirl','Turn words and letters into little adventures. Discover literacy activities across the catalogue.','/apps?category=English','Explore English apps'],
      portal:['ALL LEARNING WORLDS','The adventure is yours.','Explore the real StudyBuddy catalogue. Browse subjects and find something new to try.','/apps','See all learning worlds']
    };
    buttons.forEach(button=>button.addEventListener('click',()=>{
      const key=button.dataset.sbWorldChoice,values=worlds[key];if(!stage||!values)return;
      stage.dataset.world=key;if(badge)badge.textContent=values[0];if(title)title.textContent=values[1];if(desc)desc.textContent=values[2];
      if(link){link.href=values[3];link.replaceChildren(document.createTextNode(values[4]+' '));const arrow=document.createElement('span');arrow.setAttribute('aria-hidden','true');arrow.textContent='↗';link.append(arrow);}
      buttons.forEach(b=>b.setAttribute('aria-pressed',String(b===button)));
      if(!noMotion()){stage.classList.remove('is-switching');void stage.offsetWidth;stage.classList.add('is-switching');}
    }));
  }
  // School pilot concept switcher — no real classroom data or permissions exposed.
  const school=document.querySelector('[data-sb-school-roles]');
  if(school){
    const tabs=Array.from(school.querySelectorAll('[data-sb-school-tab]'));
    const panel=school.querySelector('#sb-u-school-panel');
    const kicker=school.querySelector('[data-sb-school-kicker]');
    const title=school.querySelector('[data-sb-school-title]');
    const desc=school.querySelector('[data-sb-school-description]');
    const values={
      leader:['PILOT DESIGN CONCEPT / 01','A school space with real boundaries.','Human-reviewed organization applications, private staff membership, and controlled invitation paths.'],
      teacher:['PILOT DESIGN CONCEPT / 02','A calmer hub for educators.','Imagine sharing a curated path to suitable learning worlds. Full class assignment and analytics workflows need further development.'],
      family:['PILOT DESIGN CONCEPT / 03','A window into learning.','Families could discover suitable activities and learning information. Child privacy and consent must be reviewed before expanding family tools.']
    };
    const activate=(i,focus=false)=>{
      const tab=tabs[i];if(!tab)return;
      const info=values[tab.dataset.sbSchoolTab];if(!info)return;
      tabs.forEach((b,j)=>{b.setAttribute('aria-selected',String(i===j));b.tabIndex=i===j?0:-1;});
      if(panel){panel.dataset.role=tab.dataset.sbSchoolTab;panel.setAttribute('aria-labelledby',tab.id);}
      if(kicker)kicker.textContent=info[0];if(title)title.textContent=info[1];if(desc)desc.textContent=info[2];
      if(focus)tab.focus();
    };
    tabs.forEach((tab,i)=>{
      tab.addEventListener('click',()=>activate(i));
      tab.addEventListener('keydown',e=>{
        if(!['ArrowLeft','ArrowRight','Home','End'].includes(e.key))return;
        e.preventDefault();
        const next=e.key==='Home'?0:e.key==='End'?tabs.length-1:e.key==='ArrowRight'?(i+1)%tabs.length:(i-1+tabs.length)%tabs.length;
        activate(next,true);
      });
    });
  }
})();
