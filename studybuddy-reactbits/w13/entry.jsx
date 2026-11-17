/* StudyBuddy W13: isolated application layer. React Bits / ReactDrops original
 * sources are imported by install.py from the user's own installed project.
 * This file contains only StudyBuddy app-specific glue and React UI, not cloned
 * library components. */
import React, { useEffect, useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { createPortal } from 'react-dom';

const Originals = { FlexCarousel:()=>import("../src/components/FlexCarousel.jsx"), CircularCarousel:()=>import("../src/components/CircularCarousel.jsx"), HoloCard:()=>import("../src/components/HoloCard.jsx"), Galaxy:()=>import("../../resources/js/studybuddy-react/components/StudyBuddyWave8/Galaxy.jsx"), Antigravity:()=>import("../src/components/StudyBuddyOfficial/Antigravity.jsx"), GradientWaves:()=>import("../src/components/StudyBuddyOfficial/GradientWaves.jsx"), MagicRings:()=>import("../src/components/StudyBuddyOfficial/MagicRings.jsx"), WebThreads:()=>import("../src/components/StudyBuddyOfficial/WebThreads.jsx"), LiquidEther:()=>import("../src/components/StudyBuddyOfficial/LiquidEther.jsx"), SloshGauge:()=>import("../src/components/StudyBuddyOfficial/SloshGauge.jsx"), BranchedMenu:()=>import("../../resources/js/studybuddy-react/components/StudyBuddyWave3/micro/BranchedMenu/BranchedMenu.jsx") };
const libraryNames = Object.keys(Originals);
const chooseComponent = (mod, name) => mod?.default || mod?.[name] || Object.values(mod || {}).find(v => typeof v === 'function');
const C = Object.fromEntries(Object.entries(Originals).map(([name,load])=>[name,React.lazy(()=>load().then(mod=>({default:chooseComponent(mod,name)})))]));
const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const isTouch = () => window.matchMedia('(hover: none), (pointer: coarse)').matches;
const rootPath = () => location.pathname.toLowerCase();
const currentPage = () => {
 const p=rootPath();
 if(p === '/')return 'home';
 if(p.includes('/apps'))return 'apps';
 if(p.includes('/roles'))return 'roles';
 if(p.includes('school'))return 'schools';
 if(p.includes('dashboard')||p.includes('my-quest'))return 'dashboard';
 if(p.includes('learning')||p.includes('quest'))return 'learn';
 if(p.includes('/login')||p.includes('/register')||p.includes('password'))return 'auth';
 if(p.startsWith('/admin'))return 'admin';
 return 'info';
};
const safeHref = href => {
 try {const u=new URL(href,location.origin);return u.origin===location.origin && !/^javascript:/i.test(href) ? `${u.pathname}${u.search}${u.hash}`:null;}catch{return null;}
};
const asset = name => `/assets/studybuddy-base44-exact/fallback/${name}`;
const pageItems = (page) => {
 if(page === 'roles')return [
 {title:'Students',subtitle:'Explore learning worlds',href:'/roles#student',image:asset('path-apps.webp')},
 {title:'Families',subtitle:'Learn and grow together',href:'/roles#parent',image:asset('path-parents.webp')},
 {title:'Educators',subtitle:'Classroom learning',href:'/roles#teacher',image:asset('path-teachers.webp')},
 {title:'Independent learners',subtitle:'Find your own rhythm',href:'/roles#independent-learner',image:asset('path-support.webp')}
 ];
 const scope = document.querySelector('main') || document;
 let links=[...scope.querySelectorAll('a[href]')].filter(a=>!a.closest('nav,footer,header') && safeHref(a.getAttribute('href')) && a.getBoundingClientRect().width>0);
 const used=new Set();
 const data=links.map((a,i)=>{
   const img=a.querySelector('img');
   const title=(a.querySelector('h2,h3,h4,[class*=title]')?.textContent || img?.alt || a.textContent || '').replace(/\s+/g,' ').trim().slice(0,60);
   const href=safeHref(a.getAttribute('href'));
   if(!title || title.length>70 || used.has(href+title))return null;
   used.add(href+title);
   return {title,href,image:img?.currentSrc||img?.src||'',subtitle:(a.querySelector('p')?.textContent||'Explore in StudyBuddy').trim().slice(0,68)};
 }).filter(Boolean).filter(x=>x.image && (x.href.startsWith('/apps')||x.href.startsWith('/roles')||x.href.includes('learning')));
 if(data.length>=3) return data.slice(0,12);
 if(page==='dashboard')return [
  {title:'My Dashboard',subtitle:'Your personal overview',href:'/dashboard',image:asset('hero-dolphin-book.webp')},
  {title:'My Quest',subtitle:'Continue your goals',href:'/my-quest',image:asset('path-apps.webp')},
  {title:'Explore apps',subtitle:'Learn through play',href:'/apps',image:asset('path-teachers.webp')},
 ];
 if(['home','apps','learn','info','schools'].includes(page))return [];
 return [];
};
class SafeLibrary extends React.Component {
 constructor(props){super(props);this.state={broken:false};}
 static getDerivedStateFromError(){return {broken:true};}
 componentDidCatch(err){console.warn('[StudyBuddy W13] original component could not mount:',this.props.name,err.message);}
 render(){return this.state.broken?this.props.fallback || null:<React.Suspense fallback={this.props.fallback || null}>{this.props.children}</React.Suspense>;}
}
function StoryRail({items, page}){
 const [active,setActive]=useState(0); const count=items.length;
 const next = dir => setActive(i=>(i+dir+count)%count);
 if(!count)return null;
 return <section className="w13-rail" aria-label="Explore StudyBuddy stories">
   <div className="w13-topline"><span>YOUR STUDYBUDDY UNIVERSE</span><span>{String(active+1).padStart(2,'0')} / {String(count).padStart(2,'0')}</span></div>
   <div className="w13-railhead"><div><h2>{page==='roles'?'Find your place.':page==='dashboard'?'Your next move.':'Follow your curiosity.'}</h2>
   <p>Explore real learning journeys — choose the one that feels right.</p></div>
   <div className="w13-arrows"><button aria-label="Previous experience" onClick={()=>next(-1)}>←</button><button aria-label="Next experience" onClick={()=>next(1)}>→</button></div></div>
   <div className="w13-stage">
      <div className="w13-visual">
       {page==='dashboard' && C.ScrollStack ? <SafeLibrary name="ScrollStack" fallback={<GalleryCards items={items} active={active} setActive={setActive}/>}>
       <C.ScrollStack>{items.map((x,i)=><a key={i} href={x.href} className="w13-stack-card"><img src={x.image} alt=""/><span>{x.title}</span></a>)}</C.ScrollStack>
       </SafeLibrary> : (page==='apps' && C.HelixGallery || page==='roles' && (C.DepthRibbon || C.FanGallery) || C.FlexCarousel) ? <SafeLibrary name="Gallery" fallback={<GalleryCards items={items} active={active} setActive={setActive}/> }>
         {React.createElement((page==='apps' && C.HelixGallery || page==='roles' && (C.DepthRibbon || C.FanGallery) || C.FlexCarousel),{ items:items.map((x,i)=>({...x,id:String(i),src:x.image,alt:x.title,description:x.subtitle})), images:(page==='apps'&&C.HelixGallery || page==='roles'&&C.DepthRibbon)?items.map(x=>x.image):items.map(x=>({src:x.image,alt:x.title,title:x.title,href:x.href})), activeIndex:active, onChange:setActive, onItemClick:(i)=>{if(items[i])location.assign(items[i].href);}, onIndexChange:setActive })}
       </SafeLibrary> : <GalleryCards items={items} active={active} setActive={setActive}/>}
      </div>
      <div className="w13-detail" aria-live="polite"><span className="w13-index">EXPLORE / {String(active+1).padStart(2,'0')}</span>
         <h3>{items[active].title}</h3><p>{items[active].subtitle}</p><a href={items[active].href}>Explore this world <span aria-hidden="true">↗</span></a>
      </div>
   </div>
 </section>;
}
function GalleryCards({items,active,setActive}){
 return <div className="w13-fallback-gallery" role="list" aria-label="Learning destinations">
   {items.map((x,i)=><a role="listitem" key={`${x.href}-${i}`} href={x.href} onMouseEnter={()=>setActive(i)} onFocus={()=>setActive(i)} className={active===i?'is-active':''}>
    <img loading="lazy" src={x.image} alt=""/><span>{x.title}</span></a>)}
 </div>;
}
function QuickAccess({page}){
 const [open,setOpen]=useState(false);
 useEffect(()=>{const onKey=e=>{if((e.metaKey||e.ctrlKey)&&e.key.toLowerCase()==='k'){e.preventDefault();setOpen(x=>!x);}if(e.key==='Escape')setOpen(false);};addEventListener('keydown',onKey);return ()=>removeEventListener('keydown',onKey);},[]);
 const links=page==='dashboard'?[['My quest','/my-quest'],['Learning apps','/apps'],['My dashboard','/dashboard']]:[['Learning apps','/apps'],['Learning roles','/roles'],['For schools','/for-schools'],['My dashboard','/dashboard']];
 return <div className="w13-quick"><button aria-expanded={open} aria-controls="w13-quick-menu" onClick={()=>setOpen(o=>!o)} aria-label="StudyBuddy quick navigation">✦ <span>Explore</span><kbd>⌘K</kbd></button>
 {open&&<nav id="w13-quick-menu" aria-label="Quick navigation"><strong>Where to next?</strong>{links.map(([name,href])=><a key={href} href={href}>{name}<span>↗</span></a>)}</nav>}</div>;
}
function Backdrop({kind}){
 const Comp=C[kind]; if(!Comp || reduceMotion() || (isTouch() && ['Galaxy','Antigravity','SpiralFunnel','GlimmerWrap'].includes(kind)))return null;
 const config=kind==='Galaxy'?{density:0.55,glowIntensity:0.12,twinkleIntensity:0.22,hueShift:15}:
 kind==='Antigravity'?{count:90,particleSize:0.4,colors:['#8b5cf6','#69dcf5','#d5a8fa']}:
 kind==='SpiralFunnel'?{colors:['#8b5cf6','#ff9ffc'],backgroundColor:'transparent',quality:0.5,dust:280,strands:32,interactive:false}:
 {color:'#8b5cf6'};
 return <SafeLibrary name={kind}><Comp {...config}/></SafeLibrary>;
}
function Mount({page}){
 const items=useMemo(()=>pageItems(page),[page]);
 const bg=page==='home'?'Galaxy':page==='schools'?(C.SpiralFunnel?'SpiralFunnel':'GradientWaves'):page==='dashboard'?(C.GlimmerWrap?'GlimmerWrap':'MagicRings'):page==='roles'?'Antigravity':page==='apps'?'WebThreads':page==='learn'?(C.FadingStars?'FadingStars':'Galaxy'):null;
 const [railSlot,setRailSlot]=useState(null);
 const [bgSlot,setBgSlot]=useState(null);
 useEffect(()=>{if(['admin','auth','home'].includes(page))return;
   const main=document.querySelector('main#main-content,main,[role=main]');if(!main)return;
   const slot=document.createElement('div');slot.className='w13-rail-slot';slot.id='w13-real-gallery';
   const anchor=main.querySelector('.sb-catalog-v3__hero,.sb-roles-clean-hero,.role-dash-hero,.sb-school-hero');
   if(anchor)anchor.insertAdjacentElement('afterend',slot); else main.insertBefore(slot,main.firstChild);
   setRailSlot(slot);return ()=>{slot.remove();};
 },[page]);
 useEffect(()=>{if(!bg || !C[bg] || ['admin','auth'].includes(page))return;
    const target=page==='home'?document.querySelector('.galaxy-container'):document.querySelector('main > section,main#main-content,main');if(!target)return;
    const positioned=getComputedStyle(target).position==='static';if(positioned)target.style.position='relative';
    const slot=document.createElement('div');slot.className='w13-bg w13-bg-'+page;slot.setAttribute('aria-hidden','true');target.insertBefore(slot,target.firstChild);
    setBgSlot(slot);return ()=>{slot.remove();if(positioned)target.style.removeProperty('position');};
 },[page,bg]);
 useEffect(()=>{window.StudyBuddyW13={page,originalComponents:libraryNames,mounted:true,version:'13.0',hasOriginal:(name)=>!!C[name]};
   document.documentElement.dataset.w13Ready='true';},[page]);
 return <>
  {page!=='admin'&&page!=='auth'&&<QuickAccess page={page}/>}
  {items.length>0&&railSlot&& (page!=='home'||!!C.FlexCarousel) &&createPortal(<StoryRail page={page} items={items}/>,railSlot)}
  {bg&&C[bg]&&bgSlot&&createPortal(<Backdrop kind={bg}/>,bgSlot)}
  {C.CelebrationCursor && !reduceMotion() && !isTouch() && !['admin','auth'].includes(page) && <div className="w13-cursor"><SafeLibrary name="CelebrationCursor"><C.CelebrationCursor/></SafeLibrary></div>}
 </>;
}
function main(){
 if(document.querySelector('[data-w13-react-root]'))return;
 let root=document.createElement('div'); root.dataset.w13ReactRoot='';root.className='w13-root';document.body.append(root);
 const page=currentPage();
 try{createRoot(root).render(<Mount page={page}/>);}catch(err){console.error('[StudyBuddy W13] mount:',err);}
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',main,{once:true});else main();
