/* StudyBuddy V5: vanilla JS progressive enhancements. Existing catalogue and role logic remain authoritative. */
(function(){
'use strict';
var root=document.documentElement;
root.classList.add('sb-v5-js');
var allowed=['light','dark','system'];
var stored='system';
try{var initial=window.localStorage.getItem('studybuddy-color-mode');if(allowed.includes(initial)) stored=initial;}catch(_e){}
var media=window.matchMedia?window.matchMedia('(prefers-color-scheme: dark)'):null;
function applyTheme(mode){
 if(!allowed.includes(mode)) mode='system';
 root.dataset.sbColorMode=mode;
 root.dataset.sbColorTheme=mode==='system' ? ((media&&media.matches)?'dark':'light') : mode;
 document.querySelectorAll('[data-sb-v5-theme]').forEach(function(button){
   button.setAttribute('aria-pressed',button.dataset.sbV5Theme===mode?'true':'false');
 });
 document.querySelectorAll('meta[name="theme-color"]').forEach(function(meta){meta.content=root.dataset.sbColorTheme==='dark'?'#070d22':'#f6f7ff';});
}
document.querySelectorAll('[data-sb-v5-theme]').forEach(function(button){button.addEventListener('click',function(){stored=button.dataset.sbV5Theme;try{window.localStorage.setItem('studybuddy-color-mode',stored);}catch(_e){}applyTheme(stored);});});
if(media){var onSystem=function(){if(stored==='system')applyTheme(stored);};if(media.addEventListener)media.addEventListener('change',onSystem);else if(media.addListener)media.addListener(onSystem);}
applyTheme(stored);

var motionOK=!(window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches);
var motionOn=!root.classList.contains('sb-u-motion-off')&&motionOK;
var observer=null;
if('IntersectionObserver' in window && motionOK){
  observer=new IntersectionObserver(function(entries){entries.forEach(function(entry){if(entry.isIntersecting){entry.target.classList.remove('sb-v5-await');entry.target.classList.add('sb-v5-in');observer.unobserve(entry.target);}});},{threshold:.07,rootMargin:'0px 0px 30px 0px'});
}
// Select a few high-level content surfaces; never hide auth forms, live announcements or navigation.
if(observer){document.querySelectorAll('.sb-catalog-v3__hero,.sb-v5-world-chooser,.sb-app-card-v3,.sb-u-gateway__heading,.sb-u-school-roles__intro,.sb-next-school-spotlight').forEach(function(el){el.classList.add('sb-v5-reveal');if(el.getBoundingClientRect().top<innerHeight*.86){el.classList.add('sb-v5-in');return;}el.classList.add('sb-v5-await');observer.observe(el);});}

// Respect existing category select/filter from studybuddy-apps-v3.js, without another dataset or fetch.
var catalog=document.querySelector('[data-app-catalog]');
if(catalog){
 var category=catalog.querySelector('[data-app-category]');
 var chips=Array.from(catalog.querySelectorAll('[data-v5-category]'));
 function syncChips(){var current=category?category.value:'';chips.forEach(function(button){button.setAttribute('aria-pressed',button.dataset.v5Category===current?'true':'false');});}
 chips.forEach(function(button){button.addEventListener('click',function(){if(!category)return;category.value=button.dataset.v5Category;category.dispatchEvent(new Event('change',{bubbles:true}));syncChips();var grid=catalog.querySelector('[data-app-grid]');if(grid){grid.scrollIntoView({behavior:motionOK&&!root.classList.contains('sb-u-motion-off')?'smooth':'auto',block:'start'});}});});
 if(category)category.addEventListener('change',syncChips);
 catalog.querySelectorAll('[data-app-reset]').forEach(function(button){button.addEventListener('click',function(){window.requestAnimationFrame(syncChips);});});
 syncChips();
 var surprise=catalog.querySelector('[data-v5-surprise]');
 if(surprise){surprise.addEventListener('click',function(){
  var cards=Array.from(catalog.querySelectorAll('[data-app-card]')).filter(function(card){return !card.hidden;});
  if(!cards.length){var reset=catalog.querySelector('[data-app-reset]');if(reset)reset.click();cards=Array.from(catalog.querySelectorAll('[data-app-card]')).filter(function(card){return !card.hidden;});}
  if(!cards.length)return;
  var card=cards[Math.floor(Math.random()*cards.length)];
  card.classList.remove('sb-v5-surprise--picked');void card.offsetWidth;card.classList.add('sb-v5-surprise--picked');
  card.scrollIntoView({behavior:motionOK&&!root.classList.contains('sb-u-motion-off')?'smooth':'auto',block:'center'});
  var link=card.querySelector('a[href]');if(link){setTimeout(function(){try{link.focus({preventScroll:true});}catch(_e){link.focus();}},motionOK?280:0);}
  setTimeout(function(){card.classList.remove('sb-v5-surprise--picked');},1300);
 });}
 // pointer spotlight is per card; no global cursor tracking or expensive canvas.
 if(window.matchMedia&&window.matchMedia('(hover: hover) and (pointer: fine)').matches){
  catalog.querySelectorAll('.sb-app-card-v3').forEach(function(card){card.addEventListener('pointermove',function(e){if(root.classList.contains('sb-u-motion-off'))return;var rect=card.getBoundingClientRect();card.style.setProperty('--v5-mx',(100*(e.clientX-rect.left)/rect.width)+'%');card.style.setProperty('--v5-my',(100*(e.clientY-rect.top)/rect.height)+'%');});});
 }
}
})();
