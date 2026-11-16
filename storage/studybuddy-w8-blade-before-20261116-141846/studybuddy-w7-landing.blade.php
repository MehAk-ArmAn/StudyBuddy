{{-- StudyBuddy W7 | Original image-led landing inspired by cinematic gallery design.
     Laravel remains the source of real content, routes and authorization. --}}
<div class="sbw7" id="sbw7-page" data-sbw7-page>
  <!-- SB_WAVE8_GALAXY: subtle purple atmosphere behind ALL homepage sections -->
  <div class="sbw8-galaxy-layer" aria-hidden="true"><div data-sb-react="SBW8Galaxy" class="sbw8-galaxy-slot"></div><div class="sbw8-galaxy-tone"></div></div>
  <a class="sbw7-skip" href="#sbw7-worlds">Skip to learning worlds</a>
  <section class="sbw7-hero" aria-labelledby="sbw7-title" id="welcome">
    <div class="sbw7-hero__ambient" aria-hidden="true"></div>
    <!-- SB_WAVE8_ANTIGRAVITY: original React Bits JS/CSS source -->
    <div class="sbw8-hero-layer" aria-hidden="true"><div data-sb-react="SBW8Antigravity" class="sbw8-hero-slot"></div></div>
    <div class="sbw7-shell sbw7-hero__inner">
      <div class="sbw7-hero__logo" data-sb-react="SBLogoSequence" data-sb-react-props='@json(["logo" => asset("assets/studybuddy-brand/studybuddy-logo.png")])'>
        <span class="sbw7-hero__logo-static"><img src="{{ asset('assets/studybuddy-brand/studybuddy-logo.png') }}" alt="" width="42" height="42"><strong>StudyBuddy</strong></span>
      </div>
      <p class="sbw7-overline"><span class="sbw7-pulse" aria-hidden="true"></span>{{ $hero?->eyebrow ?: 'A NEW WAY TO LEARN' }} <span aria-hidden="true">✦</span></p>
      <h1 id="sbw7-title" class="sbw7-hero__heading">{{ $hero?->title ?: 'Learn. Play. Grow. Your Way.' }}</h1>
      <p class="sbw7-hero__subtitle">{{ $hero?->subtitle ?: 'A more imaginative place to discover, practise and grow.' }}</p>
      <div class="sbw7-hero__actions">
        <a class="sbw7-button sbw7-button--primary" href="{{ $hero?->button_url ?: url('/apps') }}">{{ $hero?->button_label ?: 'Explore learning worlds' }} <span aria-hidden="true">↗</span></a>
        <a class="sbw7-button sbw7-button--secondary" href="{{ route('schools.info') }}">For schools <span aria-hidden="true">↗</span></a>
      </div>
      <div class="sbw7-hero__gallery" data-sbw7-hero-scene aria-label="StudyBuddy learning artwork collection">
        <div class="sbw7-hero__light" aria-hidden="true"></div>
        <figure class="sbw7-hero__orbit sbw7-hero__orbit--one"><img src="{{ asset('assets/studybuddy-w6/art/path-apps.webp') }}" alt="" width="480" height="480" decoding="async"><figcaption>DISCOVER</figcaption></figure>
        <figure class="sbw7-hero__orbit sbw7-hero__orbit--two"><img src="{{ asset('assets/studybuddy-w6/art/path-teachers.webp') }}" alt="" width="480" height="480" decoding="async"><figcaption>CREATE</figcaption></figure>
        <figure class="sbw7-hero__orbit sbw7-hero__orbit--three"><img src="{{ asset('assets/studybuddy-w6/art/path-parents.webp') }}" alt="" width="480" height="480" decoding="async"><figcaption>GROW</figcaption></figure>
        <figure class="sbw7-hero__orbit sbw7-hero__orbit--four"><img src="{{ asset('assets/studybuddy-w6/art/path-support.webp') }}" alt="" width="480" height="480" decoding="async"><figcaption>EXPLORE</figcaption></figure>
        <img class="sbw7-hero__dolphin" src="{{ $heroImage }}" alt="StudyBuddy's dolphin leaps from a magical open book" width="860" height="860" decoding="async" fetchpriority="high">
      </div>
      <div class="sbw7-hero__footer"><span>YOUR NEXT ADVENTURE IS WAITING</span><a href="#sbw7-worlds">SCROLL TO DISCOVER <span aria-hidden="true">↓</span></a></div>
    </div>
  </section>

  <section class="sbw7-worlds" id="sbw7-worlds" aria-labelledby="sbw7-worlds-title">
    <div class="sbw7-shell">
      <div class="sbw7-heading sbw7-inview">
        <p class="sbw7-overline">01 / THE WORLD IS YOUR CLASSROOM</p>
        <h2 id="sbw7-worlds-title">Find your next <em>wonder.</em></h2>
        <p>{{ $appsSection?->subtitle ?: 'Playful worlds, thoughtful practice, and activities that make you want to keep exploring.' }}</p>
      </div>
      @if($featured->isNotEmpty())
        <div class="sbw7-reel" data-sbw7-reel tabindex="0" role="region" aria-label="Featured learning worlds, scroll horizontally or use arrow buttons">
          <div class="sbw7-reel__track" data-sbw7-track>
            @foreach($featured->take(7) as $index => $app)
            @php
              $isDemo = str_starts_with((string)$app->slug, 'demo-next-');
              $cover = $app->safeHeroImage();
              $fallbacks = ['path-apps.webp','path-teachers.webp','path-parents.webp','path-support.webp'];
              $fallback = asset('assets/studybuddy-w6/art/'.$fallbacks[$index % 4]);
              $art = $cover ? $imageUrl($cover) : $fallback;
            @endphp
              <a class="sbw7-world" href="{{ route('studybuddy.apps.show', $app->slug) }}" aria-label="Explore {{ $app->name }}{{ $isDemo ? ', concept preview' : '' }}">
                <span class="sbw7-world__number">WORLD {{ str_pad((string)($index+1),2,'0',STR_PAD_LEFT) }}</span>
                <img src="{{ $art }}" alt="" loading="lazy" decoding="async">
                <span class="sbw7-world__bottom"><span><small>{{ $isDemo ? 'CONCEPT PREVIEW · NOT PLAYABLE' : 'EXPLORE THE WORLD' }}</small><strong>{{ $app->name }}</strong>@if($app->tagline)<span>{{ $app->tagline }}</span>@endif</span><span aria-hidden="true" class="sbw7-world__arrow">↗</span></span>
              </a>
            @endforeach
          </div>
        </div>
        <div class="sbw7-worlds__foot"><span>DRAG OR SWIPE THROUGH THE WORLDS</span><div class="sbw7-reel__arrows"><button type="button" data-sbw7-prev aria-label="Previous learning world">←</button><button type="button" data-sbw7-next aria-label="Next learning world">→</button></div><a href="{{ url('/apps') }}">Explore all apps ↗</a></div>
      @else
        <div class="sbw7-empty"><p>Our learning worlds are being prepared.</p><a href="{{ url('/apps') }}">See all available apps ↗</a></div>
      @endif
    </div>
  </section>

  <section class="sbw7-story" id="sbw7-story" aria-labelledby="sbw7-story-title">
    <div class="sbw7-shell sbw7-story__layout">
      <div class="sbw7-story__visual" data-sbw7-stage aria-hidden="true">
        <div class="sbw7-story__halo"></div>
        <img class="sbw7-story__picture is-active" src="{{ asset('assets/studybuddy-w6/art/path-apps.webp') }}" alt="" loading="lazy" data-sbw7-story-image="0">
        <img class="sbw7-story__picture" src="{{ asset('assets/studybuddy-w6/art/path-parents.webp') }}" alt="" loading="lazy" data-sbw7-story-image="1">
        <img class="sbw7-story__picture" src="{{ asset('assets/studybuddy-w6/art/path-teachers.webp') }}" alt="" loading="lazy" data-sbw7-story-image="2">
        <div class="sbw7-story__caption"><span data-sbw7-caption-number>01 / 03</span><span data-sbw7-caption>BE CURIOUS</span></div>
      </div>
      <div class="sbw7-story__content">
        <div class="sbw7-story__eyebrow"><p class="sbw7-overline">02 / DESIGNED AROUND YOU</p><h2 id="sbw7-story-title">More than a website.<br><em>Your way to wonder.</em></h2></div>
        <article class="sbw7-story__step" data-sbw7-story-step="0"><span class="sbw7-step-count">01</span><h3>Every adventure starts with curiosity.</h3><p>Start with a subject, discover an activity, and follow what interests you. Your next big idea might begin with one tiny step.</p><a href="{{ url('/apps') }}">Find your first world ↗</a></article>
        <article class="sbw7-story__step" data-sbw7-story-step="1"><span class="sbw7-step-count">02</span><h3>Learning feels better when it fits you.</h3><p>Find experiences for students, parents, teachers and independent learners. Explore the pathways that match your needs.</p><a href="{{ url('/roles') }}">See learning roles ↗</a></article>
        <article class="sbw7-story__step" data-sbw7-story-step="2"><span class="sbw7-step-count">03</span><h3>Little wins add up to something wonderful.</h3><p>Explore personal tools and supported classroom features, with progress and school information shown only when it really exists.</p><a href="{{ route('schools.info') }}">Discover school connections ↗</a></article>
      </div>
    </div>
  </section>

  <section class="sbw7-paths" id="sbw7-paths" aria-labelledby="sbw7-paths-title">
    <div class="sbw7-shell">
      <div class="sbw7-heading sbw7-heading--center sbw7-inview"><p class="sbw7-overline">03 / EVERYONE BELONGS</p><h2 id="sbw7-paths-title">Different minds.<br><em>One beautiful journey.</em></h2><p>Choose a perspective and discover the StudyBuddy experience that makes sense for you.</p></div>
      <!-- SB_WAVE8_ROLES: actual React Bits AccordionGallery; existing Blade gallery is a fallback -->
      <div class="sbw8-role-host" data-sbw8-role-host data-sb-react="SBW8RoleGallery" data-sb-react-props='@json(["items" => [
        ["image" => asset("assets/studybuddy-w6/art/path-apps.webp"), "alt" => "StudyBuddy illustrated learning adventures", "label" => "For students", "link" => url("/roles")],
        ["image" => asset("assets/studybuddy-w6/art/path-parents.webp"), "alt" => "StudyBuddy family learning", "label" => "For families", "link" => url("/roles")],
        ["image" => asset("assets/studybuddy-w6/art/path-teachers.webp"), "alt" => "StudyBuddy educator learning resources", "label" => "For educators", "link" => url("/roles")],
        ["image" => asset("assets/studybuddy-w6/art/path-support.webp"), "alt" => "StudyBuddy independent explorer", "label" => "Independent", "link" => url("/roles")]
      ]])'></div>
      <div class="sbw7-paths__gallery" aria-label="Learning journeys">
        @foreach([
          ['art'=>'path-apps.webp','index'=>'01','title'=>'Students','caption'=>'Chase curiosity','description'=>'Find apps, activities and personal ways to learn.'],
          ['art'=>'path-parents.webp','index'=>'02','title'=>'Families','caption'=>'Grow together','description'=>'Discover family tools and helpful learning connections.'],
          ['art'=>'path-teachers.webp','index'=>'03','title'=>'Educators','caption'=>'Inspire discovery','description'=>'Explore approved teaching and classroom resources.'],
          ['art'=>'path-support.webp','index'=>'04','title'=>'Independent','caption'=>'Go your way','description'=>'Choose the pace and interests that fit you.']
        ] as $role)
        <a href="{{ url('/roles') }}" class="sbw7-path" aria-label="Learn about {{ $role['title'] }}">
          <img src="{{ asset('assets/studybuddy-w6/art/'.$role['art']) }}" alt="" loading="lazy" decoding="async">
          <span class="sbw7-path__top">{{ $role['index'] }} / 04 <span aria-hidden="true">↗</span></span>
          <span class="sbw7-path__bottom"><small>{{ $role['caption'] }}</small><strong>{{ $role['title'] }}</strong><span>{{ $role['description'] }}</span></span>
        </a>
        @endforeach
      </div>
      <a class="sbw7-underlink" href="{{ url('/roles') }}">Compare all learning roles ↗</a>
    </div>
  </section>

  <section class="sbw7-feature" id="sbw7-features" aria-labelledby="sbw7-feature-title">
    <div class="sbw7-shell sbw7-feature__split">
      <div class="sbw7-feature__art sbw7-inview"><div class="sbw7-feature__glow"></div>
      <!-- SB_WAVE8_BOUNCE: real React Bits BounceCards with StudyBuddy artwork -->
      <div class="sbw8-bounce-host" data-sbw8-bounce-host data-sb-react="SBW8FeatureBounce" data-sb-react-props='@json(["images" => [
        ["src" => asset("assets/studybuddy-w6/art/path-apps.webp"),"alt"=>"Discover learning"],
        ["src" => asset("assets/studybuddy-w6/art/path-parents.webp"),"alt"=>"Families learning together"],
        ["src" => asset("assets/studybuddy-w6/art/path-teachers.webp"),"alt"=>"Teaching and creativity"],
        ["src" => asset("assets/studybuddy-w6/art/path-support.webp"),"alt"=>"Safe learning community"]
      ]])'></div><img src="{{ asset('assets/studybuddy-w6/art/path-support.webp') }}" alt="StudyBuddy illustrated safe learning universe" width="620" height="620" loading="lazy"></div>
      <div class="sbw7-feature__copy sbw7-inview">
        <p class="sbw7-overline">04 / THE LITTLE THINGS MATTER</p><h2 id="sbw7-feature-title">Made to feel <em>personal.</em></h2>
        <p class="sbw7-feature__lead">Not another page of tiny boxes. Discover thoughtfully arranged information, useful activities and experiences that respect your pace.</p>
        @if($what && $what->items->isNotEmpty())
          <div class="sbw7-feature__points">
          @foreach($what->items->take(3) as $index => $item)
            <div class="sbw7-feature__point"><span>{{ str_pad((string)($index+1),2,'0',STR_PAD_LEFT) }}</span><div><h3>{{ $item->title }}</h3>@if($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if($item->button_label)<a href="{{ $item->button_url ?: url('/apps') }}">{{ $item->button_label }} ↗</a>@endif</div></div>
          @endforeach
          </div>
        @else
          <div class="sbw7-feature__points"><div class="sbw7-feature__point"><span>01</span><div><h3>Explore thoughtfully.</h3><p>Choose what sparks your curiosity.</p></div></div><div class="sbw7-feature__point"><span>02</span><div><h3>Learn your way.</h3><p>Take the next step at your own pace.</p></div></div></div>
        @endif
      </div>
    </div>
  </section>

  <section class="sbw7-personal" id="sbw7-personal" aria-labelledby="sbw7-personal-title">
    <div class="sbw7-shell sbw7-personal__layout">
      <div class="sbw7-personal__intro sbw7-inview"><p class="sbw7-overline">05 / A SPACE OF YOUR OWN</p><h2 id="sbw7-personal-title">Keep the things that <em>spark your curiosity.</em></h2><p>Collect public worlds you like and choose little goals that fit your pace. Everything here is yours to explore.</p><img src="{{ asset('assets/studybuddy-w6/art/path-parents.webp') }}" alt="" loading="lazy" width="360" height="360"></div>
      <div class="sbw7-personal__widget sbw7-inview"><div data-sb-react="SBPersonalCorner" data-sb-react-props='{"context":"public"}'><p>Your personal learning corner is loading. <a href="{{ url('/apps') }}">Explore apps ↗</a></p></div><small>Public saved apps and simple choices stay in this browser. Official progress and school records stay inside your authorized StudyBuddy account.</small></div>
    </div>
  </section>

  <section class="sbw7-school" id="sbw7-schools" aria-labelledby="sbw7-school-title">
    <div class="sbw7-shell sbw7-school__inner">
      <div class="sbw7-school__copy sbw7-inview"><p class="sbw7-overline">06 / LEARNING TOGETHER</p><h2 id="sbw7-school-title">The brightest ideas<br><em>grow together.</em></h2><p>StudyBuddy is growing into a connected learning space for students, families and educators. Schools can request reviewed workspaces with access controls designed for their community.</p><a class="sbw7-button sbw7-button--primary" href="{{ route('schools.info') }}">Discover StudyBuddy for Schools ↗</a><small>School access requires approval and verification.</small></div>
      <div class="sbw7-school__art sbw7-inview"><img src="{{ asset('assets/studybuddy-w6/art/path-teachers.webp') }}" alt="Colorful learning tools surrounding a teaching board" loading="lazy" width="680" height="680"></div>
    </div>
  </section>

  @if($pagesSection || $whySection || $trustSection)
  <section class="sbw7-details" id="sbw7-details" aria-label="More about StudyBuddy">
    <div class="sbw7-shell"><p class="sbw7-overline">07 / THE WHOLE PICTURE</p><h2>The details matter <em>too.</em></h2>
      @foreach([['section'=>$pagesSection,'label'=>'Find your learning path'],['section'=>$whySection,'label'=>'Why StudyBuddy?'],['section'=>$trustSection,'label'=>'Our approach to trust']] as $group)
        @if($group['section'])<details class="sbw7-details__item"><summary>{{ $group['section']->title ?: $group['label'] }} <span aria-hidden="true">+</span></summary><div class="sbw7-details__body">@if($group['section']->subtitle)<p>{{ $group['section']->subtitle }}</p>@endif @foreach($group['section']->items as $item)<article><h3>{{ $item->title }}</h3>@if($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if($item->button_label)<a href="{{ $item->button_url ?: url('/apps') }}">{{ $item->button_label }} ↗</a>@endif</article>@endforeach</div></details>@endif
      @endforeach
    </div>
  </section>
  @endif

  <section class="sbw7-end" id="sbw7-get-started" aria-labelledby="sbw7-end-title"><div class="sbw7-shell sbw7-end__inner"><img src="{{ asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp') }}" alt="" loading="lazy" width="400" height="400"><p class="sbw7-overline">YOUR NEXT LITTLE ADVENTURE</p><h2 id="sbw7-end-title">{{ $cta?->title ?: 'Ready to find your next adventure?' }}</h2><p>{{ $cta?->subtitle ?: 'A world of curious possibilities is waiting.' }}</p><a class="sbw7-button sbw7-button--primary" href="{{ $cta?->button_url ?: url('/apps') }}">{{ $cta?->button_label ?: 'Explore learning worlds' }} ↗</a></div></section>
</div>
