/* StudyBuddy W8: real React Bits components, lazy render and theme-aware scenes.
   Laravel retains routes, data, auth, and all server-rendered fallbacks. */
import React, { lazy, Suspense, useEffect, useRef, useState } from 'react';

const Antigravity = lazy(() => import('./Antigravity.jsx'));
const Galaxy = lazy(() => import('./Galaxy.jsx'));
const RoleGallery = lazy(() => import('./W8RoleGallery.jsx'));
const FeatureBounce = lazy(() => import('./W8FeatureBounce.jsx'));
const FlexCarousel = lazy(() => import('./FlexCarousel.jsx'));
const CircularGallery = lazy(() => import('./CircularGallery.jsx'));
const DepthCarousel = lazy(() => import('./DepthCarousel.jsx'));
const OptionWheel = lazy(() => import('./OptionWheel.jsx'));

const COLORS = ['#a989ff', '#b49aff', '#d9b7ff', '#87d9ee'];

function currentLight() {
  const html = document.documentElement;
  const body = document.body;
  const flags = [html.dataset.sbColorTheme, html.dataset.theme, html.getAttribute('data-bs-theme'), body?.dataset.theme]
    .filter(Boolean).map(value => value.toLowerCase());
  if (flags.includes('light')) return true;
  if (flags.includes('dark')) return false;
  if (html.classList.contains('light') || body?.classList.contains('light')) return true;
  if (html.classList.contains('dark') || body?.classList.contains('dark')) return false;
  return window.matchMedia('(prefers-color-scheme: light)').matches;
}
function useLightTheme() {
  const [light, setLight] = useState(() => currentLight());
  useEffect(() => {
    const update = () => setLight(currentLight());
    const observer = new MutationObserver(update);
    observer.observe(document.documentElement, {attributes:true, attributeFilter:['class','data-sb-color-theme','data-theme','data-bs-theme']});
    if (document.body) observer.observe(document.body, {attributes:true, attributeFilter:['class','data-theme']});
    const media = window.matchMedia('(prefers-color-scheme: light)');
    media.addEventListener?.('change', update);
    return () => {observer.disconnect();media.removeEventListener?.('change',update);};
  }, []);
  return light;
}
function useAllowGPU(ref) {
  const [allowed, setAllowed] = useState(false);
  useEffect(() => {
    const el = ref.current;
    if (!el || !('IntersectionObserver' in window)) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || navigator.connection?.saveData) return;
    let canvas;
    try {canvas=document.createElement('canvas');if (!canvas.getContext('webgl2')) return;} catch {return;}
    const io=new IntersectionObserver(entries=>setAllowed(entries.some(e=>e.isIntersecting)),{rootMargin:'160px'});
    io.observe(el);
    return () => io.disconnect();
  },[ref]);
  return allowed;
}
function Scene({ variant }) {
  const ref=useRef(null);
  const enabled=useAllowGPU(ref);
  const light=useLightTheme();
  const isHero=variant==='antigravity';
  return <div ref={ref} className={'sbw8-fx sbw8-fx--'+variant} aria-hidden="true" data-sbw8-effect={variant}>
    {enabled ? <Suspense fallback={null}>{isHero ? <Antigravity
      colors={COLORS} colorMode="radial" count={900} particleShape="sphere"
      particleSize={1.0} magnetRadius={9} ringRadius={6.5} ambient={0.42}
      waveAmplitude={0.65} waveSpeed={0.24} opacity={light ? 0.43 : 0.76}
      lightMode={light} autoAnimate interactive={false} clickBurst={false} maxDevicePixelRatio={1.5}
    /> : <Galaxy
      starColors={COLORS} density={0.73} layers={3} glowIntensity={0.24}
      flareIntensity={0.42} twinkleIntensity={0.22} starSpeed={0.2} speed={0.35}
      rotationSpeed={0.025} mouseInteraction={false} mouseRepulsion={false}
      clickWarp={false} opacity={light ? 0.43 : 0.54} lightMode={light} transparent
      dpr={1.4}
    />}</Suspense> : null}
  </div>;
}

export function SBW8Antigravity(){return <Scene variant="antigravity"/>;}
export function SBW8Galaxy(){return <Scene variant="galaxy"/>;}
export function SBW8RoleGallery(props){return <Suspense fallback={null}><RoleGallery {...props}/></Suspense>;}
export function SBW8FeatureBounce(props){return <Suspense fallback={null}><FeatureBounce {...props}/></Suspense>;}

// Components available throughout the existing Laravel/Blade bridge, but not all auto-mounted:
// avoid running multiple simultaneous WebGL galleries in one viewport.
export function SBW8FlexCarousel(props){return <Suspense fallback={null}><FlexCarousel {...props}/></Suspense>;}
export function SBW8CircularGallery(props){return <Suspense fallback={null}><CircularGallery {...props}/></Suspense>;}
export function SBW8DepthCarousel(props){return <Suspense fallback={null}><DepthCarousel {...props}/></Suspense>;}
export function SBW8OptionWheel(props){return <Suspense fallback={null}><OptionWheel {...props}/></Suspense>;}
