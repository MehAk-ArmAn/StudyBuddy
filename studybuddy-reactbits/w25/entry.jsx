import React from 'react';
import { createRoot } from 'react-dom/client';
import GooeyNav from '../w24/source/GooeyNav.jsx';
import '../w24/source/GooeyNav.css';

const mount = document.querySelector('[data-sb25-react-host]');
if (mount) {
  const items = [
    { label: 'Apps', href: '/apps' },
    { label: 'Roles', href: '/roles' },
    { label: 'For schools', href: '/for-schools' },
    { label: 'How it works', href: '/#story' }
  ];
  const active = mount.dataset.sb25Active;
  const selected = { apps: 0, roles: 1, schools: 2 }[active];
  try {
    createRoot(mount).render(
      <GooeyNav
        items={items}
        particleCount={20}
        particleDistances={[90, 10]}
        particleR={100}
        spread={90}
        gooeyness={0.6}
        wobble={0.42}
        initialActiveIndex={Number.isInteger(selected) ? selected : -1}
        animationTime={1500}
        timeVariance={260}
        colors={['#7166E8','#927CF2','#75B5F7','#A8B8FF']}
      
    color="#7166e8"

    activeTextColor="#ffffff"
/>
    );
    window.StudyBuddyShell25React = { source: 'original-project-GooeyNav', items: items.map(item => item.label) };
  } catch (error) {
    // React errors within effects are not guaranteed to be caught here;
    // Laravel links remain the pre-rendered fallback until hydration.
    console.error('[StudyBuddy W25] Gooey navigation could not mount', error);
  }
}
