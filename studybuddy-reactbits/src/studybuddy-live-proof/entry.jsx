import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import SloshGauge from './SloshGauge.jsx';
import './proof.css';

const TARGET = 'sb-react-bits-meter-v11';
const PREFIX = '[StudyBuddy React Bits LIVE]';

class MeterErrorBoundary extends React.Component {
  constructor(props) { super(props); this.state = { error: null }; }
  static getDerivedStateFromError(error) { return { error }; }
  componentDidCatch(error) {
    console.error(PREFIX, 'Component failed during rendering:', error);
  }
  render() {
    if (this.state.error) {
      return <div className="sb-real-react__error" role="alert">React loaded, but the gauge failed. Open Console for the precise error.</div>;
    }
    return this.props.children;
  }
}

function RealCuriosityMeter() {
  const [value, setValue] = useState(60);
  useEffect(() => {
    const target = document.getElementById(TARGET);
    if (target) target.dataset.reactMounted = 'true';
    window.StudyBuddyReactBitsLive = { status: 'mounted', component: 'SloshGauge' };
    console.info(PREFIX, 'SloshGauge MOUNTED. Try dragging the gauge.');
    return () => { if (target) delete target.dataset.reactMounted; };
  }, []);

  return <div className="sb-real-react" data-component="ReactBits-SloshGauge">
    <div className="sb-real-react__meter">
      <SloshGauge
        value={value} onChange={setValue} interactive showValue
        liquidColor="#91F7DF" glassColor="#242052" width={118} height={208}
        radius={22} ticks={5} viscosity={0.17} tilt={0.43} splash={0.42}
        unit="%" ariaLabel="Curiosity meter: drag up or down to change"
      />
    </div>
    <div className="sb-real-react__content">
      <span className="sb-real-react__live" role="status"><span aria-hidden="true">●</span> REACT BITS IS LIVE</span>
      <span className="sb-real-react__eyebrow">THE CURIOSITY METER</span>
      <div className="sb-real-react__value" aria-live="off">{value}<span>%</span></div>
      <p>Grab the liquid. Drag it. Feel the interface respond.</p>
      <div className="sb-real-react__actions" role="group" aria-label="Try a curiosity level">
        <button type="button" onClick={() => setValue(25)}>Chill</button>
        <button type="button" onClick={() => setValue(60)}>Explore</button>
        <button type="button" onClick={() => setValue(95)}>SUPER curious</button>
      </div>
      <small>Interactive preview only · Your learning progress isn't changed</small>
    </div>
  </div>;
}

function mount() {
  const target = document.getElementById(TARGET);
  window.StudyBuddyReactBitsLive = { status: target ? 'starting' : 'target-missing' };
  if (!target) {
    console.warn(PREFIX, `Target #${TARGET} not found on this page`);
    return;
  }
  if (target.dataset.reactBooted === 'yes') return;
  target.dataset.reactBooted = 'yes';
  try {
    createRoot(target).render(<MeterErrorBoundary><RealCuriosityMeter /></MeterErrorBoundary>);
  } catch (error) {
    window.StudyBuddyReactBitsLive = { status: 'error', error: String(error) };
    console.error(PREFIX, 'React initialization failed:', error);
    target.dataset.reactBooted = 'no';
  }
}

console.info(PREFIX, 'Standalone bundle EXECUTED, document state:', document.readyState);
window.StudyBuddyReactBitsLive = { status: 'script-executed' };
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mount, { once: true });
} else {
  mount();
}
