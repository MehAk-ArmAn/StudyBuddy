/* Same original visual DOM/CSS, with local behavior instead of the Base44 SDK. */
(() => {
  'use strict';
  const $ = (sel,root=document) => root.querySelector(sel);
  const $$ = (sel,root=document) => Array.from(root.querySelectorAll(sel));
  const motion = !matchMedia('(prefers-reduced-motion: reduce)').matches;
  const log = (message) => console.info('[StudyBuddy exact visual]',message);
  // Downloaded page had empty Base44 Image component placeholders. These img tags load the
  // original public images, with local artwork fallbacks when the remote CDN is inaccessible.
  $$('img[data-sb-fallback]').forEach(img => {
    const fallback = () => { if(!img.dataset.sbFallbackUsed) {
      img.dataset.sbFallbackUsed = 'true'; img.src = img.dataset.sbFallback; 
    }};
    img.addEventListener('error',fallback,{once:true});
    if(img.complete && img.naturalWidth===0)fallback();
  });
  // Bring SSR-only entrance elements into view: their initial opacity was zero until React hydrated.
  $$('[style*="opacity:0"]:not([data-base44-image])').forEach(el=>{
    el.dataset.sbReveal='visible';
  });
  // Theme control, honoring system choice on first load and browser preference thereafter.
  const themeBtn=$('button[aria-label^="Switch to"]');
  const key='studybuddy-exact-theme';
  try {
    const saved=localStorage.getItem(key);
    if(saved==='light'||saved==='dark')document.documentElement.classList.toggle('dark',saved==='dark');
  }catch(e){}
  const updateTheme=()=>{if(themeBtn)themeBtn.setAttribute('aria-label',document.documentElement.classList.contains('dark')?'Switch to light theme':'Switch to dark theme');};
  updateTheme();
  themeBtn?.addEventListener('click',()=>{
    document.documentElement.classList.toggle('dark');updateTheme();
    try{localStorage.setItem(key,document.documentElement.classList.contains('dark')?'dark':'light');}catch(e){}
  });
  // Do not insert a second navigation header: use the same original mobile-menu button.
  const menuBtn=$('button[aria-label="Open menu"]');
  const drawer=document.createElement('nav');
  drawer.className='sb44-mobile-panel';drawer.hidden=true;drawer.setAttribute('aria-label','Mobile site navigation');
  [['Apps','/apps'],['Roles','/roles'],['For schools','/for-schools'],['How it works','/#story'],['My dashboard','/dashboard'],['Sign in','/login']].forEach(([label,url])=>{
    const a=document.createElement('a');a.href=url;a.textContent=label;drawer.appendChild(a);
  });
  if(menuBtn){document.body.appendChild(drawer);
    const close=()=>{drawer.hidden=true;menuBtn.setAttribute('aria-expanded','false');};
    menuBtn.addEventListener('click',()=>{drawer.hidden=!drawer.hidden;menuBtn.setAttribute('aria-expanded',String(!drawer.hidden));});
    document.addEventListener('keydown',ev=>{if(ev.key==='Escape')close();});
    document.addEventListener('click',ev=>{if(!drawer.hidden&&!drawer.contains(ev.target)&&!menuBtn.contains(ev.target))close();});
  }
  // Original FAQ sections were React Radix accordions. Use their exact markup and styles.
  $$('button[id^="radix-"]').forEach(button=>{
    const contentId=button.getAttribute('aria-controls');
    const content=(contentId?document.getElementById(contentId):null) || button.parentElement?.parentElement?.querySelector('[role=region]');
    if(!content)return;
    button.addEventListener('click',()=>{
      const opening=button.getAttribute('aria-expanded')!=='true';
      $$('button[id^="radix-"]').forEach(b=>{
        b.setAttribute('aria-expanded','false');b.dataset.state='closed';
        b.parentElement?.setAttribute('data-state','closed');
      });
      $$('[id^="radix-"][role="region"]').forEach(r=>{r.hidden=true;r.dataset.state='closed';});
      button.setAttribute('aria-expanded',String(opening)); button.dataset.state=opening?'open':'closed';
      button.parentElement?.setAttribute('data-state',opening?'open':'closed');
      content.hidden=!opening;content.dataset.state=opening?'open':'closed';
    });
  });
  // Use the exact image/card strips present in the original HTML.
  function findScrollable(button){
    const scope=button.closest('section') || document;
    return $$('*',scope).filter(e=>e.scrollWidth>e.clientWidth+130 && /auto|scroll/.test(getComputedStyle(e).overflowX)).sort((a,b)=>b.scrollWidth-a.scrollWidth)[0];
  }
  [['Previous world',-1],['Next world',1],['Scroll apps left',-1],['Scroll apps right',1]].forEach(([label,direction])=>{
    const button=$('button[aria-label="'+label+'"]'); if(!button)return;
    button.addEventListener('click',()=>{
      const strip=findScrollable(button);
      if(strip)strip.scrollBy({left:direction*(strip.clientWidth*.68),behavior:motion?'smooth':'instant'});
      else {
        const section=button.closest('section');
        const cards=$$('a[href*="world="],a[href^="/apps/"]',section||document);
        if(cards.length){const active=cards.findIndex(e=>e.dataset.active==='yes');
          const next=(active+direction+cards.length)%cards.length;
          cards.forEach(e=>e.dataset.active='no');cards[next].dataset.active='yes';
          cards[next].scrollIntoView({behavior:motion?'smooth':'instant',inline:'center',block:'nearest'});
        }
      }
    });
  });
  // If the snapshot has form controls, direct search to the real Laravel catalogue.
  $$('form').forEach(f=>{
    const input=$('input[type="search"],input[name="query"],input[name="q"]',f);
    if(!input)return;
    f.addEventListener('submit',ev=>{ev.preventDefault();const q=input.value.trim();location.href='/apps'+(q?'?search='+encodeURIComponent(q):'');});
  });
  window.StudyBuddyExactVisual={source:'user-provided Base44 snapshot',mode:'Laravel-owned snapshot, no Base44 SDK',version:'W12'};
  log('Original saved HTML + original CSS loaded; Laravel routes remain in control.');
})();
