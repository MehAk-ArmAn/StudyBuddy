// W29: real user-provided React Bits Galaxy component, mounted ONCE on saved home.
// Keeps W28 planet/orb layer and Laravel pages, without a second React root in them.
import React, {useEffect} from 'react';
import {createRoot} from 'react-dom/client';
import Galaxy from './Galaxy.jsx';
import './w29.css';

const GALAXY_PROPS = Object.freeze({
  density:1,
  glowIntensity:0.3,
  flareIntensity:1,
  starSpeed:0.5,
  mouseRepulsion:true,
  clickWarp:true
});
const q = (sel, root=document) => root.querySelector(sel);

function LiveLandingGalaxy(){
  useEffect(()=>{
    document.documentElement.classList.add('sb29-galaxy-ready');
    return ()=>document.documentElement.classList.remove('sb29-galaxy-ready');
  }, []);
  return <Galaxy {...GALAXY_PROPS} className="sb29-galaxy" style={{width:'100%',height:'100%'}}/>;
}

function boot(){
  if(location.pathname !== '/') return;
  const original = [...document.querySelectorAll('.galaxy-container')].find(node =>
    !node.closest('.w13-bg,#sb19-galaxy-host,#sb29-galaxy-host') && node.closest('main section'));
  const hero=original?.closest('main section');
  if(!original || !hero || !q('h1', hero)){
    console.warn('[StudyBuddy W29] Saved original landing not recognized. Nothing changed.');
    return;
  }
  if(q('#sb29-galaxy-host',original)) return;
  const host = document.createElement('div');
  host.id = 'sb29-galaxy-host';
  host.setAttribute('aria-hidden', 'true');
  host.dataset.studybuddyW29='landing';
  original.appendChild(host);
  // Galaxy.jsx listens to window-level pointermove/pointerdown itself,
  // so cursor repulsion and click warp work through the background.
  // DO NOT forward synthetic events; that would double-trigger the warp.
  // Don't replace the original homepage or React components.
  const root = createRoot(host);
  root.render(<LiveLandingGalaxy/>);
  window.StudyBuddyGalaxy29 = {
    diagnostics:()=>({
      version:'W29',path:location.pathname,
      props:GALAXY_PROPS,
      mounts:document.querySelectorAll('#sb29-galaxy-host').length,
      actualGalaxyCanvas:document.querySelectorAll('#sb29-galaxy-host canvas').length,
      heroCount:document.querySelectorAll('main section.sb28-hero').length,
      landingReady:document.documentElement.classList.contains('sb29-galaxy-ready'),
      otherLayersUnchanged:!!document.querySelector('.sb28-cosmos'),
      brandLogo:document.querySelector('header a[href="/"] img')?.getAttribute('src') || null
    })
  };
  console.info('[StudyBuddy W29] Genuine Galaxy mounted on landing with exact user props.');
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
