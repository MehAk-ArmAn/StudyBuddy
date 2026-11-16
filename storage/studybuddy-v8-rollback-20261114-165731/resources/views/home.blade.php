@extends('layouts.app')
@php
    $sectionMap = collect($sections ?? [])->keyBy('section_key');
    $hero = $sectionMap->get('hero');
    $what = $sectionMap->get('what_we_do');
    $appsSection = $sectionMap->get('apps_preview') ?? $sectionMap->get('apps');
    $pagesSection = $sectionMap->get('page_paths');
    $whySection = $sectionMap->get('why');
    $trustSection = $sectionMap->get('trust');
    $cta = $sectionMap->get('cta');
    $featured = collect($featuredApps ?? []);
    $imageUrl = static function (?string $path): ?string {
        if (blank($path)) return null;
        return preg_match('/^https?:\/\//i', $path) ? $path : asset(ltrim($path, '/'));
    };
    $heroImage = !blank($hero?->image_path)
        ? $imageUrl($hero->image_path)
        : asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp');
@endphp
@section('content')
<div class="sb7" data-sb7-home>
  <section class="sb7-hero" aria-labelledby="sb7-title" id="welcome">
    <div class="sb7-hero__mesh" aria-hidden="true"></div>
    <div class="sb7-hero__grain" aria-hidden="true"></div>
    <div class="sb7-wrap sb7-hero__grid">
      <div class="sb7-hero__content">
        <div class="sb7-eyebrow"><span class="sb7-live-dot" aria-hidden="true"></span> {{ $hero?->eyebrow ?: 'LEARN · PLAY · GROW' }} <span class="sb7-eyebrow__shine" aria-hidden="true">✦</span></div>
        <h1 class="sb7-hero__headline" id="sb7-title" data-sb7-reveal-title>{{ $hero?->title ?: 'Learn. Play. Grow. Your Way.' }}</h1>
        <p class="sb7-hero__intro">{{ $hero?->subtitle ?: 'A brighter way to explore, practise, and keep learning.' }}</p>
        @if(!blank($hero?->body))<p class="sb7-hero__subcopy">{{ $hero->body }}</p>@endif
        <div class="sb7-hero__actions">
          <a class="sb7-button sb7-button--primary" href="{{ $hero?->button_url ?: url('/apps') }}"><span>{{ $hero?->button_label ?: 'Explore learning apps' }}</span><span class="sb7-button__arrow" aria-hidden="true">↗</span></a>
          <a class="sb7-button sb7-button--quiet" href="{{ route('schools.info') }}">For schools <span aria-hidden="true">→</span></a>
        </div>
        <div class="sb7-hero__micro"><span><i aria-hidden="true">✳</i> Playful learning</span><span><i aria-hidden="true">✳</i> For every learner</span><span><i aria-hidden="true">✳</i> At your own pace</span></div>
      </div>
      <div class="sb7-hero__visual" data-sb7-hero-visual aria-label="StudyBuddy dolphin mascot emerging from a storybook">
        <div class="sb7-hero__orb" aria-hidden="true"></div><div class="sb7-hero__orbit sb7-hero__orbit--one" aria-hidden="true"></div><div class="sb7-hero__orbit sb7-hero__orbit--two" aria-hidden="true"></div>
        <img class="sb7-hero__mascot" src="{{ $heroImage }}" alt="StudyBuddy's dolphin mascot jumping from a magical book" width="800" height="800" decoding="async" fetchpriority="high" data-sb7-hero-img>
        <div class="sb7-hero__star sb7-hero__star--a" aria-hidden="true">✦</div><div class="sb7-hero__star sb7-hero__star--b" aria-hidden="true">✧</div>
        <div class="sb7-hero__float sb7-hero__float--a"><span class="sb7-hero__float-icon" aria-hidden="true">✦</span><span><strong>Curiosity first.</strong><small>Little moments, big ideas.</small></span></div>
        <div class="sb7-hero__float sb7-hero__float--b"><span class="sb7-hero__float-icon" aria-hidden="true">✓</span><span><strong>Learn your way.</strong><small>One step at a time.</small></span></div>
      </div>
    </div>
    <div class="sb7-hero__foot" aria-hidden="true"><span>YOUR NEXT ADVENTURE STARTS HERE</span><span class="sb7-hero__foot-line"></span><span>SCROLL TO EXPLORE ↓</span></div>
  </section>

  <section class="sb7-section sb7-apps" id="explore" aria-labelledby="sb7-explore-title">
    <div class="sb7-wrap">
      <div class="sb7-heading sb7-reveal"><div><p class="sb7-kicker">01 / DISCOVER</p><h2 id="sb7-explore-title">Find something that <em>clicks.</em></h2><p>{{ $appsSection?->subtitle ?: 'Explore learning activities made for curious minds.' }}</p></div><a class="sb7-text-link" href="{{ url('/apps') }}">Browse all apps <span aria-hidden="true">↗</span></a></div>
      @if($featured->isNotEmpty())
        <div class="sb7-app-grid">
        @foreach($featured->take(4) as $index => $app)
          @php
            $colors = $app->accentColors();
            $isDemo = str_starts_with((string)$app->slug, 'demo-next-');
            $img = $app->safeHeroImage();
          @endphp
          <a href="{{ route('studybuddy.apps.show', $app->slug) }}" class="sb7-app-card sb7-reveal" style="--sb7-app-a:{{ $colors[0] }};--sb7-app-b:{{ $colors[1] }};--sb7-card-order:{{ $index }}" data-sb7-spotlight>
            <div class="sb7-app-card__image" aria-hidden="true">
              @if($img)<img src="{{ $imageUrl($img) }}" alt="" loading="lazy" decoding="async">@else<span class="sb7-app-card__initial">{{ $app->icon ?: $app->initials() }}</span>@endif
            </div>
            <div class="sb7-app-card__body">
              <p class="sb7-app-card__type">{{ $isDemo ? 'CONCEPT PREVIEW' : 'EXPLORE & LEARN' }}</p>
              <h3>{{ $app->name }}</h3>
              @if($app->tagline)<p>{{ $app->tagline }}</p>@endif
              <span class="sb7-app-card__bottom"><span>{{ $isDemo ? 'DEMO · NOT PLAYABLE' : 'Explore app' }}</span><span aria-hidden="true">↗</span></span>
            </div>
          </a>
        @endforeach
        </div>
      @else
        <div class="sb7-apps__empty">Our learning collection is growing. <a href="{{ url('/apps') }}">See the full catalogue →</a></div>
      @endif
    </div>
  </section>

  <section class="sb7-section sb7-path" id="choose-your-path" aria-labelledby="sb7-path-title">
    <div class="sb7-wrap sb7-path__grid">
      <div class="sb7-path__intro sb7-reveal"><p class="sb7-kicker">02 / MADE FOR YOU</p><h2 id="sb7-path-title">Different minds.<br><em>One bright place.</em></h2><p>Whether you're learning, guiding, or teaching, StudyBuddy has a place for you.</p><div class="sb7-path__links"><a class="sb7-text-link" href="{{ url('/roles') }}">Explore every role ↗</a></div></div>
      <div class="sb7-path__selector sb7-reveal" data-sb7-roles>
        <div class="sb7-role-tabs" role="tablist" aria-label="Choose your learning perspective">
          <button type="button" role="tab" id="sb7-tab-student" aria-controls="sb7-role-panel" aria-selected="true" tabindex="0" data-sb7-role="student">Student</button>
          <button type="button" role="tab" id="sb7-tab-parent" aria-controls="sb7-role-panel" aria-selected="false" tabindex="-1" data-sb7-role="parent">Parent</button>
          <button type="button" role="tab" id="sb7-tab-teacher" aria-controls="sb7-role-panel" aria-selected="false" tabindex="-1" data-sb7-role="teacher">Teacher</button>
          <button type="button" role="tab" id="sb7-tab-independent" aria-controls="sb7-role-panel" aria-selected="false" tabindex="-1" data-sb7-role="independent">Independent</button>
        </div>
        <div class="sb7-role-panel" id="sb7-role-panel" role="tabpanel" aria-labelledby="sb7-tab-student" tabindex="0" aria-live="polite">
          <div class="sb7-role-panel__symbol" data-sb7-role-symbol aria-hidden="true">✦</div>
          <div class="sb7-role-panel__copy"><span class="sb7-role-panel__label">YOUR STUDYBUDDY SPACE</span><h3 data-sb7-role-title>A little progress, every day.</h3><p data-sb7-role-desc>Explore activities, keep your favourites close, and make learning part of your day.</p><a href="{{ url('/register') }}" data-sb7-role-link>Start learning <span aria-hidden="true">↗</span></a></div>
        </div>
      </div>
    </div>
  </section>

  @if($what && $what->items->isNotEmpty())
    <section class="sb7-section sb7-what" id="what-we-do" aria-labelledby="sb7-what-title"><div class="sb7-wrap"><div class="sb7-heading sb7-reveal"><div><p class="sb7-kicker">03 / THE LITTLE DETAILS</p><h2 id="sb7-what-title">{{ $what->title ?: 'Designed for everyday discovery' }}</h2>@if($what->subtitle)<p>{{ $what->subtitle }}</p>@endif</div></div><div class="sb7-what__grid">@foreach($what->items->take(3) as $item)<article class="sb7-what__card sb7-reveal" data-sb7-spotlight><span class="sb7-what__icon" aria-hidden="true">{{ $item->badge_text ?: '✦' }}</span><h3>{{ $item->title }}</h3>@if($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if($item->button_label)<a href="{{ $item->button_url ?: url('/apps') }}">{{ $item->button_label }} ↗</a>@endif</article>@endforeach</div></div></section>
  @endif

  <section class="sb7-school" aria-labelledby="sb7-school-title" id="for-schools">
    <div class="sb7-wrap sb7-school__grid">
      <div class="sb7-school__art sb7-reveal" aria-hidden="true"><span class="sb7-school__halo"></span><img src="{{ asset('assets/studybuddy-v7/learning-portal.webp') }}" alt="" width="700" height="700" loading="lazy" decoding="async"></div>
      <div class="sb7-school__copy sb7-reveal"><p class="sb7-kicker">04 / FOR SCHOOLS & FAMILIES</p><h2 id="sb7-school-title">A little more wonder.<br><em>A lot more together.</em></h2><p>StudyBuddy is growing into a connected learning space for students, families and educators. Schools can request a reviewed workspace, with access controls designed for their community.</p><a class="sb7-button sb7-button--primary" href="{{ route('schools.info') }}">Explore StudyBuddy for Schools <span aria-hidden="true">↗</span></a><small>School features are evolving; approval and verification are required.</small></div>
    </div>
  </section>

  @if($pagesSection || $whySection || $trustSection)
  <section class="sb7-section sb7-more" id="discover-more" aria-labelledby="sb7-more-title"><div class="sb7-wrap"><div class="sb7-heading sb7-reveal"><div><p class="sb7-kicker">05 / EXPLORE MORE</p><h2 id="sb7-more-title">Curious about <em>the details?</em></h2><p>More about what makes the StudyBuddy experience useful.</p></div></div>
  <div class="sb7-more__list">
    @foreach([['section'=>$pagesSection,'label'=>'Find your learning path'],['section'=>$whySection,'label'=>'Why StudyBuddy?'],['section'=>$trustSection,'label'=>'Our approach to trust']] as $group)
      @if($group['section'])<details class="sb7-more__item"><summary><span>{{ $group['section']->title ?: $group['label'] }}</span><span class="sb7-more__plus" aria-hidden="true">+</span></summary><div class="sb7-more__content">@if($group['section']->subtitle)<p>{{ $group['section']->subtitle }}</p>@endif<div class="sb7-more__tiles">@foreach($group['section']->items as $item)<div class="sb7-more__tile"><h3>{{ $item->title }}</h3>@if($item->subtitle)<p>{{ $item->subtitle }}</p>@endif @if($item->button_label)<a href="{{ $item->button_url ?: url('/apps') }}">{{ $item->button_label }} ↗</a>@endif</div>@endforeach</div></div></details>@endif
    @endforeach
  </div></div></section>
  @endif

  <section class="sb7-last" id="get-started"><div class="sb7-wrap sb7-last__inner sb7-reveal"><span class="sb7-last__spark" aria-hidden="true">✳</span><p class="sb7-kicker">YOUR NEXT LITTLE WIN STARTS HERE</p><h2>{{ $cta?->title ?: 'Ready to find your next adventure?' }}</h2>@if($cta?->subtitle)<p>{{ $cta->subtitle }}</p>@else<p>Find the activities that make you curious.</p>@endif<div class="sb7-last__actions"><a class="sb7-button sb7-button--primary" href="{{ $cta?->button_url ?: url('/apps') }}">{{ $cta?->button_label ?: 'Explore StudyBuddy' }} <span aria-hidden="true">↗</span></a>@if($cta?->secondary_button_label)<a class="sb7-button sb7-button--quiet" href="{{ $cta->secondary_button_url ?: url('/for-schools') }}">{{ $cta->secondary_button_label }}</a>@endif</div></div></section>
</div>
@endsection
