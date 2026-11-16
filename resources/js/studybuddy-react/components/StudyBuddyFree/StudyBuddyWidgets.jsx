/* STUDYBUDDY FREE BITS — actual official component sources adapted to the Laravel React bridge.
   Never use these widgets as a permission gate or a substitute for real CMS data. */
import React from 'react';
import GradientText from './GradientText.jsx';
import RotatingText from './RotatingText.jsx';
import Counter from './Counter.jsx';
import ShapeGrid from './ShapeGrid.jsx';
import GridMotion from './GridMotion.jsx';
import './studybuddy-widgets.css';

export function SBHeroMotion() {
  return (
    <div className="sbfb-hero-words" aria-label="Learn, play and grow">
      <span className="sbfb-hero-words__fixed">A PLACE TO</span>
      <RotatingText
        texts={['EXPLORE.', 'CREATE.', 'GROW.']}
        effect="slide"
        direction="up"
        rotationInterval={3400}
        staggerDuration={0.025}
        splitLevelClassName="sbfb-hero-words__clip"
        mainClassName="sbfb-hero-words__rotating"
      />
      <span className="sbfb-hero-words__spark" aria-hidden="true">✦</span>
    </div>
  );
}

export function SBGradientNote({text='LITTLE DISCOVERIES. BIG IDEAS.'}) {
  return (
    <span className="sbfb-gradient-note">
      <GradientText
        colors={['#865cff', '#d04bb7', '#157c94']}
        variant="linear"
        animationSpeed={11}
        glow={0}
      >{text}</GradientText>
    </span>
  );
}

export function SBHeroShapeGrid() {
  return (
    <div className="sbfb-hero-grid" aria-hidden="true">
      <ShapeGrid
        shape="dot"
        cellSize={46}
        direction="diagonal"
        lineColor="#9982e5"
        hoverColor="#8a67ef"
        hoverFillColor="#c49cfe"
        fillOpacity={0.06}
        speed={0.2}
        fade={1}
        opacity={0.55}
        clickCascade={false}
      />
    </div>
  );
}

export function SBCounter({value=0}) {
  const numeric = Number(value);
  return (
    <span className="sbfb-ready-num">
    <Counter
      value={Number.isFinite(numeric) ? numeric : 0}
      fontSize="inherit"
      fontWeight="inherit"
      duration={0.65}
      stagger={0.025}
      bounce={0.1}
      motionBlur={false}
      intro
    />
    </span>
  );
}

/* Registered for future CMS-powered galleries. No generic imagery or fake links. */
export function SBArtworkWall({items=[], theme='dark'}) {
  const valid = (Array.isArray(items) ? items : []).filter(item => typeof item?.src === 'string' && item.src.length > 0).slice(0,12);
  if (valid.length < 3) return null;
  return (
    <div className="sbfb-artwall" role="img" aria-label="StudyBuddy learning app artwork gallery">
      <GridMotion
        items={valid}
        rows={2}
        aspectRatio={1.33}
        gap={12}
        radius={16}
        angle={-8}
        speed={11}
        parallax={0.25}
        spotlight={0.35}
        dim={0.18}
        fade={0.5}
        theme={theme === 'light' ? 'light' : 'dark'}
      />
    </div>
  );
}
