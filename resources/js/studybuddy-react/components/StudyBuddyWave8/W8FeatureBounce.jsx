import React, { useEffect, useRef, useState } from 'react';
import BounceCards from './BounceCards.jsx';
export default function W8FeatureBounce({images=[]}) {
  const ref=useRef(null);
  const [width,setWidth]=useState(480);
  const list=Array.isArray(images)?images.filter(item=>typeof item?.src==='string'&&item.src).slice(0,5):[];
  useEffect(()=>{
    if(!ref.current)return;
    const ro=new ResizeObserver(([entry])=>setWidth(Math.max(260,Math.min(550,entry.contentRect.width))));
    ro.observe(ref.current);
    return ()=>ro.disconnect();
  },[]);
  useEffect(()=>{
    const host=ref.current?.closest('[data-sbw8-bounce-host]');
    if(host&&list.length>=2){host.dataset.sbw8Ready='1';return ()=>{delete host.dataset.sbw8Ready;};}
  },[list.length]);
  if(list.length<2)return null;
  const cardSize=Math.round(Math.min(225,Math.max(140,width*.43)));
  return <div ref={ref} className="sbw8-feature-bounce" aria-label="StudyBuddy moments of discovery">
    <BounceCards images={list} containerWidth={width} containerHeight={360}
      cardSize={cardSize} spread={width*.145} rotation={9} arc={25}
      pushDistance={width*.14} hoverScale={1.05} borderWidth={3}
      borderColor="#ebe7ff" radius={22} animationDelay={0.15} bounciness={0.44}
      enableHover shadow/>
  </div>;
}
