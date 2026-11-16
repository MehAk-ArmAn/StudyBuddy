import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import FlexCarousel from './components/FlexCarousel.jsx';
import HoloCard from './components/HoloCard.jsx';
import CircularCarousel from './components/CircularCarousel.jsx';
import './reactbits-islands.css';

// React-only islands: StudyBuddy's Laravel routing, CMS, filters, authentication,
// and original server-rendered cards remain in charge of the site.
function FlexWorlds({ items }) {
  const [index, setIndex] = useState(0);
  const item = items[index] || items[0];
  return <>
    <div className="sb8-flex-viewport">
      <FlexCarousel items={items} preset="liquid" intro="rise" fit="landscape"
        cardHeight={0.59} gap={18} radius={22} squeeze={0.2}
        followCursor={false} focusOnClick captions={false} captureWheel={false}
        autoplay={false} onChange={(next) => setIndex(next)} />
    </div>
    <div className="sb8-flex-caption" aria-live="polite">
      <div><span className="sb8-caption-overline">SELECTED ADVENTURE · {String(index + 1).padStart(2, '0')} / {String(items.length).padStart(2, '0')}</span>
        <strong>{item.title}</strong><small>{item.subtitle}</small></div>
      <a href={item.url} className="sb8-open-button" aria-label={`View details for ${item.title}`}>View details <span aria-hidden="true">↗</span></a>
    </div>
  </>;
}
function HoloSpotlight({ items }) {
  const item = items[0];
  if (!item) return null;
  return <div className="sb8-holo-frame">
    <HoloCard image={item.src} alt={item.alt} preset="cosmos" intensity={0.55}
      width={272} radius={24} tiltMax={10} hoverScale={1.035} idle={false} glare={0.35} edgeSparkle={0.55} />
    <a href={item.url} className="sb8-holo-visit">{item.title} <span aria-hidden="true">↗</span></a>
    <small>{item.subtitle}</small>
  </div>;
}
function OrbitCollection({ items }) {
  const [index, setIndex] = useState(0);
  const item = items[index] || items[0];
  return <>
    <div className="sb8-circular-viewport">
      <CircularCarousel items={items} preset="orbit" intro="rise" cardWidth={200}
        aspectRatio={0.94} gap={24} autoplay="off" speed={8} captions={false}
        draggable momentum={0.45} snap pauseOnHover focusOnClick parallax={0.16}
        stretch={0.18} depthFade={0.42} fadeColor="#11152d"
        cornerRadius={22} onChange={(next) => setIndex(next)} />
    </div>
    <div className="sb8-circle-caption" aria-live="polite">
      <div><span>IN FOCUS · {index + 1} / {items.length}</span><strong>{item.title}</strong><small>{item.subtitle}</small></div>
      <a className="sb8-open-button" href={item.url}>View activity ↗</a>
    </div>
  </>;
}
function parseItems(id) {
  const node = document.getElementById(id);
  if (!node) return [];
  try {
    const items = JSON.parse(node.textContent || '[]');
    return Array.isArray(items) ? items.filter(x => x && typeof x.src === 'string' && typeof x.url === 'string') : [];
  } catch (error) {
    console.warn('[StudyBuddy] React Bits data could not be parsed:', error);
    return [];
  }
}
function canWebGL2() {
  try {
    const c = document.createElement('canvas');
    const gl = c.getContext('webgl2', { alpha: true });
    if (!gl) return false;
    gl.getExtension('WEBGL_lose_context')?.loseContext();
    return true;
  } catch { return false; }
}
function mount() {
  const off = document.documentElement.classList.contains('sb-u-motion-off') || document.documentElement.dataset.sbEffects === 'off' ||
      document.documentElement.dataset.sbMotion === 'off' ||
      window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;
  const webgl = !off && canWebGL2();
  document.querySelectorAll('[data-sb8-component]').forEach(host => {
    const type = host.dataset.sb8Component;
    const items = parseItems(host.dataset.sb8Config);
    if (!items.length) return;
    // Preserve the original server-rendered grid if a shader cannot run.
    if ((type === 'flex' || type === 'holo') && !webgl) return;
    if (type === 'circular' && (off || items.length < 3)) return;
    try {
      const root = createRoot(host);
      if (type === 'flex' && items.length >= 3) {
        root.render(<FlexWorlds items={items} />);
        host.closest('[data-sb8-flex-shell]')?.setAttribute('data-ready', 'true');
        host.closest('.sb7-apps')?.classList.add('sb8-has-carousel');
      } else if (type === 'holo') {
        root.render(<HoloSpotlight items={items} />);
        host.closest('[data-sb8-holo-host]')?.setAttribute('data-ready', 'true');
      } else if (type === 'circular') {
        root.render(<OrbitCollection items={items} />);
        host.closest('[data-sb8-orbit-section]')?.setAttribute('data-ready', 'true');
      }
    } catch (error) {
      console.error('[StudyBuddy] React Bits mount failed:', error);
    }
  });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount, { once: true });
else mount();
