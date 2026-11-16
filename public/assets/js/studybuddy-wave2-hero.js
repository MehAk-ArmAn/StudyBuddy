/* StudyBuddy original hero polish. Presentation only, no route or data logic. */
(()=>{
  const hero = document.querySelector('.sb7.sb12-home .sb7-hero');
  if (!hero || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const visual = hero.querySelector('.sb7-hero__visual');
  if (!visual) return;
  let frame=0;
  hero.addEventListener('pointermove',event=>{
    if(event.pointerType==='touch' || document.documentElement.classList.contains('sb-u-motion-off'))return;
    if(frame)cancelAnimationFrame(frame);
    frame=requestAnimationFrame(()=>{
      const r=visual.getBoundingClientRect();
      const x=Math.max(-1,Math.min(1,(event.clientX-r.left-r.width/2)/(r.width/2)));
      const y=Math.max(-1,Math.min(1,(event.clientY-r.top-r.height/2)/(r.height/2)));
      visual.style.setProperty('--sb12-mouse-x',(x*8).toFixed(2)+'px');
      visual.style.setProperty('--sb12-mouse-y',(y*8).toFixed(2)+'px');
    });
  },{passive:true});
  hero.addEventListener('pointerleave',()=>{visual.style.setProperty('--sb12-mouse-x','0px');visual.style.setProperty('--sb12-mouse-y','0px');});
})();
