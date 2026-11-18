/**
 * StudyBuddy Phase 08 — original React 19 private onboarding preview.
 * Built from original code + MIT-licensed React runtime, NOT React Bits Pro.
 * Never sends profile information to a remote service.
 */
import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';

type PreviewConfig = { name: string; goal: string; role: string; image: string; accent: string };
function Preview({initial}: {initial: PreviewConfig}) {
  const [name, setName] = useState(initial.name);
  const [goal, setGoal] = useState(initial.goal);
  const [accent, setAccent] = useState(initial.accent);
  useEffect(() => {
    const form = document.getElementById('sb08-form');
    if (!form) return;
    const sync = () => {
      const n = form.querySelector<HTMLInputElement>('[data-preview-field="name"]');
      const g = form.querySelector<HTMLInputElement>('[data-preview-field="goal"]');
      const a = form.querySelector<HTMLSelectElement>('[data-preview-field="accent"]');
      if (n) setName(n.value);
      if (g) setGoal(g.value);
      if (a) setAccent(a.value);
    };
    sync();
    form.addEventListener('input', sync);
    form.addEventListener('change', sync);
    return () => { form.removeEventListener('input',sync); form.removeEventListener('change',sync); };
  }, []);
  return <div className="sb08-react-preview" data-accent={accent}>
    <img src={initial.image} alt="" aria-hidden="true" />
    <span className="sb08-pill">{initial.role}</span>
    <strong>{name.trim() || 'Your name here'}</strong>
    <p>{goal.trim() || 'A universe of possibilities awaits.'}</p>
  </div>;
}
function boot(){
  const root=document.getElementById('sb08-react-preview');
  const cfg=document.getElementById('sb08-preview-data');
  if(!root || !cfg)return;
  try {
    const initial=JSON.parse(cfg.textContent||'{}') as PreviewConfig;
    createRoot(root).render(<Preview initial={initial}/>);
  } catch (error) { console.warn('StudyBuddy preview uses its accessible HTML fallback.'); }
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',boot,{once:true});else boot();
