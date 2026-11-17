import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import GooeyNav from '../source/GooeyNav.jsx';
import './mount.css';

// W24 only enhances real navigation. It does not own routes, permissions, or login.
const pathOnly = (href) => {
  try { const url = new URL(href, location.origin); return url.origin === location.origin ? (url.pathname.replace(/\/$/, '') || '/') : null; }
  catch { return null; }
};
const currentIndex = items => {
  const pathname = location.pathname.replace(/\/$/, '') || '/';
  const hash = location.hash;
  const hashMatch = items.findIndex(item => {
    try {const u = new URL(item.href, location.origin); return u.pathname === pathname && !!u.hash && u.hash === hash;} catch {return false;}
  });
  if (hashMatch >= 0) return hashMatch;
  const exact = items.findIndex(item => {
    try {const u = new URL(item.href,location.origin);return u.pathname === pathname && !u.hash;} catch{return false;}
  });
  if(exact >= 0) return exact;
  return items.findIndex(item => {
    const path=pathOnly(item.href);
    return path && path!=='/' && pathname.startsWith(path+'/');
  });
};
const themeNow = () => {
  const html=document.documentElement; const body=document.body;
  if(html.classList.contains('dark') || html.getAttribute('data-theme')==='dark' || body?.getAttribute('data-theme')==='dark') return 'dark';
  if(html.classList.contains('light') || html.getAttribute('data-theme')==='light' || body?.getAttribute('data-theme')==='light') return 'light';
  return matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';
};
const isLocal = link => {
  if (!link?.href || link.getAttribute('href')?.trim()==='#') return false;
  const u = new URL(link.href,location.origin);
  return u.origin === location.origin && /^(https?:)$/.test(u.protocol) && !link.hasAttribute('download') && !link.hasAttribute('target');
};

function StudyBuddyGooey({ items, initial, onMounted, locationKind }) {
  const [active,setActive]=useState(Math.max(0,initial));
  const [theme,setTheme]=useState(themeNow);
  useEffect(() => {
    const obs=new MutationObserver(() => setTheme(themeNow()));
    obs.observe(document.documentElement,{attributes:true,attributeFilter:['class','data-theme']});
    obs.observe(document.body,{attributes:true,attributeFilter:['class','data-theme']});
    const onHash=()=>{const idx=currentIndex(items);if(idx>=0)setActive(idx)};
    addEventListener('hashchange',onHash);
    onMounted();
    return () => {obs.disconnect(); removeEventListener('hashchange',onHash)};
  },[items,onMounted]);
  return <GooeyNav
    items={items}
    initialActiveIndex={Math.max(0,initial)}
    activeIndex={active}
    onChange={(index) => setActive(index)}
    theme={theme}
    size="md"
    frame={true}
    color="#7166e8"
    activeTextColor="#ffffff"
    colors={['#7166E8','#927CF2','#75B5F7','#A8B8FF']}
    particleCount={20}
    spread={90}
    animationTime={1500}
    timeVariance={260}
    gooeyness={0.6}
    wobble={0.42}
    hoverEffect={true}
    className={`sb24-gooey sb24-gooey--${locationKind}`}
  />;
}

function enhance(original, type) {
  if (!original || original.dataset.sb24Enhanced) return false;
  const candidates=type==='snapshot'
    ? [...original.children].filter(node=>node.matches?.('a[href]'))
    // Laravel's CMS-driven "More" dropdown may contain legitimate role-filtered links.
    // Preserve those routes in GooeyNav instead of silently dropping them.
    : [...original.querySelectorAll(':scope > a[href], :scope > .sb-nav-more a[href]')];
  const items=candidates.filter(isLocal).map(a=>({label:a.textContent.trim(),href:a.getAttribute('href')})).filter(x=>x.label);
  // Make Home an accurate active selection instead of highlighting Apps on the homepage.
  if(type==='snapshot' && !items.some(x=>pathOnly(x.href)==='/' && !x.href.includes('#'))) items.unshift({label:'Home',href:'/'});
  if(items.length<2 || items.length>7) {console.warn('[StudyBuddy W24] Nav shape changed; keeping original navigation.');return false;}
  const host=document.createElement('div');
  host.className='sb24-nav-host';
  host.setAttribute('data-sb24-kind',type);
  host.setAttribute('aria-label','StudyBuddy animated navigation');
  original.insertAdjacentElement('afterend',host);
  const initial=currentIndex(items);
  try {
    const root=createRoot(host);
    root.render(<StudyBuddyGooey items={items} initial={initial} locationKind={type} onMounted={() => {
      if (!host.isConnected) return;
      host.dataset.sb24Ready='true';
      original.dataset.sb24Enhanced='true';
      original.classList.add('sb24-nav-replaced');
      // Browser / no-JS fallback remains the actual original Laravel/HTML nav.
    }}/>)
  } catch(err) {
    console.error('[StudyBuddy W24] Original links kept because React mount failed',err);
    host.remove(); return false;
  }
  return true;
}

function boot(){
  if(location.pathname.startsWith('/admin')) return;
  // The current W25 header owns navigation. W24 only enhances a legacy header.
  if (document.querySelector('[data-sb25-react-host]')) return;
  const base44=document.querySelector('header nav[aria-label="Main"]');
  const laravel=document.querySelector('header .sb-nav-links');
  if(base44) enhance(base44,'snapshot');
  else if(laravel) enhance(laravel,'laravel');
  window.StudyBuddyGooey24 = {
    diagnostics: () => ({ version:'W24', source:'exact user-supplied GooeyNav JSX + CSS',
      hosts:document.querySelectorAll('.sb24-nav-host').length,
      mounted:document.querySelectorAll('.sb24-nav-host[data-sb24-ready="true"]').length,
      canvases:document.querySelectorAll('.sb24-nav-host canvas').length,
      originalsHidden:document.querySelectorAll('.sb24-nav-replaced').length,
      galaxyCanvas:!!document.querySelector('#sb19-galaxy-host canvas'),
      antigravityUntouched:true,
      route:location.pathname,
      theme:themeNow()})
  };
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
