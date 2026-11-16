import React, { useEffect, useRef, useState } from 'react';
import AccordionGallery from './AccordionGallery.jsx';

export default function W8RoleGallery({items=[]}) {
  const root=useRef(null);
  const [compact,setCompact]=useState(() => window.matchMedia('(max-width: 650px), (prefers-reduced-motion: reduce)').matches);
  useEffect(() => {
    const mq=window.matchMedia('(max-width: 650px), (prefers-reduced-motion: reduce)');
    const update=()=>setCompact(mq.matches);
    mq.addEventListener?.('change',update);
    return ()=>mq.removeEventListener?.('change',update);
  },[]);
  const safe=Array.isArray(items)?items.filter(item=>item?.image && item?.link && item?.label).slice(0,6):[];
  useEffect(()=>{
    const host=root.current?.closest('[data-sbw8-role-host]');
    if(host && safe.length>=2 && !compact) {host.dataset.sbw8Ready='1';return ()=>{delete host.dataset.sbw8Ready;};}
  },[safe.length,compact]);
  if(safe.length<2 || compact)return null;
  return <div ref={root} className="sbw8-role-gallery" aria-label="Illustrated StudyBuddy journeys">
    <AccordionGallery items={safe} height={470} defaultIndex={1} gap={10} radius={23}
      expandRatio={0.46} grayscale={false} duration={0.54} tilt={1.5} parallax={0.12}
      overlayColor="#060b20" accentColor="#c1a9ff" textColor="#ffffff"
      trigger="hover" showLabels />
    <p className="sbw8-role-hint">Explore a journey: point, focus, or tap a picture. Select the active picture to open its role guide.</p>
  </div>;
}
