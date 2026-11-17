{{-- StudyBuddy Wave 10 — Base44 visual direction, original Laravel implementation. Real CMS apps; no fake account metrics. --}}
@php
  $sbxApps = collect($discoveryApps ?? $featuredApps ?? $sbCarouselItems ?? [])->take(12);
  $sbxArt = 'assets/studybuddy-brand/pages/';
  $sbxImage = static function (?string $path): ?string {
      if (blank($path)) return null;
      return preg_match('/^https?:\/\//i', $path) ? $path : asset(ltrim($path, '/'));
  };
  $sbxHeroTitle = trim((string) ($settings['sbx_hero_title'] ?? 'Learning that feels like an adventure.'));
  $sbxHeroDescription = trim((string) ($settings['sbx_hero_description'] ?? 'Illustrated worlds, playful learning apps, and a progress journey that belongs to the learner. StudyBuddy turns curiosity into momentum — at home, in class, or anywhere in between.'));
  $sbxRoleCards = [
    ['Student','Every little discovery starts somewhere.','path-apps.webp','/roles','#bdd8ff','Discover'],
    ['Parent','Learning feels better together.','path-parents.webp','/roles','#f4cbea','Encourage'],
    ['Teacher','A thoughtful place to inspire curiosity.','path-teachers.webp','/roles','#b8ede6','Inspire'],
    ['Independent','Go at your own pace, in your own way.','path-support.webp','/roles','#f6dfaa','Explore']
  ];
@endphp
<div class="sbx" data-sbx-home>
  <div class="sbx-progress" aria-hidden="true"><span data-sbx-progress></span></div>
  <section class="sbx-hero" aria-labelledby="sbx-headline" id="sbx-start">
    <div class="sbx-star-fallback" aria-hidden="true"></div>
    {{-- Existing original React Bits components mounted via the site's existing W8 bridge (when present). --}}
    <div class="sbx-effect sbx-effect--galaxy" data-sb-react="SBW8Galaxy" data-sb-react-props='{"density":0.75,"glowIntensity":0.16,"hueShift":18,"twinkleIntensity":0.35}' aria-hidden="true"></div>
    <div class="sbx-effect sbx-effect--antigravity" data-sb-react="SBW8Antigravity" data-sb-react-props='{"colors":["#9779FF","#D9A4FA","#60DFF2"],"count":180,"particleSize":0.6,"autoAnimate":true}' aria-hidden="true"></div>
    <div class="sbx-wrap sbx-hero__grid">
      <div class="sbx-hero__copy">
        <p class="sbx-eyebrow"><span class="sbx-glowdot" aria-hidden="true"></span> LEARN · PLAY · GROW</p>
        <h1 id="sbx-headline">{{ $sbxHeroTitle === 'Learning that feels like an adventure.' ? 'Learning that feels' : $sbxHeroTitle }} @if($sbxHeroTitle === 'Learning that feels like an adventure.')<em>like an adventure.</em>@endif</h1>
        <p class="sbx-lead">{{ $sbxHeroDescription }}</p>
        <div class="sbx-actions">
          <a class="sbx-button sbx-button--primary" href="{{ url('/apps') }}">Explore learning apps <span aria-hidden="true">↗</span></a>
          <a class="sbx-button sbx-button--ghost" href="#sbx-how">See how it works <span aria-hidden="true">↘</span></a>
        </div>
        <form class="sbx-search" action="{{ url('/apps') }}" method="GET" role="search">
          <label class="sbx-sr" for="sbx-search-input">Search learning apps</label>
          <span aria-hidden="true">⌕</span>
          <input id="sbx-search-input" name="q" placeholder="Discover an app, topic or subject..." minlength="1" maxlength="80" type="search">
          <button type="submit" aria-label="Search app catalogue">Search ↗</button>
        </form>
        <p class="sbx-proof">A real app catalogue · Four learning perspectives · One connected StudyBuddy</p>
        <a class="sbx-scrollhint" href="#sbx-worlds">SCROLL TO EXPLORE <span aria-hidden="true">↓</span></a>
      </div>
      <div class="sbx-hero__visual" aria-label="StudyBuddy dolphin mascot rising from a magical book">
        <div class="sbx-orbit sbx-orbit--one" aria-hidden="true"></div><div class="sbx-orbit sbx-orbit--two" aria-hidden="true"></div>
        <div class="sbx-hero__halo" aria-hidden="true"></div>
        <img class="sbx-hero__dolphin" src="{{ asset($sbxArt.'hero-dolphin-book.webp') }}" width="850" height="850" fetchpriority="high" alt="A cheerful blue StudyBuddy dolphin leaping from an open magical book">
        <a href="{{ url('/learning-hub') }}" class="sbx-float sbx-float--lower"><small>YOUR NEXT ADVENTURE</small><strong>Find a world to explore ↗</strong></a>
        <span class="sbx-float sbx-float--upper"><small>YOUR JOURNEY</small><strong>Starts with curiosity ✦</strong></span>
      </div>
    </div>
  </section>

  <section class="sbx-worlds" id="sbx-worlds" aria-labelledby="sbx-worlds-title">
    <div class="sbx-wrap">
      <div class="sbx-heading"><div><p class="sbx-eyebrow">01 / THE DISCOVERY COLLECTION</p><h2 id="sbx-worlds-title">Pick a world. <em>Find your wonder.</em></h2></div><p>Actual apps from our StudyBuddy catalogue, beautifully presented. Only published, available experiences appear as playable.</p></div>
      <div class="sbx-reel-toolbar">
        <div class="sbx-pills" role="group" aria-label="Filter featured apps">
          <button class="is-selected" type="button" data-sbx-category="all" aria-pressed="true">All</button>
          @foreach($sbxApps->pluck('category')->filter()->unique()->take(3) as $sbxCategory)
            <button type="button" data-sbx-category="{{ \Illuminate\Support\Str::slug($sbxCategory) }}" aria-pressed="false">{{ $sbxCategory }}</button>
          @endforeach
        </div>
        <div class="sbx-reel-controls"><button type="button" data-sbx-previous aria-label="Previous learning app">←</button><button type="button" data-sbx-next aria-label="Next learning app">→</button></div>
      </div>
      <div class="sbx-reel" data-sbx-reel tabindex="0" role="region" aria-label="Featured learning apps, scroll sideways to explore">
        @forelse($sbxApps as $sbxIndex => $sbxApp)
          @php
            $sbxCover = method_exists($sbxApp,'safeHeroImage') ? $sbxApp->safeHeroImage() : null;
            $sbxCover = $sbxCover ?: (method_exists($sbxApp,'cardImage') ? $sbxApp->cardImage() : null);
            $sbxCoverUrl = $sbxImage($sbxCover) ?: asset($sbxArt.(['path-apps.webp','path-parents.webp','path-teachers.webp','path-support.webp'][$sbxIndex % 4]));
            $sbxStatus = method_exists($sbxApp,'availabilityLabel') ? $sbxApp->availabilityLabel() : 'View details';
          @endphp
          <a class="sbx-app" data-sbx-app data-sbx-app-category="{{ \Illuminate\Support\Str::slug((string) $sbxApp->category) }}" href="{{ route('studybuddy.apps.show', $sbxApp->slug) }}">
            <div class="sbx-app__visual"><img src="{{ $sbxCoverUrl }}" alt="" loading="lazy" decoding="async"><span class="sbx-app__number">{{ str_pad((string)($sbxIndex+1),2,'0',STR_PAD_LEFT) }}</span><span class="sbx-app__arrow" aria-hidden="true">↗</span></div>
            <span class="sbx-app__category">{{ $sbxApp->category ?: 'Explore' }} · {{ $sbxStatus }}</span><strong>{{ $sbxApp->name }}</strong><span class="sbx-app__desc">{{ $sbxApp->tagline ?: 'Open the experience to learn more.' }}</span>
          </a>
        @empty
          <div class="sbx-empty"><h3>Our next adventures are coming together.</h3><p>Once apps are published in the StudyBuddy CMS, they appear here automatically.</p><a href="{{ url('/apps') }}">Visit the app library ↗</a></div>
        @endforelse
      </div>
      <div class="sbx-reel-foot"><span>DRAG OR USE THE ARROWS TO EXPLORE</span><a href="{{ url('/apps') }}">Browse the full app library ↗</a></div>
    </div>
  </section>

  <section class="sbx-story" id="sbx-how" aria-labelledby="sbx-story-title"><div class="sbx-wrap sbx-story__grid">
    <div class="sbx-story__art"><div class="sbx-story__artframe" data-sbx-artframe>
      <img src="{{ asset($sbxArt.'path-apps.webp') }}" alt="Learning apps and ideas" class="is-current" data-sbx-story-image="0" loading="lazy">
      <img src="{{ asset($sbxArt.'path-parents.webp') }}" alt="Learning with family" data-sbx-story-image="1" loading="lazy">
      <img src="{{ asset($sbxArt.'path-teachers.webp') }}" alt="Tools for educators" data-sbx-story-image="2" loading="lazy">
      <span class="sbx-story__caption">YOUR STORY, YOUR WAY</span>
    </div></div>
    <div class="sbx-story__content"><p class="sbx-eyebrow">02 / LITTLE MOMENTS. BIG DISCOVERIES.</p><h2 id="sbx-story-title">Learning feels better <em>when it's yours.</em></h2>
      <article class="sbx-story-step is-current" data-sbx-story-step="0"><span>01 — DISCOVER</span><h3>Find what sparks your curiosity.</h3><p>Explore real games, subjects and learning experiences, from the first little question to a brand-new interest.</p><a href="{{ url('/apps') }}">Explore apps ↗</a></article>
      <article class="sbx-story-step" data-sbx-story-step="1"><span>02 — MAKE IT PERSONAL</span><h3>Collect what matters to you.</h3><p>Save activities and build your own learning goals using My Quest after signing in.</p><a href="{{ url('/my-quest') }}">Open My Quest ↗</a></article>
      <article class="sbx-story-step" data-sbx-story-step="2"><span>03 — KEEP GROWING</span><h3>Celebrate the little wins.</h3><p>Progress and points come from real account activity. No invented scores or school records.</p><a href="{{ url('/dashboard') }}">My dashboard ↗</a></article>
    </div>
  </div></section>

  <section class="sbx-roles" id="sbx-roles" aria-labelledby="sbx-roles-title"><div class="sbx-wrap">
    <div class="sbx-heading sbx-heading--center"><div><p class="sbx-eyebrow">03 / EVERYONE HAS A PLACE</p><h2 id="sbx-roles-title">Your journey. <em>Your kind of wonder.</em></h2></div><p>Different perspectives for learners, families, educators and independent minds.</p></div>
    <div class="sbx-role-gallery">
      @foreach($sbxRoleCards as $index => $roleCard)
      <a class="sbx-role" href="{{ url($roleCard[3]) }}" style="--role-tint: {{ $roleCard[4] }}">
        <span class="sbx-role__top"><span>{{ str_pad((string)($index+1),2,'0',STR_PAD_LEFT) }}</span><span aria-hidden="true">↗</span></span>
        <img src="{{ asset($sbxArt.$roleCard[2]) }}" alt="" loading="lazy" decoding="async">
        <span class="sbx-role__info"><small>{{ $roleCard[5] }}</small><strong>{{ $roleCard[0] }}</strong><span>{{ $roleCard[1] }}</span></span>
      </a>
      @endforeach
    </div>
  </div></section>

  <section class="sbx-cta" aria-labelledby="sbx-cta-title"><div class="sbx-wrap sbx-cta__grid">
    <div><p class="sbx-eyebrow">04 / STUDYBUDDY FOR EVERYONE</p><h2 id="sbx-cta-title">Every adventure <em>starts somewhere.</em></h2><p>Explore an app, create your own goals or discover how StudyBuddy can support learning in schools. Start with one small useful action.</p>
      <div class="sbx-actions"><a class="sbx-button sbx-button--primary" href="{{ url('/apps') }}">Choose an app ↗</a><a class="sbx-button sbx-button--ghost" href="{{ url('/for-schools') }}">For schools ↗</a></div>
    </div><div class="sbx-cta__visual"><img src="{{ asset($sbxArt.'hero-dolphin-book.webp') }}" alt="StudyBuddy dolphin mascot" loading="lazy"></div>
  </div></section>
</div>
