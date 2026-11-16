/* StudyBuddy Laravel React bridge — one reusable entry for all Blade layouts.
   This does NOT take over routes, authentication or server-rendered pages. */
import React, { Component, createElement, useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import SloshGauge from './components/SloshGauge.jsx';
import './studybuddy-react.css';

/* SB_FREE_BITS_IMPORTS_START */
import { SBHeroMotion, SBGradientNote, SBHeroShapeGrid, SBCounter, SBArtworkWall } from './components/StudyBuddyFree/StudyBuddyWidgets.jsx';
/* SB_FREE_BITS_IMPORTS_END */

const REGISTRY = new Map();
const ROOTS = new WeakMap();
const TRACKED = new Set();
const VERSION = '2026.11.15';

function CuriosityMeter({ initialLevel = 60, title = 'Curiosity Meter' }) {
  const [level, setLevel] = useState(Number.isFinite(+initialLevel) ? Math.min(100, Math.max(0, +initialLevel)) : 60);
  return (
    <div className="sbreact-meter" aria-label="Interactive curiosity demonstration">
      <div className="sbreact-meter__status"><span className="sbreact-meter__signal" /> REACT BITS IS LIVE</div>
      <div className="sbreact-meter__body">
        <SloshGauge
          value={level}
          onChange={setLevel}
          interactive
          showValue
          liquidColor="#8AF4DD"
          glassColor="#20203F"
          width={94}
          height={186}
          radius={20}
          ticks={4}
          viscosity={0.15}
          tilt={0.45}
          splash={0.42}
          unit="%"
          ariaLabel="Curiosity level"
        />
        <div className="sbreact-meter__details">
          <strong>{title}</strong>
          <p>Drag the liquid or use the keyboard arrows. This is a working React Bits component, not a static mockup.</p>
          <div className="sbreact-meter__presets" aria-label="Curiosity presets">
            {[['Chill', 25], ['Explore', 60], ['Super curious', 100]].map(([label, amount]) => (
              <button type="button" key={label} onClick={() => setLevel(amount)} aria-pressed={level === amount}>{label}</button>
            ))}
          </div>
          <small>Interactive demo only · Not saved to student progress</small>
        </div>
      </div>
    </div>
  );
}

REGISTRY.set('CuriosityMeter', CuriosityMeter);
REGISTRY.set('SloshGauge', SloshGauge);
/* SB_FREE_BITS_REGISTRY_START */
REGISTRY.set('SBHeroMotion', SBHeroMotion);
REGISTRY.set('SBGradientNote', SBGradientNote);
REGISTRY.set('SBHeroShapeGrid', SBHeroShapeGrid);
REGISTRY.set('SBCounter', SBCounter);
REGISTRY.set('SBArtworkWall', SBArtworkWall);
/* SB_FREE_BITS_REGISTRY_END */


class WidgetErrorBoundary extends Component {
  constructor(props) { super(props); this.state = { failed: false }; }
  static getDerivedStateFromError() { return { failed: true }; }
  componentDidCatch(error) { console.error('[StudyBuddy React] component render failed', error); }
  render() {
    if (this.state.failed) return (
      <div className="sbreact-error" role="status">This interactive element is temporarily unavailable. The rest of the page still works.</div>
    );
    return this.props.children;
  }
}

function Ready({ node, children }) {
  useEffect(() => {
    node.dataset.sbReactActive = 'mounted';
    document.dispatchEvent(new CustomEvent('studybuddy:react-mounted', { detail: { component: node.dataset.sbReact } }));
    if (node.id === 'sb-react-native-curiosity') {
      const stage = node.closest('.sb-live-section__stage');
      if (stage) {
        for (const child of stage.children) {
          if (child === node || child.classList.contains('sb-live-section__stamp')) continue;
          // Hide only old SloshGauge proof placeholders, after a real React mount succeeds.
          if (child.id?.startsWith('sb-live-curiosity') || child.classList.contains('sb-one-live-fallback') || child.textContent?.includes('React component not mounted')) child.hidden = true;
        }
      }
    }
    return () => { node.dataset.sbReactActive = 'unmounted'; };
  }, [node]);
  return children;
}

function propsFor(node) {
  try { const value = JSON.parse(node.getAttribute('data-sb-react-props') || '{}'); return value && typeof value === 'object' && !Array.isArray(value) ? value : {}; }
  catch (error) { console.warn('[StudyBuddy React] Invalid data-sb-react-props', error); return {}; }
}

function mount(node) {
  if (!(node instanceof Element) || ROOTS.has(node)) return false;
  const componentName = node.getAttribute('data-sb-react');
  const Widget = REGISTRY.get(componentName);
  if (!Widget) { console.warn('[StudyBuddy React] Unregistered component', componentName); return false; }
  try {
    node.dataset.sbReactActive = 'loading';
    const root = createRoot(node);
    ROOTS.set(node, root);
    TRACKED.add(node);
    root.render(createElement(WidgetErrorBoundary, null,
      createElement(Ready, { node }, createElement(Widget, propsFor(node)))
    ));
    return true;
  } catch (error) {
    node.dataset.sbReactActive = 'error';
    console.error('[StudyBuddy React] mount failed', error);
    return false;
  }
}

function placeHomepageDemo() {
  const section = document.getElementById('sb-one-live-section');
  const stage = section?.querySelector('.sb-live-section__stage');
  if (!stage) return;
  if (document.getElementById('sb-react-native-curiosity')) return;
  const host = document.createElement('div');
  host.id = 'sb-react-native-curiosity';
  host.dataset.sbReact = 'CuriosityMeter';
  host.dataset.sbReactProps = '{"initialLevel":60}';
  host.className = 'sbreact-hero-slot';
  stage.appendChild(host);
}

function scan() {
  placeHomepageDemo();
  document.querySelectorAll('[data-sb-react]').forEach(mount);
  window.StudyBuddyReactBridge.status = 'loaded';
  window.StudyBuddyReactBridge.mounted = [...TRACKED].filter(node => node.dataset.sbReactActive === 'mounted').length;
}

window.StudyBuddyReactBridge = {
  version: VERSION,
  status: 'booting',
  mounted: 0,
  available: () => [...REGISTRY.keys()],
  register(name, component) {
    if (typeof name !== 'string' || !name.trim() || !component) throw new Error('register requires a name and React component');
    REGISTRY.set(name, component);
    scan();
  },
  mount,
  scan
};

let queued = false;
function queueScan() {
  if (queued) return;
  queued = true;
  queueMicrotask(() => { queued = false; scan(); });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', queueScan, { once: true });
else queueScan();
new MutationObserver(mutations => {
  if (mutations.some(item => [...item.addedNodes].some(node => node.nodeType === 1 && (node.matches?.('[data-sb-react],#sb-one-live-section') || node.querySelector?.('[data-sb-react]'))))) queueScan();
}).observe(document.documentElement, { childList: true, subtree: true });
console.info('[StudyBuddy React] Laravel + Vite bridge loaded', VERSION);
