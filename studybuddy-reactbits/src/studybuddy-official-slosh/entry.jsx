import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import SloshGauge from './SloshGauge.jsx';
import './interface.css';

const TARGET = 'sb-rb-slosh-official';
const PREFIX = '[StudyBuddy Official SloshGauge]';

function report(status, extra = {}) {
  window.StudyBuddyOfficialSlosh = { status, ...extra };
  console.info(PREFIX, status, extra);
}

class GaugeBoundary extends React.Component {
  constructor(props) { super(props); this.state = { error: null }; }
  static getDerivedStateFromError(error) { return { error }; }
  componentDidCatch(error, info) {
    report('render-error', { message: String(error), stack: info?.componentStack });
    console.error(PREFIX, error);
  }
  render() {
    if (this.state.error) return <p role="alert" className="sb-rb-error">The gauge loaded but could not render: {String(this.state.error)}</p>;
    return this.props.children;
  }
}

function GaugeDemo() {
  const [level, setLevel] = useState(60);
  useEffect(() => {
    const node = document.getElementById(TARGET);
    if (node) node.dataset.reactMounted = 'true';
    report('mounted', { version: 'registry-js-css', value: 60 });
    return () => { if (node) delete node.dataset.reactMounted; };
  }, []);

  return <div className="sb-rb-live">
    <SloshGauge
      value={level}
      onChange={setLevel}
      interactive
      showValue
      liquidColor="#f5f5f5"
      glassColor="#27272a"
      width={88}
      height={180}
      radius={20}
      ticks={4}
      viscosity={0.15}
      tilt={0.45}
      splash={0.42}
      unit="%"
      ariaLabel="Curiosity level"
    />
    <div className="sb-rb-live__copy">
      <span className="sb-rb-live__status" role="status">● REACT BITS IS LIVE</span>
      <strong>Curiosity level: <output>{level}%</output></strong>
      <p>Drag the liquid or use your arrow keys. This is a playground preview; your real progress stays unchanged.</p>
      <div className="sb-rb-live__controls" role="group" aria-label="Curiosity presets">
        <button type="button" onClick={() => setLevel(25)}>Chill</button>
        <button type="button" onClick={() => setLevel(60)}>Explore</button>
        <button type="button" onClick={() => setLevel(95)}>Super curious</button>
      </div>
    </div>
  </div>;
}

function boot() {
  const node = document.getElementById(TARGET);
  if (!node) { report('target-missing', { target: TARGET }); return; }
  if (node.dataset.reactBooted === 'yes') return;
  try {
    node.dataset.reactBooted = 'yes';
    report('rendering');
    createRoot(node).render(<GaugeBoundary><GaugeDemo /></GaugeBoundary>);
  } catch (error) {
    node.dataset.reactBooted = 'no';
    report('mount-error', { error: String(error) });
    console.error(PREFIX, error);
  }
}

report('bundle-executed');
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot, { once: true });
else boot();
