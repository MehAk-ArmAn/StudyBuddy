#!/usr/bin/env python3
"""Fail closed on unexpected homepage / React source. No PHP business logic edits."""
import sys,re
from pathlib import Path
project=Path(sys.argv[1]).expanduser().resolve()
home=project/'resources/views/home.blade.php'
wave3=project/'resources/js/studybuddy-react/components/StudyBuddyWave3/StudyBuddyWave3.jsx'
if not home.is_file() or not wave3.is_file():
 raise SystemExit('STOP: W3 homepage and React source must exist. No changes made.')
h=home.read_text(); c=wave3.read_text()
if 'SB_WAVE4_HOME_START' in h:
 if 'SB_WAVE4_REACT_PROPS' in c:
  print('Already installed: W4 homepage + refined FlexCarousel props. No duplicates.');sys.exit(0)
 raise SystemExit('STOP: W4 partly installed. Restore the previous backup before retrying.')
required=['SB_WAVE3_HOMEPAGE','class="sb7 sb12-home"','class="sbw3-home-flex"','class="sbw3-role-mount"','class="sbw3-personal-mount"','<section class="sb7-section sb7-apps"']
missing=[anchor for anchor in required if anchor not in h]
if missing:raise SystemExit('STOP: W3 homepage has changed: '+', '.join(missing)+'. No edits made.')
if 'SB_WAVE3_IMPORTS' not in (project/'resources/js/studybuddy-react/entry.jsx').read_text():
 raise SystemExit('STOP: Working W3 React registry not detected.')
props=[
 ('preset="liquid" fit="natural" gap={12} cardHeight={0.58} radius={18}',
  'preset="arch" fit="landscape" gap={18} cardHeight={0.59} radius={20} bend={0.12} tilt={0} dispersion={0.08} squeeze={0.02} followCursor={false}'),
 ('preset="arch" intro="bloom" fit="landscape" cardHeight={0.58} gap={14} radius={20}',
  'preset="arch" intro="bloom" fit="landscape" cardHeight={0.54} gap={20} radius={22} bend={0.11} tilt={0} dispersion={0.07} squeeze={0.02} followCursor={false}'),
 ('preset="ribbon" intro="rise" fit="landscape" cardHeight={0.58} radius={18}',
  'preset="arch" intro="rise" fit="landscape" cardHeight={0.56} radius={20} bend={0.12} tilt={0} dispersion={0.08} squeeze={0.02}')
]
for old,new in props:
 if c.count(old)!=1:raise SystemExit('STOP: React Bits prop signature changed: '+old[:65])
 c=c.replace(old,new,1)
c='/* SB_WAVE4_REACT_PROPS: gentle lens, no caption distortion. */\n'+c
h=h.replace('class="sb7 sb12-home"','class="sb7 sb12-home sbw4-page"',1)
# Add a meaningful introduction tied to REAL Laravel app collection, not demo data.
anchor='<div class="sbw3-home-flex"'
if h.count(anchor)!=1:raise SystemExit('STOP: Expected exactly one W3 apps carousel.')
world='''<!-- SB_WAVE4_HOME_START -->
<div class="sbw4-chapter" aria-labelledby="sbw4-world-title">
  <p class="sbw4-chapter__kicker">01 / EXPLORE YOUR UNIVERSE</p>
  <h2 id="sbw4-world-title">Find a world. <em>Follow the wonder.</em></h2>
  <p>Discover real StudyBuddy learning activities. Choose a world to see what it offers, save a favourite, or browse the full catalogue.</p>
</div>
<!-- SB_WAVE4_HOME_END -->
'''
h=h.replace(anchor,world+anchor,1)
roles_mount='<div class="sbw3-role-mount" data-sb-react="SBRoleFlexGallery"></div>'
if h.count(roles_mount)!=1:raise SystemExit('STOP: W3 role mount structure changed.')
roles_companion='''
<nav class="sbw4-role-pathways" aria-label="Explore StudyBuddy roles">
 <a class="sbw4-role-pathways__card" href="{{ url('/roles') }}"><span class="sbw4-role-pathways__icon" aria-hidden="true">✦</span><strong>Students</strong><p>Discover learning worlds, make personal choices and celebrate genuine progress.</p><span>Explore the learner path ↗</span></a>
 <a class="sbw4-role-pathways__card" href="{{ url('/roles') }}"><span class="sbw4-role-pathways__icon" aria-hidden="true">♡</span><strong>Parents</strong><p>Understand available family tools and support the learning journey.</p><span>Explore the family path ↗</span></a>
 <a class="sbw4-role-pathways__card" href="{{ url('/roles') }}"><span class="sbw4-role-pathways__icon" aria-hidden="true">✎</span><strong>Teachers</strong><p>Learn about classroom experiences, planning and school access.</p><span>Explore the teacher path ↗</span></a>
 <a class="sbw4-role-pathways__card" href="{{ url('/roles') }}"><span class="sbw4-role-pathways__icon" aria-hidden="true">⌁</span><strong>Independent</strong><p>Follow your own interests and explore at a pace that suits you.</p><span>Explore independent learning ↗</span></a>
</nav>
'''
h=h.replace(roles_mount,roles_mount+roles_companion,1)
personal='<div class="sbw3-personal-mount" data-sb-react="SBPersonalCorner" data-sb-react-props=\'{"context":"public"}\'></div>'
if h.count(personal)!=1:raise SystemExit('STOP: W3 personal mount structure changed.')
h=h.replace(personal,'<section class="sbw4-personal-wrap" aria-label="Make learning your own">'+personal+'<p class="sbw4-personal-note">Personal goals and saved public apps are stored in this browser. Official school progress and access always come from your StudyBuddy account.</p></section>',1)
# All feature text, links, school sections, and Laravel loops are preserved.
insert='''@push('styles')
<!-- SB_WAVE4_ASSETS -->
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-w4-home.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-w4-home.css')) }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/js/studybuddy-wave4-home.js') }}?v={{ filemtime(public_path('assets/js/studybuddy-wave4-home.js')) }}" defer></script>
@endpush
'''
if h.count("@section('content')")!=1:raise SystemExit('STOP: cannot safely locate Blade content directive.')
h=h.replace("@section('content')",insert+"@section('content')",1)
home.write_text(h);wave3.write_text(c)
print('W4 homepage story, stable role descriptions, personalized corner and accessible fallbacks added.')
print('W3 FlexCarousel uses gentle arch; all Laravel CMS content and routes preserved.')
