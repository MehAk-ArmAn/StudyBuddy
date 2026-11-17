{{-- Actual StudyBuddy Laravel landing source. The existing Laravel layout supplies navigation, theme controls and footer. --}}
@php
    $iwHero = $hero ?? null;
    $iwCta = $cta ?? null;
    $iwFeatured = collect($featured ?? [])->take(7);
    $iwArt = 'assets/studybuddy-imageworks/';
@endphp
<main class="sb-imageworks" id="iw-main" data-imageworks-home>
  <a class="iw-skip" href="#iw-discover">Skip to learning worlds</a>
  <section class="iw-hero" aria-labelledby="iw-hero-title">
    <div class="iw-effect iw-effect--galaxy" data-sb-react="SBW8Galaxy" data-sb-react-props='{"density":0.75,"glowIntensity":0.16,"hueShift":18,"twinkleIntensity":0.35}' aria-hidden="true"></div>
    <div class="iw-effect iw-effect--antigravity" data-sb-react="SBW8Antigravity" data-sb-react-props='{"colors":["#9A76FF","#FFAAEC","#66DAF9"],"count":700,"particleSize":0.7,"autoAnimate":true}' aria-hidden="true"></div>
    <div class="iw-hero__texture" aria-hidden="true"></div>
    <div class="iw-shell iw-hero__content">
      <p class="iw-kicker"><span class="iw-dot" aria-hidden="true"></span> WELCOME TO THE STUDYBUDDY UNIVERSE</p>
      <h1 id="iw-hero-title">{{ $iwHero?->title ?: 'Learn. Play. Grow. Your Way.' }}</h1>
      <p class="iw-hero__description">{{ $iwHero?->subtitle ?: 'Playful little discoveries, real learning and a whole world to explore.' }}</p>
      <div class="iw-actions"><a class="iw-button iw-button--light" href="{{ $iwHero?->button_url ?: url('/apps') }}">{{ $iwHero?->button_label ?: 'Explore learning worlds' }} <span aria-hidden="true">↗</span></a><a class="iw-button iw-button--outline" href="{{ url('/for-schools') }}">For schools <span aria-hidden="true">↗</span></a></div>
    </div>
    <div class="iw-hero__stage iw-shell" aria-label="StudyBuddy learning worlds illustrated with magical objects">
      <div class="iw-stage-glow" aria-hidden="true"></div>
      <div class="iw-stage-card iw-stage-card--a"><img src="{{ asset($iwArt.'path-apps.webp') }}" alt="" width="480" height="480" decoding="async"><span>DISCOVER</span></div>
      <div class="iw-stage-card iw-stage-card--b"><img src="{{ asset($iwArt.'path-teachers.webp') }}" alt="" width="480" height="480" decoding="async"><span>CREATE</span></div>
      <div class="iw-stage-card iw-stage-card--c"><img src="{{ asset($iwArt.'path-parents.webp') }}" alt="" width="480" height="480" decoding="async"><span>GROW</span></div>
      <div class="iw-stage-card iw-stage-card--d"><img src="{{ asset($iwArt.'path-support.webp') }}" alt="" width="480" height="480" decoding="async"><span>EXPLORE</span></div>
      <img class="iw-stage-mascot" src="{{ asset($iwArt.'hero-dolphin-book.webp') }}" alt="StudyBuddy dolphin rising from an enchanted learning book" width="860" height="860" fetchpriority="high" decoding="async">
    </div>
    <div class="iw-shell iw-hero__foot"><span>BIG IDEAS. LITTLE ADVENTURES.</span><a href="#iw-discover">SCROLL TO EXPLORE ↓</a></div>
  </section>

  <section class="iw-discover iw-section" id="iw-discover" aria-labelledby="iw-discover-title">
    <div class="iw-shell">
      <div class="iw-section-head"><div><p class="iw-kicker">01 / THE DISCOVERY COLLECTION</p><h2 id="iw-discover-title">Find a world. <em>Follow the wonder.</em></h2></div><p>Choose an adventure that fits you. Every world leads to more than just a pretty picture.</p></div>
      <div class="iw-gallery" data-iw-gallery tabindex="0" role="region" aria-label="Learning world gallery: scroll horizontally or use the arrow controls">
        @forelse($iwFeatured as $index => $iwApp)
          @php
            $iwSlug = (string)($iwApp->slug ?? '');
            $iwDemo = str_starts_with($iwSlug,'demo-next-');
            $iwCover = method_exists($iwApp,'safeHeroImage') ? $iwApp->safeHeroImage() : null;
            $iwImage = $iwCover ? (preg_match('/^https?:\/\//i',$iwCover) ? $iwCover : asset($iwCover)) : asset($iwArt.(['path-apps.webp','path-parents.webp','path-teachers.webp','path-support.webp'][$index % 4]));
          @endphp
          <a class="iw-world" href="{{ url('/apps/'.$iwSlug) }}">
            <img src="{{ $iwImage }}" alt="" loading="lazy" decoding="async">
            <span class="iw-world__index">{{ str_pad((string)($index+1),2,'0',STR_PAD_LEFT) }} / 07</span>
            <span class="iw-world__caption"><small>{{ $iwDemo ? 'CONCEPT PREVIEW · NOT PLAYABLE' : 'LEARNING WORLD' }}</small><strong>{{ $iwApp->name }}</strong><span>{{ $iwApp->tagline ?: 'A little adventure awaits.' }}</span></span>
            <span class="iw-world__arrow" aria-hidden="true">↗</span>
          </a>
        @empty
          <div class="iw-world-empty"><p>New learning adventures are on their way.</p><a href="{{ url('/apps') }}">Explore all apps ↗</a></div>
        @endforelse
      </div>
      <div class="iw-gallery-bar"><span>DRAG TO DISCOVER THE COLLECTION</span><div class="iw-gallery-buttons"><button type="button" data-iw-prev aria-label="Previous learning world">←</button><button type="button" data-iw-next aria-label="Next learning world">→</button></div><a href="{{ url('/apps') }}">View all apps ↗</a></div>
    </div>
  </section>

  <section class="iw-story iw-section" id="iw-story" aria-labelledby="iw-story-title">
    <div class="iw-shell iw-story__layout">
      <div class="iw-story__art"><div class="iw-story__halo" aria-hidden="true"></div><img data-iw-image="0" class="is-active" src="{{ asset($iwArt.'path-apps.webp') }}" alt="" loading="lazy"><img data-iw-image="1" src="{{ asset($iwArt.'path-parents.webp') }}" alt="" loading="lazy"><img data-iw-image="2" src="{{ asset($iwArt.'path-teachers.webp') }}" alt="" loading="lazy"><span class="iw-story__counter" data-iw-counter>01 / 03</span></div>
      <div class="iw-story__copy">
        <p class="iw-kicker">02 / A JOURNEY THAT GROWS WITH YOU</p><h2 id="iw-story-title">The magic is in <em>the moments.</em></h2>
        <article class="iw-step is-active" data-iw-step="0"><span>01</span><h3>Explore what makes you curious.</h3><p>Discover learning activities and experiences that invite you to try something new.</p><a href="{{ url('/apps') }}">Find your first app ↗</a></article>
        <article class="iw-step" data-iw-step="1"><span>02</span><h3>Make learning your own.</h3><p>Different perspectives for students, parents, teachers, and independent explorers.</p><a href="{{ url('/roles') }}">Explore learning roles ↗</a></article>
        <article class="iw-step" data-iw-step="2"><span>03</span><h3>Celebrate the little wins.</h3><p>Keep improving at your own pace. Only real account activity is shown as official progress.</p><a href="{{ url('/for-schools') }}">Discover school learning ↗</a></article>
      </div>
    </div>
  </section>

  <section class="iw-roles iw-section" id="iw-roles" aria-labelledby="iw-roles-title"><div class="iw-shell">
    <div class="iw-section-head iw-section-head--center"><div><p class="iw-kicker">03 / FIND YOUR SPACE</p><h2 id="iw-roles-title">Everyone belongs <em>in this story.</em></h2></div><p>One platform, different ways to learn and grow.</p></div>
    <div class="iw-role-gallery">
      @foreach([['Students','Discover new adventures','path-apps.webp'],['Families','Grow together','path-parents.webp'],['Educators','Inspire curiosity','path-teachers.webp'],['Independent','Find your own rhythm','path-support.webp']] as $iwRole)
      <a href="{{ url('/roles') }}" class="iw-role"><img src="{{ asset($iwArt.$iwRole[2]) }}" alt="" loading="lazy"><span class="iw-role__copy"><small>{{ $iwRole[1] }}</small><strong>{{ $iwRole[0] }}</strong></span><span class="iw-role__arrow" aria-hidden="true">↗</span></a>
      @endforeach
    </div><a class="iw-text-link" href="{{ url('/roles') }}">Compare all learning roles ↗</a>
  </div></section>

  <section class="iw-schools iw-section" id="iw-schools" aria-labelledby="iw-schools-title"><div class="iw-shell iw-schools__layout">
    <div class="iw-schools__copy"><p class="iw-kicker">04 / LEARN TOGETHER</p><h2 id="iw-schools-title">Growing minds. <em>Brighter futures.</em></h2><p>StudyBuddy brings playful discovery and thoughtful support together. Schools can explore supervised learning spaces, educator tools, and features designed for their communities.</p><a class="iw-button iw-button--light" href="{{ url('/for-schools') }}">Explore for schools ↗</a><small>School access is subject to approval and verification.</small></div>
    <div class="iw-schools__art"><img src="{{ asset($iwArt.'path-teachers.webp') }}" alt="Illustration of a learning world for educators" loading="lazy"></div>
  </div></section>

  <section class="iw-ending iw-section" id="iw-end"><div class="iw-shell iw-ending__content"><img src="{{ asset($iwArt.'hero-dolphin-book.webp') }}" alt="" loading="lazy" width="380" height="380"><p class="iw-kicker">EVERY ADVENTURE STARTS SOMEWHERE</p><h2>{{ $iwCta?->title ?: 'Your next little adventure is waiting.' }}</h2><p>{{ $iwCta?->subtitle ?: 'Try an activity, follow your curiosity, and grow your way.' }}</p><a class="iw-button iw-button--light" href="{{ $iwCta?->button_url ?: url('/apps') }}">{{ $iwCta?->button_label ?: 'Explore learning apps' }} ↗</a></div></section>
</main>
