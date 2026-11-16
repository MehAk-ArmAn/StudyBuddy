import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
// ORIGINAL, user-supplied React Bits components from the working V8 source.
import FlexCarousel from './components/FlexCarousel.jsx';
import HoloCard from './components/HoloCard.jsx';
import CircularCarousel from './components/CircularCarousel.jsx';
// Free official components are fetched individually on the Mac. Missing ones never block V10.1.
import { officialBits } from './studybuddy_optional_components.jsx';
import './reactbits-islands.css';

function useReduced() {
  const [off, setOff] = useState(() => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches || false);
  useEffect(() => {
    const media = window.matchMedia?.('(prefers-reduced-motion: reduce)');
    const change = () => setOff(Boolean(media?.matches));
    media?.addEventListener?.('change', change);
    return () => media?.removeEventListener?.('change', change);
  }, []);
  return off;
}
function FlexWorlds({ items }) {
  const [index,setIndex] = useState(0);
  const item = items[index] || items[0];
  return <>
    <div className="sb10-flex-viewport"><FlexCarousel items={items} preset="liquid" intro="rise" cardHeight={0.5} gap={12} squeeze={0.2} focusOnClick captions={false} autoplay={false} captureWheel={false} onChange={setIndex}/></div>
    <div className="sb10-caption" aria-live="polite"><div><span>CHOOSE AN ADVENTURE · {index+1} / {items.length}</span><strong>{item.title}</strong><small>{item.subtitle}</small></div><a href={item.url} className="sb10-link">View activity ↗</a></div>
  </>;
}
function OrbitCollection({ items }) {
  const [index,setIndex]=useState(0); const item=items[index] || items[0];
  return <><div className="sb10-orbit-viewport"><CircularCarousel items={items} preset="orbit" intro="rise" cardWidth={200} aspectRatio={0.92} gap={22} autoplay="off" captions={false} draggable snap focusOnClick pauseOnHover parallax={0.12} stretch={0.1} depthFade={0.4} cornerRadius={20} onChange={setIndex}/></div><div className="sb10-caption" aria-live="polite"><div><span>ACTIVITY · {index+1} / {items.length}</span><strong>{item.title}</strong><small>{item.subtitle}</small></div><a className="sb10-link" href={item.url}>View details ↗</a></div></>;
}
function HoloSpotlight({items}) {
  const item=items[0]; if (!item) return null;
  return <div className="sb10-holo"><HoloCard image={item.src} alt={item.alt} preset="cosmos" intensity={0.55} width={260} radius={22} tiltMax={9} idle={false} hoverScale={1.025} edgeSparkle={0.55}/><a className="sb10-link" href={item.url}>{item.title} ↗</a></div>;
}
const worlds = [
  {name:'GradientWaves', title:'Silk Waves'},
  {name:'WebThreads', title:'Web Threads'},
  {name:'LiquidEther', title:'Liquid Ether'},
  {name:'Antigravity', title:'Antigravity'},
  {name:'MagicRings', title:'Magic Rings'}
];
function AtmosphereLab() {
  const options = worlds.filter(x => Boolean(officialBits[x.name]));
  const [chosen, setChosen] = useState(options[0]?.name || '');
  if (!options.length) return null;
  const Component = officialBits[chosen] || officialBits[options[0].name];
  const props = chosen === 'GradientWaves' ? {horizonColor:'#5227FF', waveColor:'#FF9FFC', crestColor:'#FFFFFF', speed:0.27, amplitude:1.5, relief:0.8, lightAngle:120, mouseInteraction:true, swellOnClick:true}
    : chosen === 'WebThreads' ? {colors:['#5227FF','#FF9FFC'], coreColor:'#FFFFFF', threadCount:6, spread:0.18}
    : chosen === 'LiquidEther' ? {colors:['#5227FF','#FF9FFC','#B497CF'], mouseForce:14, cursorSize:92, persistence:0.7, autoDemo:true, autoSpeed:0.35, autoIntensity:1.1}
    : chosen === 'Antigravity' ? {colors:['#5227FF','#FF9FFC','#B497CF'],count:window.innerWidth<760?320:750,magnetRadius:9,ringRadius:7,particleSize:0.75,autoAnimate:true}
    : {color:'#A855F7',colorTwo:'#6366F1',motion:'pulse',ringCount:6,speed:0.65,lineThickness:2,glow:0.75,hoverScale:1.1,clickBurst:true};
  return <div className="sb11-live-lab">
    <div className="sb11-effect-window" aria-hidden="true"><Component key={chosen} {...props}/></div>
    <div className="sb11-effect-options" role="group" aria-label="Change the learning atmosphere">{options.map(item=><button key={item.name} type="button" aria-pressed={chosen===item.name} onClick={()=>setChosen(item.name)}>{item.title}</button>)}</div>
  </div>;
}
function CrumpleCard({ src }) {
  const Comp = officialBits.PaperCrumple;
  const reduced = useReduced();
  if(!Comp) return null;
  return <Comp src={src} alt="Fictional Number Odyssey learning artwork you can crumple" width={265} height={335} sceneHeight={390} releaseBehavior="restore" crumpleAmount={0.85} crumpleDuration={0.55} releaseDuration={0.4} foldCount={6} foldSharpness={0.6} wrinkleDepth={0.65} creaseStrength={0.18} paperColor="#f4f0e8" paperTexture={0.08} draggable={!reduced} returnToOrigin disabled={reduced}/>;
}
class SafeVisual extends React.Component {
  state={failed:false};
  static getDerivedStateFromError(){return {failed:true};}
  componentDidCatch(error){this.props.onFailure?.();console.warn('[StudyBuddy] Optional visual failed; original cards remain.',error);}
  render(){return this.state.failed ? null : this.props.children;}
}
function data(id){
  const node=document.getElementById(id); if(!node)return [];
  try { const arr=JSON.parse(node.textContent||'[]'); return Array.isArray(arr) ? arr.filter(x => x && typeof x.title==='string') : []; }
  catch(e) { console.warn('[StudyBuddy V10] Invalid CMS JSON',id,e); return []; }
}
function canWebGL() { try {const c=document.createElement('canvas');const gl=c.getContext('webgl2'); return Boolean(gl);}catch{return false;} }
function motionOff(){return document.documentElement.classList.contains('sb-u-motion-off') || document.documentElement.dataset.sbEffects==='off' || document.documentElement.dataset.sbMotion==='off' || Boolean(window.matchMedia?.('(prefers-reduced-motion: reduce)').matches);}
function readyWhen(host, selector, cb, timeout=6000){
  const start=Date.now();let done=false;
  const check=()=>{
    if(!host.isConnected || done)return;
    const node=host.querySelector(selector);
    // A present-but-zero-size WebGL canvas isn't a working visible gallery.
    const measurable=node && ((node.tagName==='CANVAS' ? node.width>0 && node.height>0 && node.getBoundingClientRect().width>40 : true));
    if(measurable){ done=true; cb(); return; }
    if(Date.now()-start<timeout) window.setTimeout(check,160);
    else {host.dataset.sb10Error='not-ready';console.warn('[StudyBuddy V10] Animation not ready; server-rendered cards remain visible.');}
  }; window.setTimeout(check,200);
}
const mountedRoots = new Map();
const shellSelector='[data-sb8-flex-shell], [data-sb8-holo-host], [data-sb8-orbit-section], [data-sb10-gallery-shell], [data-sb10-bento-shell], [data-sb11-experiments-shell], [data-sb11-paper-shell]';
function mount(){
  const reduce=motionOff(); const webgl=!reduce && canWebGL();
  document.querySelectorAll('[data-sb8-component], [data-sb10-component]').forEach(host=>{
    // Component scripts are only included on targeted public pages.
    if(host.dataset.sb10Initialized) return;
    const type=host.dataset.sb10Component || host.dataset.sb8Component;
    const items=data(host.dataset.sb10Config || host.dataset.sb8Config);
    const optional=['paper','atmosphere'].includes(type);
    if(!items.length && !optional)return;
    if(type==='paper' && !officialBits.PaperCrumple) return;
    if(type==='atmosphere' && !worlds.some(x=>Boolean(officialBits[x.name])))return;
    const is3d=['flex','holo','circular','roles','schools','paper','atmosphere'].includes(type);
    if(is3d && !webgl)return; // accessible server content remains visible
    if(['flex','circular','roles','schools'].includes(type) && items.length<3)return;
    // Defer additional GPU effects until the viewer is near the Study Lab.
    if(optional && !host.dataset.sb10LazyLoaded && !host.dataset.sb10Watching && 'IntersectionObserver' in window){
      host.dataset.sb10Watching='1';
      const observer=new IntersectionObserver(entries=>{
        if(entries.some(entry=>entry.isIntersecting)){
          observer.disconnect();
          delete host.dataset.sb10Watching;
          host.dataset.sb10LazyLoaded='1';
          mount();
        }
      },{rootMargin:'240px'});
      observer.observe(host);
      return;
    }
    if(optional && host.dataset.sb10Watching)return;
    host.dataset.sb10Initialized='1';
    host.inert=true; // hidden, not-yet-ready React content must not trap keyboard focus
    try {
      const root=createRoot(host);
      mountedRoots.set(host,root);
      const mark=()=>{
        host.inert=motionOff();
        host.dataset.sb10Live='true';
        const shell=host.closest(shellSelector);
        if(shell){shell.dataset.sb10Live='true';shell.dataset.ready='true';}
      };
      if(type==='flex' || type==='roles' || type==='schools') {
        root.render(<FlexWorlds items={items}/>);
        readyWhen(host, '.flex-carousel canvas',mark);
      } else if(type==='holo') {
        root.render(<HoloSpotlight items={items}/>);
        readyWhen(host, '.holo-card[data-ready]',mark);
      } else if(type==='circular') {
        root.render(<OrbitCollection items={items}/>);
        readyWhen(host, '.circular-carousel[data-ready]',mark);
      } else if(type==='paper') {
        root.render(<SafeVisual onFailure={()=>{ const shell=host.closest(shellSelector);if(shell)delete shell.dataset.sb10Live;}}><CrumpleCard src={host.dataset.sb11Image || '/assets/studybuddy-demo/number-odyssey.svg'}/></SafeVisual>);
        readyWhen(host, 'canvas',mark,8000);
      } else if(type==='atmosphere') {
        root.render(<SafeVisual onFailure={()=>{const shell=host.closest(shellSelector);if(shell)delete shell.dataset.sb10Live;}}><AtmosphereLab/></SafeVisual>);
        readyWhen(host, 'canvas',mark,8000);
      }
    } catch(e){ host.inert=true;host.dataset.sb10Error='1'; delete host.dataset.sb10Initialized; console.error('[StudyBuddy V10] Component failed; original content retained.',e);}
  });
}
// Stop real GPU animations when Effects Off / Reduced Motion is enabled. Restore Blade fallbacks.
function syncMotion() {
  if(motionOff()) {
    for(const [host,root] of mountedRoots) {
      root.unmount();
      host.inert=true;
      delete host.dataset.sb10Initialized;
      delete host.dataset.sb10Live;
      const shell=host.closest(shellSelector);
      if(shell){delete shell.dataset.sb10Live; delete shell.dataset.ready;}
    }
    mountedRoots.clear();
  } else mount();
}
const effectsWatcher=new MutationObserver(syncMotion);
effectsWatcher.observe(document.documentElement,{attributes:true,attributeFilter:['class','data-sb-effects','data-sb-motion']});
window.matchMedia?.('(prefers-reduced-motion: reduce)').addEventListener?.('change',syncMotion);
function showDiagnostics(){
  if(!new URLSearchParams(location.search).has('sb10-debug'))return;
  window.setTimeout(()=>{
    const hosts=[...document.querySelectorAll('[data-sb8-component], [data-sb10-component]')];
    const panel=document.createElement('pre');
    panel.setAttribute('role','status');panel.style.cssText='position:fixed;bottom:14px;left:14px;z-index:2147483646;max-width:min(460px,90vw);max-height:50vh;overflow:auto;padding:14px 16px;border-radius:16px;background:#121a36;color:#fff;font:12px/1.6 monospace;box-shadow:0 10px 40px #0007;white-space:pre-wrap;';
    panel.textContent='StudyBuddy V10 diagnostics\nWebGL2: '+canWebGL()+' | Effects off: '+motionOff()+'\n'+hosts.map(h=>[h.dataset.sb10Component||h.dataset.sb8Component,'loaded='+Boolean(h.dataset.sb10Initialized),'visible='+Boolean(h.dataset.sb10Live),'items='+data(h.dataset.sb10Config||h.dataset.sb8Config).length].join(' ')).join('\n');
    document.body.appendChild(panel);
  },2800);
}
if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',()=>{mount();showDiagnostics();},{once:true}); else {mount();showDiagnostics();}
