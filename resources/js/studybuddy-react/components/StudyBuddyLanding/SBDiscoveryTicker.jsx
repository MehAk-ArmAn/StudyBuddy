import React from 'react';
import ScrollVelocity from './ScrollVelocity.jsx';
export default function SBDiscoveryTicker() {
  return (
    <div aria-hidden="true" className="sb12-discovery-ticker">
      <ScrollVelocity texts={['DISCOVER ✦ CREATE ✦ GROW ✦', 'LEARN ✦ PLAY ✦ REPEAT ✦']} velocity={18} skewWithVelocity={1.5} scaleWithVelocity={0} draggable={false} pauseOnHover fontSize={20} fontWeight={900} numCopies={5} separator="  ·  " fadeEdges />
    </div>
  );
}
