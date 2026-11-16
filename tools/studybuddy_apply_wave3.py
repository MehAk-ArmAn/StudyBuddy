#!/usr/bin/env python3
"""Patch only known public Blade anchors and the existing additive React registry."""
from pathlib import Path
import sys,re,json
P=Path(sys.argv[1]).expanduser().resolve()
home=P/'resources/views/home.blade.php'
entry=P/'resources/js/studybuddy-react/entry.jsx'
if not home.is_file() or not entry.is_file():raise SystemExit('STOP: StudyBuddy Blade + React core bridge not found.')
h=home.read_text();e=entry.read_text()
if 'const REGISTRY = new Map();' not in e or '<section class="sb7-section sb7-apps"' not in h:
 raise SystemExit('STOP: StudyBuddy source anchors changed. No changes made.')
if 'SB_WAVE3_IMPORTS' in e and 'SB_WAVE3_HOMEPAGE' in h:
 print('W3 homepage and React registry already patched; nothing to duplicate.');sys.exit(0)
# Remove old public, test-only tube, preserving every other homepage section.
if 'SB_ONE_LIVE_REACTBITS_START' in h and 'SB_ONE_LIVE_REACTBITS_END' in h:
 h,n=re.subn(r'\s*<!-- SB_ONE_LIVE_REACTBITS_START[^>]*-->.*?<!-- SB_ONE_LIVE_REACTBITS_END[^>]* -->\s*','\n',h,count=1,flags=re.S)
 if not n:
  h,n=re.subn(r'\s*<!-- SB_ONE_LIVE_REACTBITS_START[^>]*-->.*?<!-- SB_ONE_LIVE_REACTBITS_END[^>]*-->\s*','\n',h,count=1,flags=re.S)
 if not n:raise SystemExit('STOP: cannot isolate the old test tube safely.')
elif 'id="sb-one-live-section"' in h:
 # Defensive balanced section tag extraction; don't greedily eat adjacent homepage sections.
 opening=list(re.finditer(r'<section\b[^>]*\bid="sb-one-live-section"[^>]*>',h,re.S))
 if len(opening)!=1:raise SystemExit('STOP: duplicate or malformed tube section.')
 start=opening[0].start();depth=0;end=None
 for match in re.finditer(r'<(/?)section\b[^>]*>',h[start:],re.S):
  depth += -1 if match.group(1) else 1
  if depth==0:end=start+match.end();break
 if end is None:raise SystemExit('STOP: unbalanced old tube markup.')
 h=h[:start]+h[end:]
# Explicitly stop the original React bridge from recreating the test tube.
e=e.replace('  placeHomepageDemo();','  /* W3: old test-only CuriosityMeter is not auto-mounted on the homepage. */')
# Only the official Laravel bridge registry gets new sources.
imports='''/* SB_WAVE3_IMPORTS: additions only, no Laravel routing changes */
import { SBAppFlexGallery, SBRoleFlexGallery, SBFeatureFlexGallery, SBPersonalCorner } from './components/StudyBuddyWave3/StudyBuddyWave3.jsx';
import Crosshair from './components/StudyBuddyWave3/Crosshair.jsx';
import { lazy as sbw3Lazy, Suspense as SBW3Suspense } from 'react';
const SBW3LanyardLazy = sbw3Lazy(() => import('./components/StudyBuddyWave3/Lanyard.jsx'));
function SBW3Lanyard(props) { return <SBW3Suspense fallback={null}><SBW3LanyardLazy {...props}/></SBW3Suspense>; }
/* SB_WAVE3_IMPORTS_END */
'''
if 'SB_WAVE3_IMPORTS' not in e:
 e=e.replace('const REGISTRY = new Map();',imports+'\nconst REGISTRY = new Map();',1)
 registrations='''/* SB_WAVE3_REGISTRY */
REGISTRY.set('SBAppFlexGallery', SBAppFlexGallery);
REGISTRY.set('SBRoleFlexGallery', SBRoleFlexGallery);
REGISTRY.set('SBFeatureFlexGallery', SBFeatureFlexGallery);
REGISTRY.set('SBPersonalCorner', SBPersonalCorner);
REGISTRY.set('SBCrosshair', Crosshair);
REGISTRY.set('SBLanyard', SBW3Lanyard);
'''
 e=e.replace("REGISTRY.set('SloshGauge', SloshGauge);", "REGISTRY.set('SloshGauge', SloshGauge);\n"+registrations,1)
 if 'SB_WAVE3_REGISTRY' not in e:raise SystemExit('STOP: unable to connect to real React bridge.')
# Discover real app cards on the existing /apps page. Do not invent cards or interfere with Laravel search.
pagejs='''
/* SB_WAVE3_PAGE_DISCOVERY: only append mounts to existing pages. */
function sbw3AdditionalMounts() {
  const path = location.pathname.replace(/\\/$/, '') || '/';
  const main = document.querySelector('main');
  if (!main) return;
  if (path === '/apps' && !document.getElementById('sbw3-apps-page')) {
    const cards = Array.from(main.querySelectorAll('a[href]')).filter(a => {
      const img = a.querySelector('img[src]');
      if (!img) return false;
      try {const p = new URL(a.getAttribute('href'), location.href);return p.origin === location.origin && p.pathname.startsWith('/apps/') && p.pathname !== '/apps/';}catch{return false;}
    });
    const unique = new Map();
    cards.forEach(a => {
      const img=a.querySelector('img[src]');
      const title=a.querySelector('h2,h3,h4,[class*="title"]')?.textContent?.trim() || img?.getAttribute('alt')?.trim();
      const url=a.getAttribute('href');
      if(!title || !url || !img) return;
      const full=new URL(url,location.href);
      unique.set(full.pathname,{src:img.currentSrc || img.src,alt:img.alt||title,title,subtitle:a.querySelector('[class*="category"],[class*="tagline"]')?.textContent?.trim()||'',url:full.pathname});
    });
    const items=[...unique.values()].slice(0,18);
    if(items.length>=3){
      const shell=document.createElement('section');shell.id='sbw3-apps-page';shell.className='sbw3-dashboard-insertion';
      shell.innerHTML='<h2 style="margin:15px 0;letter-spacing:-.035em">Explore visually <span aria-hidden="true">✦</span></h2>';
      const widget=document.createElement('div');widget.dataset.sbReact='SBAppFlexGallery';widget.dataset.sbReactProps=JSON.stringify({items});shell.appendChild(widget);
      const ref=cards[0].closest('section, .grid, [class*="grid"], [class*="list"]')||cards[0];
      ref.parentElement?.insertBefore(shell,ref);
    }
  }
  if(path==='/roles' && !document.getElementById('sbw3-roles-page')){
    const slot=document.createElement('div');slot.id='sbw3-roles-page';slot.dataset.sbReact='SBRoleFlexGallery';
    main.prepend(slot);
  }
  // Public learning preferences are device-local and never touch Laravel grades or permissions.
  if(/\\/(?:student|parent|teacher)(?:\\/|$)/.test(path) && /dashboard|home|overview/.test(path) && !document.getElementById('sbw3-dashboard-corner')){
    const section=document.createElement('div');section.id='sbw3-dashboard-corner';section.className='sbw3-dashboard-insertion';
    section.dataset.sbReact='SBPersonalCorner';section.dataset.sbReactProps=JSON.stringify({context:'dashboard'});
    const heading=main.querySelector('h1');if(heading?.parentElement===main)heading.insertAdjacentElement('afterend',section);else main.prepend(section);
  }
}
'''
if 'SB_WAVE3_PAGE_DISCOVERY' not in e:
 e=e.replace('function scan() {',pagejs+'\nfunction scan() {\n  sbw3AdditionalMounts();',1)
 if 'SB_WAVE3_PAGE_DISCOVERY' not in e:raise SystemExit('STOP: no registry scan found.')
# Replace only old Flex mount with the real bundled component. JSON contents are Laravel CMS source.
old='<div class="sb8-flex-mount" data-sb8-component="flex" data-sb8-config="sb8-home-carousel" aria-label="Featured StudyBuddy activities"></div>'
new='<div class="sbw3-home-flex" data-sb-react="SBAppFlexGallery" data-sb-react-props=\'{"jsonId":"sb8-home-carousel"}\' aria-label="StudyBuddy app discovery carousel"></div>'
if old in h:
 h=h.replace(old,new,1)
elif 'data-sb8-component="flex"' in h:
 h,n=re.subn(r'<div class="sb8-flex-mount"[^>]*>\s*</div>',new,h,count=1)
 if not n:raise SystemExit('STOP: existing Flex mount differs from expected source.')
else:raise SystemExit('STOP: original homepage carousel JSON markup missing.')
# Real role-based visual gallery and a personal corner; every existing CMS block stays.
role='''<!-- SB_WAVE3_HOMEPAGE: free Flex Carousel + real Micro interactions -->
<div class="sbw3-role-mount" data-sb-react="SBRoleFlexGallery"></div>
<div class="sbw3-personal-mount" data-sb-react="SBPersonalCorner" data-sb-react-props='{"context":"public"}'></div>
'''
anchor='@if($what && $what->items->isNotEmpty())'
if h.count(anchor)!=1:raise SystemExit('STOP: homepage role/feature boundary differs.')
h=h.replace(anchor,role+'\n'+anchor,1)
# Feature art is paired with REAL CMS title/subtitle/URL. Use original StudyBuddy vector art only.
feature='''@php
  $sbw3FeatureArt = ['role-student.svg', 'role-family.svg', 'role-teacher.svg', 'role-independent.svg'];
  $sbw3Features = collect($what->items)->take(8)->values()->map(function ($item, $index) use ($sbw3FeatureArt) {
    return ['src' => asset('assets/studybuddy-wave3/'.$sbw3FeatureArt[$index % 4]),
      'title' => (string) $item->title,
      'alt' => (string) $item->title,
      'subtitle' => (string) ($item->subtitle ?: ''),
      'url' => $item->button_url ?: url('/apps')];
  })->all();
@endphp
@if(count($sbw3Features) >= 3)
<div class="sbw3-feature-mount" data-sb-react="SBFeatureFlexGallery" data-sb-react-props='{"jsonId":"sbw3-feature-data"}'></div>
<script type="application/json" id="sbw3-feature-data">@json($sbw3Features)</script>
@endif
'''
anchor='<div class="sb7-what__grid">'
if h.count(anchor)==1: h=h.replace(anchor,feature+anchor,1)
# Do not discard a single Blade condition/loop or CMS headline.
if 'id="sb-one-live-section"' in h:raise SystemExit('STOP: test tube marker still present')
if 'SB_WAVE3_REGISTRY' not in e:raise SystemExit('STOP: registry missing')
home.write_text(h);entry.write_text(e)
print('Removed tube; reused real homepage carousel JSON; added role+feature and personal mounts.')
print('Registered actual React Bits components on the single working Laravel bridge.')
