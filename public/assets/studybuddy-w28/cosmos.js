/* W28 — enhance the user's ONE original StudyBuddy hero. No additional React roots/canvases. */
(() => {
  'use strict';
  if (window.StudyBuddyCosmos28) return;
  const $ = (q, scope = document) => scope.querySelector(q);
  const $$ = (q, scope = document) => [...scope.querySelectorAll(q)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)');
  const color = ['cyan','purple','pink','gold'];
  const bubbles = [
    {x:'8%',y:'24%',d:19,c:0,s:.28,period:6.3},
    {x:'24%',y:'72%',d:13,c:1,s:.12,period:9.4},
    {x:'67%',y:'13%',d:34,c:1,s:.36,period:8.7},
    {x:'91%',y:'62%',d:16,c:2,s:.51,period:7.9},
    {x:'52%',y:'87%',d:12,c:3,s:.27,period:7.5},
    {x:'82%',y:'86%',d:23,c:0,s:.33,period:11.8},
    {x:'43%',y:'16%',d:8,c:2,s:.1,period:7.5}
  ];
  let hero, stage, layer, raf = 0, pointer = [0,0], depth = 0, inView = true;
  function el(tag, cls, parent) {
    const node = document.createElement(tag);
    if(cls) node.className = cls;
    parent.appendChild(node);
    return node;
  }
  function orb(parent, c, planet) {
    const node = el('span',`sb28-orb sb28-orb--${color[c]}${planet?' sb28-orb--planet':''}`,parent);
    return node;
  }
  function buildScene() {
    layer = el('div','sb28-cosmos',hero);
    layer.setAttribute('aria-hidden','true');
    el('span','sb28-aurora sb28-aurora--l',layer);
    el('span','sb28-aurora sb28-aurora--r',layer);
    bubbles.forEach((b,i) => {
      const shell = el('span','sb28-float',layer);
      shell.style.setProperty('--left', b.x);
      shell.style.setProperty('--top', b.y);
      shell.style.setProperty('--size', b.d+'px');
      shell.style.setProperty('--sway', String(b.s));
      shell.style.setProperty('--scroll-x', ((i%2?1:-1)*18)+'px');
      shell.style.setProperty('--scroll-y', (11+i*4)+'px');
      orb(shell,b.c,i===2);
      shell.firstChild.style.setProperty('--duration',b.period+'s');
    });
    [
      ['12%','43%','18px','7.2s','.72'],
      ['70%','16%','12px','9.6s','.52'],
      ['35%','84%','21px','5.5s','.74'],
      ['90%','33%','10px','8.4s','.60']
    ].forEach(row => {
      const n=el('span','sb28-shimmer',layer);
      for (const [key,val] of [['--left',row[0]],['--top',row[1]],['--size',row[2]],['--duration',row[3]],['--opacity',row[4]]])n.style.setProperty(key,val);
    });
    const satellites = el('div','sb28-satellites',stage);
    satellites.setAttribute('aria-hidden','true');
    el('span','sb28-orbit-ring',satellites);
    [
      {x:'11%',y:'21%',d:27,c:2,s:'8s'},
      {x:'82%',y:'11%',d:37,c:1,s:'10s'},
      {x:'87%',y:'72%',d:24,c:3,s:'7.5s'},
      {x:'5%',y:'77%',d:17,c:0,s:'9s'}
    ].forEach(p => {
      const node=el('span','sb28-satellite',satellites);
      for(const [k,v] of [['--x',p.x],['--y',p.y],['--d',p.d+'px'],['--speed',p.s]])node.style.setProperty(k,v);
      orb(node,p.c,false);
    });
  }
  function chooseSingleGalaxy() {
    if (!hero) return;
    const originals = $$('.galaxy-container canvas, #sb19-galaxy-host canvas, canvas.galaxy-canvas',hero);
    const preferred = $('#sb19-galaxy-host canvas',hero) || $('.galaxy-container canvas',hero) || $('canvas.galaxy-canvas',hero);
    originals.forEach(canvas => {
      if (preferred && canvas !== preferred) canvas.dataset.sb28DuplicateGalaxy='1';
      else delete canvas.dataset.sb28DuplicateGalaxy;
    });
    hero.dataset.sb28Starfield = preferred ? 'existing-react-bits-galaxy' : 'waiting-for-existing-galaxy';
  }
  const clamp = (n,min,max)=> Math.min(max,Math.max(min,n));
  function render() {
    raf=0;
    if (!hero || !inView || reduce.matches) return;
    hero.style.setProperty('--sb28-pointer-x',(pointer[0]*15).toFixed(2)+'px');
    hero.style.setProperty('--sb28-pointer-y',(pointer[1]*12).toFixed(2)+'px');
    hero.style.setProperty('--sb28-depth',depth.toFixed(3));
    hero.style.setProperty('--sb28-aura-x',(pointer[0]*4.5).toFixed(2)+'px');
    hero.style.setProperty('--sb28-aura-y',(pointer[1]*3.5).toFixed(2)+'px');
    layer?.querySelectorAll('.sb28-float').forEach((shell,i)=>{
      const b=bubbles[i];
      shell.style.setProperty('--sb28-dx',(pointer[0]*15*b.s + depth*(i%2?1:-1)*18).toFixed(2)+'px');
      shell.style.setProperty('--sb28-dy',(pointer[1]*12*b.s - depth*(11+i*4)).toFixed(2)+'px');
    });
    const sats=stage?.querySelector('.sb28-satellites');
    if(sats){
      sats.style.setProperty('--sb28-sat-x',(-pointer[0]*5).toFixed(2)+'px');
      sats.style.setProperty('--sb28-sat-y',(-pointer[1]*4).toFixed(2)+'px');
      sats.style.setProperty('--sb28-sat-scale',(1+depth*.08).toFixed(3));
    }
  }
  function schedule() { if(!raf)raf=requestAnimationFrame(render); }
  function pointerMove(ev) {
    if (ev.pointerType==='touch' || reduce.matches)return;
    const box=hero.getBoundingClientRect();
    pointer=[clamp(((ev.clientX-box.left)/Math.max(box.width,1)-0.5)*2,-1,1),clamp(((ev.clientY-box.top)/Math.max(box.height,1)-0.5)*2,-1,1)];
    schedule();
  }
  function scrollMotion() {
    if (!hero || reduce.matches)return;
    const box=hero.getBoundingClientRect();
    depth=clamp(-box.top / Math.max(1,box.height),0,1);
    schedule();
  }
  function clickPulse(ev){
    if (reduce.matches || !ev.isTrusted || ev.target.closest('a,button,input,select,textarea,[role="button"]'))return;
    if(!layer)return;
    const box=hero.getBoundingClientRect();
    const pop=el('span','sb28-pop',layer);
    pop.style.setProperty('--pop-x',(ev.clientX-box.left)+'px');
    pop.style.setProperty('--pop-y',(ev.clientY-box.top)+'px');
    pop.addEventListener('animationend',()=>pop.remove(),{once:true});
  }
  function init(){
    // The Base44 export is still the original page; do not synthesize another landing.
    const original = $('.galaxy-container');
    hero = original?.closest('section') || $('#sb19-galaxy-host')?.closest('section');
    stage = hero?.querySelector('.mx-auto.grid.max-w-7xl > :nth-child(2)') || hero?.querySelector('.sb21-hero-grid > :nth-child(2)');
    if (!hero || !stage || !hero.querySelector('h1')) {
      console.warn('[StudyBuddy W28] Recognized landing not present. No changes applied.');
      return;
    }
    if (hero.classList.contains('sb28-hero'))return;
    hero.classList.add('sb28-hero');
    stage.classList.add('sb28-mascot-stage');
    buildScene();
    chooseSingleGalaxy();
    const monitor=new MutationObserver(chooseSingleGalaxy);
    // Only watch the Galaxy mounting area, never all of document (avoids observer loops).
    const galaxyHost = original || $('#sb19-galaxy-host',hero);
    if (galaxyHost) monitor.observe(galaxyHost,{childList:true,subtree:true});
    setTimeout(()=> {chooseSingleGalaxy();monitor.disconnect();},6500);
    const io='IntersectionObserver' in window ? new IntersectionObserver(entries=>{
      inView=entries[0]?.isIntersecting??true;
      hero.classList.toggle('sb28-away',!inView);
      if(inView)scrollMotion();
    },{threshold:0}) : null;
    io?.observe(hero);
    hero.addEventListener('pointermove',pointerMove,{passive:true});
    hero.addEventListener('pointerleave',()=>{pointer=[0,0];schedule();},{passive:true});
    hero.addEventListener('click',clickPulse,{passive:true});
    window.addEventListener('scroll',scrollMotion,{passive:true});
    window.addEventListener('resize',scrollMotion,{passive:true});
    scrollMotion();
    console.info('[StudyBuddy W28] ONE landing / existing React Bits Galaxy / decorative CSS orbs.');
  }
  window.StudyBuddyCosmos28={
    version:'W28',
    diagnostics:()=>({
      version:'W28',
      originalHero:!!hero,
      heroCount: $$('.sb28-hero').length,
      starfield:hero?.dataset.sb28Starfield||'none',
      selectedGalaxyCanvases:hero?$$('.galaxy-container canvas:not([data-sb28-duplicate-galaxy]),#sb19-galaxy-host canvas:not([data-sb28-duplicate-galaxy])',hero).filter((e,i,a)=>a.indexOf(e)===i).length:0,
      activeDuplicateCanvases:hero?$$('canvas[data-sb28-duplicate-galaxy="1"]',hero).length:0,
      cssOrbs:hero?$$('.sb28-orb',hero).length:0,
      extraCanvasesCreated:0,
      W22HiddenOnHome:!!hero?.querySelector('#sb22-antigravity-host'),
      prefersReducedMotion:reduce.matches
    })
  };
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init,{once:true});else init();
})();
