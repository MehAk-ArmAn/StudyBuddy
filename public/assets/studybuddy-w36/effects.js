/* StudyBuddy W36 original accessible cursor effects, no React Bits Pro source.
 * Preferences only (no personal identity) are stored in localStorage.
 */
(()=>{
 'use strict';
 if(window.__sb36EffectsBooted)return;window.__sb36EffectsBooted=true;
 const fine=matchMedia('(pointer:fine)').matches;
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 if(!fine)return; // No cursor overlay on touchscreens.
 const storageKey='sb36:cursor-mode:v1';
 const modes=['off','smooth','celebration','name','spark'];
 let mode='off';try{const s=localStorage.getItem(storageKey);mode=modes.includes(s)?s:'off';}catch{}
 const host=document.createElement('div');host.className='sb36-effects-ui';host.innerHTML='<button class="sb36-effects-launch" type="button" aria-controls="sb36-effects-menu" aria-expanded="false">✦ Effects</button><div class="sb36-effects-menu" id="sb36-effects-menu" hidden><strong>Cursor style</strong><p>Optional • desktop only</p><div class="sb36-effects-grid"></div></div>';
 document.body.append(host);
 const launcher=host.querySelector('button'),menu=host.querySelector('.sb36-effects-menu'),grid=host.querySelector('.sb36-effects-grid');
 const pretty={off:'Off',smooth:'Smooth',celebration:'Confetti',name:'Name tag',spark:'Click spark'};
 modes.forEach(key=>{const b=document.createElement('button');b.type='button';b.textContent=pretty[key];b.dataset.mode=key;grid.append(b);b.addEventListener('click',()=>{mode=key;try{localStorage.setItem(storageKey,key);}catch{};sync();menu.hidden=true;launcher.setAttribute('aria-expanded','false');launcher.focus();});});
 launcher.addEventListener('click',()=>{menu.hidden=!menu.hidden;launcher.setAttribute('aria-expanded',String(!menu.hidden));});
 document.addEventListener('pointerdown',e=>{if(!host.contains(e.target))menu.hidden=true;});
 document.addEventListener('keydown',e=>{if(e.key==='Escape'&&!menu.hidden){menu.hidden=true;launcher.setAttribute('aria-expanded','false');launcher.focus();}});
 const trail=document.createElement('div');trail.className='sb36-cursor-dot';trail.innerHTML='<span class="sb36-cursor-label">You</span>';trail.setAttribute('aria-hidden','true');document.body.append(trail);
 let x=-90,y=-90,tx=-90,ty=-90,raf=0;
 const enabled=()=>mode!=='off'&&!reduced.matches;
 const render=()=>{if(!enabled()){trail.style.opacity='0';raf=0;return;}x+=(tx-x)*.23;y+=(ty-y)*.23;trail.style.transform=`translate3d(${x}px,${y}px,0)`;trail.style.opacity='1';raf=requestAnimationFrame(render);};
 const sync=()=>{trail.classList.toggle('sb36-is-name',mode==='name');trail.classList.toggle('sb36-is-smooth',mode==='smooth');trail.style.opacity=enabled()?'1':'0';grid.querySelectorAll('button').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.mode===mode)));if(enabled()&&!raf)raf=requestAnimationFrame(render);};
 document.addEventListener('pointermove',e=>{if(e.pointerType==='mouse'){tx=e.clientX;ty=e.clientY;if(enabled()&&!raf)raf=requestAnimationFrame(render);}}, {passive:true});
 function burst(clientX,clientY,confetti){if(!enabled()||(mode!=='celebration'&&mode!=='spark'))return;const count=confetti?16:8;
  for(let i=0;i<count;i++){
   const p=document.createElement('span');p.className='sb36-particle';const angle=Math.PI*2*i/count+Math.random()*.3;const dist=confetti?36+Math.random()*66:15+Math.random()*28;
   p.style.cssText=`left:${clientX}px;top:${clientY}px;--sb36-dx:${Math.cos(angle)*dist}px;--sb36-dy:${Math.sin(angle)*dist+ (confetti?24:0)}px;--sb36-spin:${Math.round(Math.random()*480)}deg;--sb36-hue:${Math.round(Math.random()*330)};--sb36-t:${confetti?850:560}ms`;
   document.body.appendChild(p);p.addEventListener('animationend',()=>p.remove(),{once:true});setTimeout(()=>p.remove(),1100);
  }
 }
 document.addEventListener('click',e=>{if(host.contains(e.target))return;burst(e.clientX,e.clientY,mode==='celebration');});
 window.addEventListener('sb36:celebrate',()=>{if(mode==='celebration')burst(Math.min(innerWidth-80,innerWidth*.68),Math.min(innerHeight-90,innerHeight*.7),true);});
 reduced.addEventListener?.('change',sync);sync();
})();
