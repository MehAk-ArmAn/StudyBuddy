// StudyBuddy first visible original React Bits component — SloshGauge.
// This React island mounts independently of WebGL, IntersectionObserver,
// Effects Off, and the optional gallery readiness checks.
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import SloshGauge from './components/StudyBuddyOfficial/SloshGauge.jsx';
import './studybuddy-live-curiosity.css';

function CuriosityMeter() {
  const [level, setLevel] = useState(62);
  useEffect(() => {
    const el = document.getElementById('sb-live-curiosity-root');
    if (el) el.dataset.sbReactMounted = 'true';
  }, []);
  return (
    <div className="sb-live-meter" data-reactbits-live="slosh-gauge">
      <div className="sb-live-meter__gauge">
        <SloshGauge
          value={level}
          onChange={setLevel}
          interactive
          showValue
          liquidColor="#8ff8e5"
          glassColor="#211b50"
          width={122}
          height={204}
          radius={24}
          ticks={4}
          viscosity={0.18}
          tilt={0.45}
          splash={0.42}
          unit="%"
          ariaLabel="Interactive curiosity level, demo only"
        />
        <span className="sb-live-meter__hand" aria-hidden="true">↕</span>
      </div>
      <div className="sb-live-meter__readout">
        <span className="sb-live-meter__ready">● REACT IS RUNNING</span>
        <span className="sb-live-meter__eyebrow">YOUR CURIOSITY RIGHT NOW</span>
        <strong className="sb-live-meter__number" aria-live="off">{level}<small>%</small></strong>
        <p>Move the liquid by dragging it or using the arrow keys.</p>
        <div className="sb-live-meter__buttons" role="group" aria-label="Try a curiosity level">
          <button type="button" onClick={() => setLevel(30)}>Chill</button>
          <button type="button" onClick={() => setLevel(65)}>Explore</button>
          <button type="button" onClick={() => setLevel(95)}>Super curious</button>
        </div>
        <small className="sb-live-meter__disclaimer">Interactive demo only · Does not save learning progress</small>
      </div>
    </div>
  );
}

function mountLiveCuriosity() {
  const host = document.getElementById('sb-live-curiosity-root');
  if (!host || host.dataset.sbReactBootstrapped) return;
  host.dataset.sbReactBootstrapped = 'true';
  try {
    createRoot(host).render(<CuriosityMeter />);
  } catch (error) {
    host.dataset.sbReactError = 'true';
    console.error('[StudyBuddy live component] SloshGauge mount failed', error);
  }
}
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountLiveCuriosity, { once: true });
} else {
  mountLiveCuriosity();
}
