{{-- Optional world navigator. This does not replace the existing, CMS-controlled navigation. --}}
<div class="sb-u-toolbar" data-sb-world-toolbar>
  <button type="button" class="sb-u-toolbar__open" data-sb-world-open aria-expanded="false" aria-controls="sb-u-world-panel" aria-label="Open StudyBuddy world navigator"><span class="sb-u-toolbar__glyph" aria-hidden="true">✦</span><span>Worlds</span></button>
  <button type="button" class="sb-u-toolbar__motion" data-sb-motion-toggle aria-pressed="true" aria-label="Turn off Universe visual effects" title="Turn off Universe visual effects">✧ <span data-sb-motion-label>Effects on</span></button>
</div>
<div class="sb-u-world-panel" id="sb-u-world-panel" data-sb-world-panel hidden>
  <div class="sb-u-world-panel__heading"><div><small>STUDYBUDDY UNIVERSE</small><strong>Where to next?</strong></div><button type="button" data-sb-world-close aria-label="Close world navigator">✕</button></div>
  <div class="sb-u-world-panel__grid">
    <a href="{{ url('/') }}"><img src="{{ asset('assets/studybuddy-universe/realm-portal.webp') }}" alt="" loading="lazy"><span><b>Home universe</b><small>Start your journey</small></span></a>
    <a href="{{ url('/apps') }}"><img src="{{ asset('assets/studybuddy-universe/realm-maths.webp') }}" alt="" loading="lazy"><span><b>Learning worlds</b><small>Explore real &amp; demo apps</small></span></a>
    <a href="{{ url('/for-schools') }}"><img src="{{ asset('assets/studybuddy-universe/school-campus.webp') }}" alt="" loading="lazy"><span><b>For schools</b><small>See the school pilot</small></span></a>
    @auth
    <a href="{{ url('/dashboard') }}"><img src="{{ asset('assets/studybuddy-universe/realm-science.webp') }}" alt="" loading="lazy"><span><b>My space</b><small>Your existing dashboard</small></span></a>
    @else
    <a href="{{ url('/login') }}"><img src="{{ asset('assets/studybuddy-universe/realm-reading.webp') }}" alt="" loading="lazy"><span><b>Sign in</b><small>Continue your journey</small></span></a>
    @endauth
  </div>
  <p>Keep exploring at your own pace <span aria-hidden="true">✦</span></p>
</div>
