/* StudyBuddy W22: an isolated, genuine React Bits island inside Laravel's existing page.
 * Preserves W19 Galaxy, W20 fix, W21 scroll zoom, base44 art, all Laravel routes. */
import React from 'react';
import { createRoot } from 'react-dom/client';
import Antigravity from './Antigravity.jsx';
import './mount.css';

const SPEC = Object.freeze({
  colors: ['#5227FF', '#FF9FFC', '#B497CF'],
  count: 1500,
  magnetRadius: 9,
  ringRadius: 7,
  particleSize: 0.9,
  autoAnimate: true,
});

let mounted = false;
let root = null;
const report = (extra = {}) => ({
  version: 'W22',
  component: 'Official React Bits Antigravity (JS + CSS)',
  mounted,
  canvasVisible: !!document.querySelector('#sb22-antigravity-host canvas'),
  galaxyPreserved: !!document.querySelector('#sb19-galaxy-host canvas'),
  scrollZoomPresent: !!window.StudyBuddyJourney21,
  exactProps: SPEC,
  ...extra,
});

class AntigravityBoundary extends React.Component {
  constructor(props) { super(props); this.state = { error: false }; }
  static getDerivedStateFromError() { return { error: true }; }
  componentDidCatch(e) { console.error('[StudyBuddy W22] Antigravity renderer failed', e); }
  render() { return this.state.error ? null : this.props.children; }
}

function mount() {
  if (mounted) return;
  const hero = document.querySelector('.t-page > .sb21-journey-track > .sb21-hero')
    || document.querySelector('.t-page > section');
  if (!hero) return;
  const grid = hero.querySelector('.sb21-hero-grid')
    || hero.querySelector('div.mx-auto.grid.max-w-7xl');
  const mascot = grid?.children?.[1];
  if (!mascot || !mascot.querySelector('img')) {
    console.warn('[StudyBuddy W22] Existing mascot anchor missing; preserving original page.');
    return;
  }
  let island = document.getElementById('sb22-antigravity-host');
  if (!island) {
    island = document.createElement('div');
    island.id = 'sb22-antigravity-host';
    island.className = 'sb22-antigravity-host';
    island.setAttribute('aria-hidden', 'true');
    mascot.classList.add('sb22-mascot-host');
    mascot.prepend(island);
  }
  root = createRoot(island);
  mounted = true;
  root.render(
    <AntigravityBoundary>
      <Antigravity {...SPEC} />
    </AntigravityBoundary>
  );
  document.documentElement.dataset.sb22Antigravity = 'mounted';
  document.dispatchEvent(new CustomEvent('studybuddy:antigravity-ready', { detail: { version: 'W22' } }));
  console.info('[StudyBuddy W22] Genuine React Bits Antigravity mounted behind existing mascot.');
}

window.StudyBuddyAntigravity22 = {
  diagnostics: () => report(),
  refresh: mount,
};

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount, { once: true });
else mount();
