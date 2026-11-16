import React, { useEffect, useMemo, useState } from 'react';
import FlexCarousel from './FlexCarousel.jsx';
import FolderFloat from './FolderFloat.jsx';
import SpringCheck from './micro/SpringCheck/SpringCheck.jsx';
import GlideSelect from './micro/GlideSelect/GlideSelect.jsx';
import SwipeToast from './micro/SwipeToast/SwipeToast.jsx';
import JellyRadio from './micro/JellyRadio/JellyRadio.jsx';
import SquishSwitch from './micro/SquishSwitch/SquishSwitch.jsx';
import PulseHeart from './micro/PulseHeart/PulseHeart.jsx';
import './studybuddy-wave3.css';

const FAVORITES_KEY = 'studybuddy:public-app-shelf:v1';
const PREFS_KEY = 'studybuddy:experience-preferences:v1';
const DAY_GOALS = ['Discover one new activity', 'Learn something surprising', 'Share an idea or question'];
const roles = [
  { id: 'student', title: 'For curious learners', subtitle: 'Discover and practise in your own way', src: '/assets/studybuddy-wave3/role-student.svg', url: '/roles' },
  { id: 'parent', title: 'For families', subtitle: 'Stay close to the learning journey', src: '/assets/studybuddy-wave3/role-family.svg', url: '/roles' },
  { id: 'teacher', title: 'For educators', subtitle: 'Make thoughtful classroom moments count', src: '/assets/studybuddy-wave3/role-teacher.svg', url: '/roles' },
  { id: 'independent', title: 'For independent minds', subtitle: 'Follow your own questions', src: '/assets/studybuddy-wave3/role-independent.svg', url: '/roles' }
];
function getLocal(key, fallback) {
  try { const value = JSON.parse(localStorage.getItem(key) || 'null'); return value ?? fallback; }
  catch { return fallback; }
}
function putLocal(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); } catch { /* browsing remains usable */ } }
function safeDestination(url) {
  try { const parsed = new URL(url, window.location.href); return parsed.origin === location.origin && /^https?:$/.test(parsed.protocol) ? parsed.pathname + parsed.search + parsed.hash : null; }
  catch { return null; }
}
function validateItem(item) {
  if (!item || typeof item !== 'object' || !item.src || !item.title) return null;
  const url = safeDestination(item.url || '/apps');
  const src = String(item.src);
  // Actual images supplied by Laravel; never inject arbitrary JavaScript or HTML URLs.
  if (!url || !(src.startsWith('/') || /^https:\/\//i.test(src))) return null;
  return { src, title: String(item.title), alt: String(item.alt || item.title), subtitle: String(item.subtitle || ''), url, demo: !!item.demo };
}
function useItems(items, jsonId) {
  return useMemo(() => {
    let incoming = Array.isArray(items) ? items : [];
    if (jsonId) {
      try { incoming = JSON.parse(document.getElementById(jsonId)?.textContent || '[]'); } catch { incoming = []; }
    }
    return incoming.map(validateItem).filter(Boolean).slice(0, 24);
  }, [items, jsonId]);
}
function supportsCarousel() {
  try {
    if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches) return false;
    const canvas = document.createElement('canvas');
    const gl = canvas.getContext('webgl2');
    const supported = !!gl;
    gl?.getExtension('WEBGL_lose_context')?.loseContext();
    return supported;
  } catch { return false; }
}
function AccessibleCards({ items, onPick }) {
  return <div className="sbw3-cardrail" role="list">{items.map((item, i) => <a role="listitem" key={`${item.title}-${i}`} href={item.url} className="sbw3-cardrail__item" onClick={() => onPick?.(i)}>
    <img src={item.src} alt={item.alt} loading="lazy" /><span className="sbw3-cardrail__copy"><strong>{item.title}</strong><small>{item.subtitle}</small></span>
  </a>)}</div>;
}
export function SBAppFlexGallery({ items, jsonId }) {
  const collection = useItems(items, jsonId);
  const [favoriteUrls, setFavoriteUrls] = useState(() => getLocal(FAVORITES_KEY, []));
  const [chosen, setChosen] = useState(0);
  const [category, setCategory] = useState('All subjects');
  const [mode, setMode] = useState('All');
  const [motion, setMotion] = useState(() => getLocal(PREFS_KEY, {}).motion !== false);
  const [toast, setToast] = useState(null);
  const [gpu] = useState(supportsCarousel);
  const categories = [...new Set(collection.map(item => item.subtitle).filter(Boolean))].slice(0, 8);
  const filtered = collection.filter(item => (category === 'All subjects' || item.subtitle === category) && (mode !== 'Saved' || favoriteUrls.includes(item.url)));
  const current = filtered[Math.min(Math.max(chosen, 0), Math.max(0, filtered.length - 1))];
  useEffect(() => { putLocal(FAVORITES_KEY, favoriteUrls); }, [favoriteUrls]);
  useEffect(() => { const p = getLocal(PREFS_KEY, {}); putLocal(PREFS_KEY, { ...p, motion }); }, [motion]);
  useEffect(() => { setChosen(0); }, [category, mode]);
  const flipFavorite = item => {
    const has = favoriteUrls.includes(item.url);
    setFavoriteUrls(previous => has ? previous.filter(url => url !== item.url) : [...new Set([...previous, item.url])]);
    setToast({ title: has ? 'Removed from My Shelf' : 'Saved to My Shelf', description: 'Saved in this browser, not in your school account.', key: Date.now() });
  };
  const savedItems = collection.filter(item => favoriteUrls.includes(item.url));
  if (collection.length === 0) return null;
  return <section className="sbw3-app-experience" aria-label="Discover activities in a visual carousel">
    <div className="sbw3-gallery-controls">
      <div className="sbw3-gallery-controls__left"><strong>Find your next little adventure</strong><small>Real StudyBuddy activities · swipe, drag or use the arrow keys</small></div>
      {categories.length > 1 && <div className="sbw3-field"><span>Subject</span><GlideSelect options={['All subjects', ...categories]} value={category} onChange={setCategory} accentColor="#29bcae" /></div>}
      <div className="sbw3-mode"><JellyRadio items={['All', 'Saved']} value={mode} onChange={setMode} ariaLabel="Show all apps or saved apps" /></div>
    </div>
    {filtered.length ? <>
      {motion && gpu && filtered.length >= 3 ? <div className="sbw3-fluid-gallery" key={`${category}/${mode}`}><FlexCarousel items={filtered} intro="rise" preset="liquid" fit="natural" gap={12} cardHeight={0.58} radius={18} captions focusOnClick captureWheel={false} onChange={i => setChosen(i)} onSelect={(i, item) => { const target = safeDestination(item?.url); if (target) window.location.assign(target); }} /></div> : <AccessibleCards items={filtered} onPick={setChosen} />}
      {current && <div className="sbw3-selected"><span className="sbw3-selected__name"><strong>{current.title}</strong><small>{current.subtitle || 'StudyBuddy learning activity'}</small></span><PulseHeart liked={favoriteUrls.includes(current.url)} showCount={false} size={28} label={favoriteUrls.includes(current.url) ? 'Remove saved activity' : 'Save activity to My Shelf'} onChange={() => flipFavorite(current)} /><a href={current.url} className="sbw3-action">{current.demo ? 'View concept' : 'Explore activity'} <span aria-hidden="true">↗</span></a></div>}
    </> : <p className="sbw3-empty">Your shelf is ready for its first discovery. Switch to All, open an activity and save it with the heart.</p>}
    <div className="sbw3-gallery-bottom"><label className="sbw3-toggle"><SquishSwitch checked={motion} label="Animated gallery" onChange={setMotion} trackOnColor="#35d3bf" /></label><small>Personal picks are stored only on this device. Full app links stay available below.</small></div>
    {savedItems.length > 0 && <div className="sbw3-shelf"><div><strong>My little shelf</strong><p>Open a saved app from your own folder.</p></div><FolderFloat items={savedItems.map(item => ({label:item.title,value:item.url}))} label="Saved apps" sublabel={`${savedItems.length} saved`} trigger="click" physics={false} width={170} height={116} spread={180} folderColor="#5156a2" frontColor="#878dcd" onSelect={url => { const target = safeDestination(url); if(target) window.location.assign(target); }} /></div>}
    {toast && <SwipeToast key={toast.key} title={toast.title} description={toast.description} open={true} duration={3000} closeButton onClose={() => setToast(null)} />}
  </section>;
}
export function SBRoleFlexGallery() {
  const [gpu] = useState(supportsCarousel);
  return <div className="sbw3-role-experience"><p className="sbw3-eyebrow">MADE FOR YOUR WAY OF LEARNING</p><h2>Everyone has a place in the story.</h2><p className="sbw3-intro">Find the space that makes sense for you — no guessing, no one-size-fits-all path.</p>
  {gpu ? <div className="sbw3-fluid-gallery sbw3-fluid-gallery--roles"><FlexCarousel items={roles} preset="arch" intro="bloom" fit="landscape" cardHeight={0.58} gap={14} radius={20} focusOnClick captions captureWheel={false} onSelect={() => window.location.assign('/roles')} /></div> : <AccessibleCards items={roles} />}
  <a className="sbw3-quiet-link" href="/roles">Compare learning roles and access options ↗</a></div>;
}
export function SBFeatureFlexGallery({ jsonId }) {
  const list = useItems([], jsonId);
  const [gpu] = useState(supportsCarousel);
  if (list.length < 3) return null;
  return <div className="sbw3-feature-gallery" aria-label="Explore StudyBuddy features"><p className="sbw3-eyebrow">THINGS THAT MAKE A DIFFERENCE</p>
    {gpu ? <div className="sbw3-fluid-gallery sbw3-fluid-gallery--features"><FlexCarousel items={list} preset="ribbon" intro="rise" fit="landscape" cardHeight={0.58} radius={18} captions focusOnClick captureWheel={false} onSelect={(i,item)=>{const target=safeDestination(item?.url);if(target) window.location.assign(target);}} /></div> : <AccessibleCards items={list} />}
  </div>;
}
export function SBPersonalCorner({ context = 'learning' }) {
  const pathKey = String(context || 'learning').replace(/[^a-z0-9_-]/gi,'').slice(0,32);
  const store = `studybuddy:own-learning-ideas:${pathKey}:v1`;
  const [goals, setGoals] = useState(() => getLocal(store, {}));
  const [role, setRole] = useState(() => getLocal(PREFS_KEY, {}).role || 'Student');
  const [toast, setToast] = useState(null);
  const checked = DAY_GOALS.filter((_, index)=>goals[index]).length;
  useEffect(() => { putLocal(store, goals); }, [goals, store]);
  function chooseRole(next) {
    setRole(next);
    putLocal(PREFS_KEY, {...getLocal(PREFS_KEY, {}), role:next});
    setToast({ key:Date.now(), title:`Your view: ${next}`, description:'Your preference is saved in this browser.' });
  }
  return <section className="sbw3-personal-corner" aria-label="Your personal learning corner">
    <div className="sbw3-personal-main"><span className="sbw3-eyebrow">A LITTLE SPACE THAT FEELS LIKE YOU</span><h3>Make learning <em>your own.</em></h3><p>Choose your perspective, collect ideas, and set little goals that fit you.</p>
      <div className="sbw3-role-picker"><label>I'm exploring as</label><JellyRadio items={['Student','Parent','Teacher','Independent']} value={role} onChange={chooseRole} size="sm" ariaLabel="Your StudyBuddy perspective" /></div>
      <a href="/roles" className="sbw3-quiet-link">Learn what each role can do ↗</a>
    </div>
    <div className="sbw3-personal-goals"><div className="sbw3-goals-title"><strong>My little plan</strong><span>{checked} of {DAY_GOALS.length}</span></div>{DAY_GOALS.map((label,index)=><div className="sbw3-goal" key={label}><SpringCheck label={label} checked={!!goals[index]} color="var(--sbw3-ink)" fillColor="#30c7b5" checkColor="#0b2132" onChange={on => setGoals(previous=>({...previous,[index]:on}))} /></div>)}<small>Your self-chosen checklist stays in this browser. It does not change school grades or official activity records.</small></div>
    {toast && <SwipeToast key={toast.key} title={toast.title} description={toast.description} duration={3000} onClose={() => setToast(null)} closeButton />}
  </section>;
}
