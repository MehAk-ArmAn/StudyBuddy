/* StudyBuddy W26 — user-supplied visual library.
 * Decorative enhancement ONLY. Laravel owns the routes, authentication, CMS and data.
 * W19 Galaxy, W21 scroll, W22 Antigravity, W24 GooeyNav and W25 React Bits are untouched.
 */
(() => {
  'use strict';
  if (window.__StudyBuddyW26Loaded) return;
  window.__StudyBuddyW26Loaded = true;
  const base = '/assets/studybuddy-w26/images/';
  const scenes = [
    ['scene-galaxy-learning', 'Learning takes flight', 'A galaxy of playful discoveries'],
    ['scene-enchanted-library', 'Stories everywhere', 'Curiosity around every corner'],
    ['scene-dreamlike-classroom', 'Beyond the classroom', 'A universe to explore'],
    ['scene-galaxy-dolphin', 'Adventure begins', 'A whole world waiting to unfold'],
    ['scene-study-friends', 'Better together', 'Every learner has a place'],
    ['scene-neon-adventure', 'Take your next leap', 'A brighter kind of learning'],
    ['scene-book-galaxy', 'A world in every book', 'Imagination has no limits'],
    ['scene-mascots-constellation', 'The StudyBuddy universe', 'Meet the familiar faces'],
    ['scene-curiosity-universe', 'Follow your wonder', 'There is always something new'],
    ['scene-whimsical-library', 'Find your story', 'Each journey starts somewhere']
  ];
  const friends = [
    ['dolphin-starlight','Starry storytime'], ['dolphin-numbers','Numbers are fun'],
    ['fox-storybook','Word adventures'], ['globe-explorer','Around the world'],
    ['dolphin-mountain','Find your path'], ['family-shield','Caring together'],
    ['owl-teacher','Little lessons'], ['star-achievement','Celebrate progress'],
    ['dolphin-hello','Hello, wonder'], ['dolphin-math','Math adventures'],
    ['fox-alphabet','Letter discovery'], ['globe-worlds','New horizons'],
    ['family-heart','Support and kindness'], ['owl-lessons','Learn together'],
    ['dolphin-portal','Next discovery'], ['star-celebration','Bright little wins'],
    ['cosmic-planets','Space explorers'], ['cosmic-sticker-sheet','Meet the whole crew']
  ];
  const url = id => `${base}${id}.webp`;
  const esc = text => String(text).replace(/[&<>"']/g, match => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[match]));
  const motionReduced = () => window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches;
  const make = (tag, attrs={}, html='') => {
    const el=document.createElement(tag);
    for (const [key,value] of Object.entries(attrs)) {
      if (key==='className') el.className=value;
      else if (key==='textContent') el.textContent=value;
      else if (value!==null && value!==undefined) el.setAttribute(key,String(value));
    }
    if (html) el.innerHTML=html;
    return el;
  };
  function addHome(main) {
    if (document.getElementById('sb26-visual-worlds')) return;
    const portal=make('section',{id:'sb26-visual-worlds',className:'sb26-portal', 'aria-label':'StudyBuddy illustrated learning universe'});
    portal.innerHTML=`
      <div class="sb26-portal__intro"><p class="sb26-eyebrow">AN ILLUSTRATED UNIVERSE</p>
        <h2>Wonder looks <em>different</em> for everyone.</h2>
        <p>Travel through the scenes and characters of StudyBuddy. Your real learning apps are always one click away.</p></div>
      <div class="sb26-cinema" aria-roledescription="carousel" aria-label="Illustrated StudyBuddy worlds">
        <img class="sb26-cinema__scene" src="${url(scenes[0][0])}" alt="Cosmic illustrated StudyBuddy learning scene" loading="lazy" decoding="async">
        <div class="sb26-cinema__fade" aria-hidden="true"></div>
        <div class="sb26-cinema__copy" aria-live="polite"><span class="sb26-cinema__number">01 / 10</span>
          <h3>${esc(scenes[0][1])}</h3><p>${esc(scenes[0][2])}</p>
          <a href="/apps" class="sb26-pill">Explore learning apps <span aria-hidden="true">↗</span></a></div>
        <div class="sb26-cinema__character" aria-hidden="true"><img src="${url(friends[0][0])}" alt="" loading="lazy" decoding="async"></div>
        <div class="sb26-cinema__arrows"><button type="button" class="sb26-arrow" data-sb26-prev aria-label="Previous world">←</button>
          <button type="button" class="sb26-arrow" data-sb26-next aria-label="Next world">→</button></div>
      </div>
      <div class="sb26-scenes" role="group" aria-label="Choose a scene"></div>
      <div class="sb26-friends">
        <div class="sb26-friends__heading"><div><p class="sb26-eyebrow">MEET THE LITTLE WONDERS</p><h3>A universe of <em>familiar faces.</em></h3></div>
          <p>Playful illustrations for curiosity, imagination, discovery and growth.</p></div>
        <div class="sb26-friends__rail" role="group" aria-label="StudyBuddy character artwork"></div>
      </div>`;
    const strip=portal.querySelector('.sb26-scenes');
    scenes.forEach(([id,title],i)=>{
      const b=make('button',{type:'button',className:'sb26-scene-thumb', 'aria-label':`Show illustration ${i+1}: ${title}`,'aria-pressed':i===0?'true':'false'},
        `<img src="${url(id)}" alt="" loading="lazy" decoding="async"><span>${String(i+1).padStart(2,'0')}</span>`);
      b.addEventListener('click',()=>pick(i));strip.appendChild(b);
    });
    const rail=portal.querySelector('.sb26-friends__rail');
    friends.forEach(([id,title],i)=>{
      const tile=make('figure',{className:'sb26-friend'},
        `<div class="sb26-friend__art"><img loading="lazy" decoding="async" src="${url(id)}" alt="${esc(title)} StudyBuddy illustration"></div><figcaption><span>${String(i+1).padStart(2,'0')}</span>${esc(title)}</figcaption>`);
      rail.appendChild(tile);
    });
    const sceneImg=portal.querySelector('.sb26-cinema__scene');
    const character=portal.querySelector('.sb26-cinema__character img');
    const title=portal.querySelector('.sb26-cinema__copy h3');
    const description=portal.querySelector('.sb26-cinema__copy p');
    const count=portal.querySelector('.sb26-cinema__number');
    let current=0;
    function pick(index) {
      current=(index+scenes.length)%scenes.length;
      const [id,heading,sub]=scenes[current];
      sceneImg.src=url(id);
      sceneImg.alt=`${heading}: illustrated StudyBuddy scene`;
      title.textContent=heading; description.textContent=sub;
      count.textContent=`${String(current+1).padStart(2,'0')} / 10`;
      character.src=url(friends[[0,4,6,9,5,14,10,7,3,12][current]][0]);
      strip.querySelectorAll('button').forEach((el,i)=>el.setAttribute('aria-pressed',String(i===current)));
    }
    portal.querySelector('[data-sb26-prev]').addEventListener('click',()=>pick(current-1));
    portal.querySelector('[data-sb26-next]').addEventListener('click',()=>pick(current+1));
    // Insert directly after the existing Worlds section. Never overwrite its carousel/content.
    const worlds=document.getElementById('worlds');
    let anchor=worlds?.closest('section')||worlds;
    if (anchor && main.contains(anchor) && anchor!==main) anchor.insertAdjacentElement('afterend',portal);
    else main.appendChild(portal);
  }
  const routes=[
    {pattern:/^\/(?:apps|learning-hub)(?:\/)?$/,scene: 'scene-neon-adventure',mascot:'dolphin-numbers',name:'A whole universe to explore',copy:'Find the learning experiences that speak to your curiosity.',href:'/apps',action:'Explore apps'},
    {pattern:/^\/roles\/?$/,scene:'scene-study-friends',mascot:'family-shield',name:'Your journey is your own',copy:'Explore different ways to learn, support and grow.',href:'/roles',action:'Explore learning roles'},
    {pattern:/^\/(?:for-schools|schools)(?:\/)?$/,scene:'scene-dreamlike-classroom',mascot:'owl-teacher',name:'Let curiosity fill the classroom',copy:'See how StudyBuddy can fit different learning communities.',href:'/for-schools',action:'Explore StudyBuddy for schools'},
    {pattern:/^\/(?:login|register)(?:\/)?$/,scene:'scene-enchanted-library',mascot:'dolphin-starlight',name:'Every adventure begins somewhere',copy:'Your learning story belongs to you.',href:'/apps',action:'See the learning worlds',compact:true},
    {pattern:/^\/(?:dashboard|my-quest)(?:\/)?$/,scene:'scene-book-galaxy',mascot:'star-achievement',name:'Keep following your curiosity',copy:'Your own learning journey continues here.',href:'/apps',action:'Explore learning apps',compact:true},
    {pattern:/^\/family\/learners(?:\/)?$/,scene:'scene-whimsical-library',mascot:'family-heart',name:'Every learner has a story',copy:'A private space to help your family grow.',href:'/roles',action:'See learning roles',compact:true},
    {pattern:/^\/(?:community|search)(?:\/)?$/,scene:'scene-curiosity-universe',mascot:'fox-storybook',name:'Find another little wonder',copy:'Every new discovery starts with a question.',href:'/apps',action:'Browse apps',compact:true}
  ];
  function addRoute(main,path) {
    const info=routes.find(r=>r.pattern.test(path));
    if (!info || document.getElementById('sb26-route-art')) return;
    const wrap=make('section',{id:'sb26-route-art',className:'sb26-route-art'+(info.compact?' sb26-route-art--compact':''),'aria-label':'StudyBuddy character illustration'},
      `<img class="sb26-route-art__scene" src="${url(info.scene)}" alt="" loading="lazy" decoding="async" aria-hidden="true">
      <div class="sb26-route-art__veil"></div>
      <div class="sb26-route-art__text"><p class="sb26-eyebrow">THE STUDYBUDDY UNIVERSE</p><h2>${esc(info.name)}</h2><p>${esc(info.copy)}</p><a href="${info.href}" class="sb26-pill">${esc(info.action)} <span aria-hidden="true">↗</span></a></div>
      <img class="sb26-route-art__character" src="${url(info.mascot)}" alt="" aria-hidden="true" loading="lazy" decoding="async">`);
    // Auth/private forms stay first; put decoration at end. On editorial pages, place after first major section.
    if (info.compact) { main.appendChild(wrap); return; }
    const first=main.querySelector('section');
    if (first && main.contains(first)) {
      const major=Array.from(main.children).find(el=>el===first || el.contains(first));
      if (major && major!==main) major.insertAdjacentElement('afterend',wrap);
      else main.appendChild(wrap);
    } else main.appendChild(wrap);
  }
  function init() {
    const main=document.querySelector('main')||document.querySelector('#main')||document.querySelector('[role="main"]');
    if(!main) return;
    const path=location.pathname.replace(/\/$/,'')||'/';
    if(path==='/') addHome(main);
    else addRoute(main,path);
    const section=document.querySelector('#sb26-visual-worlds,#sb26-route-art');
    // Reveal only when visible. Honor reduced-motion accessibility.
    if(section && !motionReduced() && 'IntersectionObserver' in window) {
      section.classList.add('sb26-ready');
      const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{
        if(entry.isIntersecting){entry.target.classList.add('sb26-visible');observer.unobserve(entry.target);}
      }),{threshold:.05}); observer.observe(section);
    }
    window.StudyBuddyVisuals26={diagnostics:()=>({version:'W26',path,sectionMounted:!!section,artworkCount:28,sceneCount:scenes.length,characterCount:friends.length,
      galaxyPreserved:!!document.querySelector('.galaxy-container,[data-sb19-galaxy],canvas.galaxy-canvas'),
      w25ScriptPresent:!!document.querySelector('script[src*="studybuddy-visuals.js"]')})};
  }
  if (document.readyState==='loading') document.addEventListener('DOMContentLoaded',init,{once:true}); else init();
})();
