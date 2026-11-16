/* StudyBuddy original brand choreography. No paid/pro React Bits source.
   Re-uses the existing brand logo artwork; all visuals are SVG/CSS. */
import React, { useState } from 'react';
import './SBLogoSequence.css';

export default function SBLogoSequence({ logo = '/assets/studybuddy-brand/studybuddy-logo.png' }) {
  const [replay, setReplay] = useState(0);
  return (
    <div className="sb12-logo-outer" aria-label="StudyBuddy — learn, play, grow">
      <div key={replay} className="sb12-logo-story" data-sb12-logo-story>
        <span className="sb12-logo-symbol" aria-hidden="true">
          <svg className="sb12-logo-traces" viewBox="0 0 100 100" fill="none" aria-hidden="true">
            <circle cx="50" cy="50" r="38" pathLength="1" />
            <path d="M11 61 C 20 13, 67 4, 87 37 S 70 92, 41 89" pathLength="1" />
            <path d="M20 30 Q 50 13, 81 28" pathLength="1" />
          </svg>
          <span className="sb12-logo-spark sb12-logo-spark--one">✦</span>
          <span className="sb12-logo-spark sb12-logo-spark--two">✧</span>
          <img src={logo} alt="" decoding="async" width="64" height="64" />
        </span>
        <span className="sb12-logo-words">
          <strong><span>Study</span><span>Buddy</span></strong>
          <small>THE JOY OF DISCOVERING</small>
        </span>
      </div>
      <button type="button" className="sb12-logo-replay" onClick={() => setReplay(v => v + 1)} aria-label="Replay StudyBuddy logo animation" title="Replay logo animation">
        <svg viewBox="0 0 24 24" aria-hidden="true" fill="none"><path d="M20 11a8 8 0 1 1-2.35-5.65M20 4v7h-7" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" /></svg>
      </button>
    </div>
  );
}
