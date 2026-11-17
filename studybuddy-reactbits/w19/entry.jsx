/* StudyBuddy W19: independent, original-source React Bits integration.
   Does not depend on W15, mutate W13, or take over Laravel DOM. */
import React, { useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import Galaxy from './components/Galaxy.jsx';
import StarBorder from './components/StarBorder.jsx';
import './w19.css';

const SETTINGS = {
  density: 1, glowIntensity: 0.3, flareIntensity: 1, starSpeed: 0.5,
  mouseRepulsion: true, clickWarp: true
};
const roots = [];
const managed = new WeakSet();
const reduceMotion = () => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false;
const themeNow = () => document.documentElement.classList.contains('dark') ||
  document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
const isVisible = el => {
  const rect = el.getBoundingClientRect();
  const cs = getComputedStyle(el);
  return rect.width > 65 && rect.height > 26 && cs.display !== 'none' && cs.visibility !== 'hidden';
};

function StarAdornment({ anchor }) {
  const wrapRef = useRef(null);
  const [theme, setTheme] = useState(themeNow);
  useEffect(() => {
    const observer = new MutationObserver(() => setTheme(themeNow()));
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
    const halo = () => wrapRef.current?.querySelector('.star-border');
    const forward = (name, event) => {
      const target = halo();
      if (!target) return;
      const rect = target.getBoundingClientRect();
      const payload = {
        bubbles: false,
        pointerType: event?.pointerType || 'mouse',
        button: event?.button ?? 0,
        clientX: event?.clientX ?? rect.left + rect.width / 2,
        clientY: event?.clientY ?? rect.top + rect.height / 2
      };
      target.dispatchEvent(new PointerEvent(name, payload));
    };
    const enter = e => forward('pointerenter', e);
    const leave = e => forward('pointerleave', e);
    const down = e => forward('pointerdown', e);
    const focus = () => forward('pointerenter');
    const blur = () => forward('pointerleave');
    const keyboard = e => { if (e.key === 'Enter' || e.key === ' ') forward('pointerdown'); };
    anchor.addEventListener('pointerenter', enter);
    anchor.addEventListener('pointerleave', leave);
    anchor.addEventListener('pointerdown', down);
    anchor.addEventListener('focus', focus);
    anchor.addEventListener('blur', blur);
    anchor.addEventListener('keydown', keyboard);
    return () => {
      observer.disconnect();
      anchor.removeEventListener('pointerenter', enter);
      anchor.removeEventListener('pointerleave', leave);
      anchor.removeEventListener('pointerdown', down);
      anchor.removeEventListener('focus', focus);
      anchor.removeEventListener('blur', blur);
      anchor.removeEventListener('keydown', keyboard);
    };
  }, [anchor]);
  const color = theme === 'dark' ? '#ffffff' : '#6746d4';
  return <div ref={wrapRef} className="sb19-decoration-inner" aria-hidden="true">
    <StarBorder
      as="span" color={color} trailColor={color} duration={4}
      direction="clockwise" stars={1} trailLength={0.3} thickness={1}
      radius={12} glow={0.6} hover="lap" sparkle={false} clickPulse
      theme={theme} backgroundColor="transparent" borderColor="transparent"
      textColor="transparent" className="sb19-original-starborder"
    />
  </div>;
}

function mountGalaxy() {
  if (!document.documentElement.hasAttribute('data-studybuddy-exact')) return;
  const original = document.querySelector('.galaxy-container');
  const hero = original?.closest('section');
  if (!hero || document.getElementById('sb19-galaxy-host')) return;
  const host = document.createElement('div');
  host.id = 'sb19-galaxy-host';
  host.setAttribute('aria-hidden', 'true');
  hero.insertBefore(host, hero.firstChild);
  const root = createRoot(host);
  roots.push(root);
  root.render(<Galaxy {...SETTINGS} disableAnimation={reduceMotion()} className="sb19-original-galaxy" />);
}

function isAction(el) {
  if (el.closest('nav,header,footer,.w13-quick,.sb19-decoration,.sb19-decoration-inner')) return false;
  if (!isVisible(el) || el.disabled) return false;
  const t = (el.textContent || el.getAttribute('aria-label') || '').replace(/\s+/g, ' ').trim().toLowerCase();
  if (!t || t.length > 90) return false;
  if (/^(cancel|delete|sign out|logout|remove|back|previous|next)$/.test(t)) return false;
  const accepted = /explore learning|explore apps|create (an? )?account|start learning|get started|start your|begin (your|learning)|join studybuddy|save (goal|quest|changes)|create (goal|quest|mission)|continue learning|school onboarding|discover apps|my quest/i;
  if (el.tagName === 'A') {
    const url = new URL(el.href, location.origin);
    if (url.origin !== location.origin) return false;
    return accepted.test(t) || (/^\/(apps|register|my-quest|learning-hub)(\/|$)/.test(url.pathname) && t.length < 26);
  }
  return el.tagName === 'BUTTON' && el.type === 'submit' && accepted.test(t);
}

function mountStars() {
  const targets = [...document.querySelectorAll('main a[href],main button, [role="main"] a[href], [role="main"] button')]
    .filter(el => !managed.has(el) && isAction(el)).slice(0, 5);
  for (const anchor of targets) {
    managed.add(anchor);
    anchor.classList.add('sb19-star-target');
    const container = document.createElement('span');
    container.className = 'sb19-decoration';
    container.setAttribute('aria-hidden', 'true');
    anchor.appendChild(container);
    const root = createRoot(container);
    roots.push(root);
    root.render(<StarAdornment anchor={anchor} />);
  }
}

function diagnostics() {
  const host = document.getElementById('sb19-galaxy-host');
  const canvas = host?.querySelector('canvas');
  const rect = host?.getBoundingClientRect();
  return {
    version: 'W19', source: 'user-supplied React Bits JS-CSS',
    galaxyHost: !!host, galaxyCanvas: !!canvas,
    galaxyPixels: canvas ? [canvas.width, canvas.height] : null,
    hostSize: rect ? [Math.round(rect.width), Math.round(rect.height)] : null,
    starBorderCount: document.querySelectorAll('.sb19-original-starborder').length,
    galaxyConfig: SETTINGS, homepage: location.pathname === '/'
  };
}
function main() {
  if (window.StudyBuddyReact19) return;
  window.StudyBuddyReact19 = { diagnostics, refresh: mountStars };
  try { mountGalaxy(); mountStars(); }
  catch (error) { console.error('[StudyBuddy W19] mount failed', error); }
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', main, { once: true });
else main();
