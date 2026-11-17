{{-- W11: Base44 reference-inspired view, REAL Laravel data and routes. No Base44 runtime. --}}
@php
    $sb11Apps = collect($discoveryApps ?? $featuredApps ?? $sbCarouselItems ?? [])->take(12);
    $sb11Artwork = 'assets/studybuddy-brand/pages/';
    $sb11ImageUrl = static function ($path) {
        $path = trim((string) ($path ?? ''));
        if ($path === '') return null;
        return preg_match('~^https?://~i', $path) ? $path : asset(ltrim($path, '/'));
    };
    $sb11DefaultTitle = 'Learning that feels like an adventure.';
    $sb11Title = trim((string) (data_get($settings ?? [], 'sbx_hero_title', $sb11DefaultTitle) ?: $sb11DefaultTitle));
    $sb11Description = trim((string) (data_get($settings ?? [], 'sbx_hero_description', 'Illustrated worlds, playful apps and a progress map that belongs to the learner. StudyBuddy turns curiosity into momentum — at home, in class, or somewhere in between.')));
    $sb11Roles = [
        ['name'=>'Students','micro'=>'Discover new adventures','desc'=>'Explore learning worlds and follow what makes you curious.','art'=>'path-apps.webp','tint'=>'blue'],
        ['name'=>'Families','micro'=>'Grow together','desc'=>'Stay close to the learning journey in a supportive space.','art'=>'path-parents.webp','tint'=>'pink'],
        ['name'=>'Educators','micro'=>'Inspire curiosity','desc'=>'Find thoughtful teaching tools and school experiences.','art'=>'path-teachers.webp','tint'=>'teal'],
        ['name'=>'Independent','micro'=>'Find your own rhythm','desc'=>'Explore interests and goals at a pace that fits you.','art'=>'path-support.webp','tint'=>'sand'],
    ];
@endphp
<div class="sb11" data-sb11-home>
    <div class="sb11-progress" aria-hidden="true"><span data-sb11-progress></span></div>

    <section class="sb11-hero" id="sb11-home" aria-labelledby="sb11-hero-title">
        <div class="sb11-hero__atmosphere" aria-hidden="true">
            <canvas class="sb11-hero__starcanvas" data-sb11-stars></canvas>
            {{-- The existing StudyBuddy React/Vite bridge mounts genuine installed free React Bits components. --}}
            <div class="sb11-react sb11-react--galaxy" data-sb-react="SBW8Galaxy" data-sb-react-props='{"density":0.8,"glowIntensity":0.21,"hueShift":20,"twinkleIntensity":0.4}'></div>
            <div class="sb11-react sb11-react--gravity" data-sb-react="SBW8Antigravity" data-sb-react-props='{"colors":["#9274F6","#68D8F0","#E3B5FF"],"count":120,"particleSize":0.5,"autoAnimate":true}'></div>
        </div>
        <div class="sb11-container sb11-hero__layout">
            <div class="sb11-hero__content">
                <div class="sb11-eyebrow sb11-hero__eyebrow"><span aria-hidden="true">✧</span> LEARN · PLAY · GROW</div>
                <h1 id="sb11-hero-title">
                    @if ($sb11Title === $sb11DefaultTitle)
                        <span>Learning that feels</span><em>like an adventure.</em>
                    @else
                        <span>{{ $sb11Title }}</span>
                    @endif
                </h1>
                <p class="sb11-hero__lead">{{ $sb11Description }}</p>
                <div class="sb11-hero__actions">
                    <a class="sb11-button sb11-button--primary" href="{{ url('/apps') }}">Explore learning apps <span aria-hidden="true">↗</span></a>
                    <a class="sb11-button sb11-button--secondary" href="#sb11-how">See how it works <span aria-hidden="true">▷</span></a>
                </div>
                <form class="sb11-search" action="{{ url('/apps') }}" role="search" method="GET">
                    <label class="sb11-visually-hidden" for="sb11-search">Search available StudyBuddy apps</label>
                    <span aria-hidden="true">⌕</span><input id="sb11-search" type="search" name="q" maxlength="80" placeholder="Search a subject, app or learning world…">
                    <button type="submit" aria-label="Search StudyBuddy apps">Search ↗</button>
                </form>
                <div class="sb11-hero__facts" aria-label="StudyBuddy experiences"><div><strong>{{ $sb11Apps->count() }}</strong><span>featured app{{ $sb11Apps->count() === 1 ? '' : 's' }}</span></div><div><strong>4</strong><span>learning paths</span></div><div><strong>28</strong><span>subject categories</span></div></div>
                <a class="sb11-scrollhint" href="#sb11-worlds">SCROLL TO EXPLORE <span aria-hidden="true">↓</span></a>
            </div>
            <div class="sb11-hero__art" aria-label="StudyBuddy dolphin mascot rising from a magical book">
                <span class="sb11-hero__orbit sb11-hero__orbit--outer" aria-hidden="true"></span>
                <span class="sb11-hero__orbit sb11-hero__orbit--inner" aria-hidden="true"></span>
                <span class="sb11-hero__light" aria-hidden="true"></span>
                <img src="{{ asset($sb11Artwork.'hero-dolphin-book.webp') }}" width="900" height="900" class="sb11-hero__dolphin" alt="A joyful blue dolphin leaping from a magical open book" fetchpriority="high" decoding="async">
                <a class="sb11-float sb11-float--discover" href="{{ url('/learning-hub') }}"><span>NOW EXPLORING</span><strong>Find your learning world</strong><small>Discover something new ↗</small></a>
                <span class="sb11-float sb11-float--spark" aria-hidden="true">✦  Every adventure starts here</span>
            </div>
        </div>
        <div class="sb11-hero__fade" aria-hidden="true"></div>
    </section>

    <section class="sb11-worlds" id="sb11-worlds" aria-labelledby="sb11-worlds-heading">
        <div class="sb11-container">
            <div class="sb11-intro sb11-inview"><div><div class="sb11-eyebrow">01 / THE DISCOVERY COLLECTION</div><h2 id="sb11-worlds-heading">Pick a world.<br><em>Follow the wonder.</em></h2></div><p>Learning, but make it an adventure. Explore actual StudyBuddy app listings, discover the subjects behind them, and choose what to try next.</p></div>
            <div class="sb11-worlds__tools">
                <div class="sb11-filters" role="group" aria-label="Filter featured learning apps">
                    <button type="button" class="is-selected" aria-pressed="true" data-sb11-filter="all">All worlds</button>
                    @foreach($sb11Apps->pluck('category')->filter()->unique()->take(4) as $sb11Category)
                        <button type="button" data-sb11-filter="{{ \Illuminate\Support\Str::slug((string) $sb11Category) }}" aria-pressed="false">{{ $sb11Category }}</button>
                    @endforeach
                </div>
                <div class="sb11-reel__buttons"><button type="button" data-sb11-prev aria-label="Previous featured app">←</button><button type="button" data-sb11-next aria-label="Next featured app">→</button></div>
            </div>
            <div class="sb11-reel" tabindex="0" role="region" aria-label="Featured learning apps, scroll horizontally" data-sb11-reel>
                @forelse ($sb11Apps as $sb11Index => $sb11App)
                    @php
                        $sb11Cover = method_exists($sb11App, 'safeHeroImage') ? $sb11App->safeHeroImage() : null;
                        $sb11Cover = $sb11Cover ?: (method_exists($sb11App, 'cardImage') ? $sb11App->cardImage() : null);
                        $sb11Cover = $sb11ImageUrl($sb11Cover) ?: asset($sb11Artwork.(['path-apps.webp', 'path-parents.webp', 'path-teachers.webp', 'path-support.webp'][$sb11Index % 4]));
                        $sb11Availability = method_exists($sb11App, 'availabilityLabel') ? $sb11App->availabilityLabel() : 'View details';
                    @endphp
                    <a class="sb11-app" data-sb11-app data-sb11-category="{{ \Illuminate\Support\Str::slug((string) $sb11App->category) }}" href="{{ route('studybuddy.apps.show', $sb11App->slug) }}">
                        <span class="sb11-app__image"><img src="{{ $sb11Cover }}" alt="" loading="lazy" decoding="async"><span class="sb11-app__index">{{ str_pad((string)($sb11Index+1), 2, '0', STR_PAD_LEFT) }}</span><span class="sb11-app__arrow" aria-hidden="true">↗</span></span>
                        <span class="sb11-app__meta">{{ $sb11App->category ?: 'Learning' }} <span aria-hidden="true">·</span> {{ $sb11Availability }}</span>
                        <strong>{{ $sb11App->name }}</strong>
                        <span class="sb11-app__tagline">{{ $sb11App->tagline ?: 'Discover a new way to learn.' }}</span>
                    </a>
                @empty
                    <div class="sb11-noapps"><h3>New adventures are taking shape.</h3><p>When apps are published through the existing StudyBuddy CMS, they appear here automatically.</p><a class="sb11-button sb11-button--primary" href="{{ url('/apps') }}">Visit app library ↗</a></div>
                @endforelse
            </div>
            <div class="sb11-reel__footer"><span>SWIPE, SCROLL OR USE THE ARROWS</span><a href="{{ url('/apps') }}">Explore the complete app library ↗</a></div>
        </div>
    </section>

    <section class="sb11-atmosphere" aria-label="Our learning philosophy"><div class="sb11-container sb11-atmosphere__inner sb11-inview"><span class="sb11-eyebrow">A LITTLE WONDER GOES A LONG WAY</span><p>Every discovery begins with <em>curiosity.</em></p><a href="{{ url('/learning-hub') }}">Discover the Learning Hub ↗</a></div></section>

    <section class="sb11-story" id="sb11-how" aria-labelledby="sb11-story-heading">
        <div class="sb11-container sb11-story__layout">
            <div class="sb11-story__display"><div class="sb11-story__gallery" data-sb11-story-frame>
                <img data-sb11-story-image="0" class="is-active" src="{{ asset($sb11Artwork.'path-apps.webp') }}" alt="Colourful learning apps to discover" loading="lazy" decoding="async">
                <img data-sb11-story-image="1" src="{{ asset($sb11Artwork.'path-parents.webp') }}" alt="Family learning illustration" loading="lazy" decoding="async">
                <img data-sb11-story-image="2" src="{{ asset($sb11Artwork.'path-teachers.webp') }}" alt="Creative classroom experience" loading="lazy" decoding="async">
                <div class="sb11-story__overlay"><span>STUDYBUDDY / DISCOVER · CREATE · GROW</span><span>✧</span></div>
            </div></div>
            <div class="sb11-story__content"><div class="sb11-eyebrow">02 / YOUR STORY, YOUR WAY</div><h2 id="sb11-story-heading">Learning feels <em>better when it's yours.</em></h2>
                <article class="sb11-story__step is-active" data-sb11-step="0"><small>01 — DISCOVER</small><h3>Find what sparks your curiosity.</h3><p>Explore subjects, games and learning experiences from the real StudyBuddy catalogue.</p><a href="{{ url('/apps') }}">Explore the app library ↗</a></article>
                <article class="sb11-story__step" data-sb11-step="1"><small>02 — MAKE IT PERSONAL</small><h3>Collect what matters to you.</h3><p>Save activities, organize interests, and set your own learning goals in My Quest after signing in.</p><a href="{{ url('/my-quest') }}">Open My Quest ↗</a></article>
                <article class="sb11-story__step" data-sb11-step="2"><small>03 — KEEP GROWING</small><h3>Celebrate your little wins.</h3><p>Your dashboard shows real account activity. It never invents progress or school records.</p><a href="{{ url('/dashboard') }}">See my dashboard ↗</a></article>
            </div>
        </div>
    </section>

    <section class="sb11-roles" id="sb11-roles" aria-labelledby="sb11-roles-heading"><div class="sb11-container">
        <div class="sb11-roles__heading sb11-inview"><span class="sb11-eyebrow">03 / MADE FOR YOUR WAY OF LEARNING</span><h2 id="sb11-roles-heading">Everyone belongs <em>in this story.</em></h2><p>One world. Four perspectives. Find the space that feels like yours.</p></div>
        <div class="sb11-roles__gallery">
            @foreach ($sb11Roles as $sb11Idx => $sb11Role)
                <a href="{{ url('/roles') }}" class="sb11-role sb11-role--{{ $sb11Role['tint'] }}">
                    <span class="sb11-role__index">0{{ $sb11Idx+1 }}</span><span class="sb11-role__arrow" aria-hidden="true">↗</span>
                    <img src="{{ asset($sb11Artwork.$sb11Role['art']) }}" loading="lazy" decoding="async" alt="">
                    <span class="sb11-role__caption"><small>{{ $sb11Role['micro'] }}</small><strong>{{ $sb11Role['name'] }}</strong><span>{{ $sb11Role['desc'] }}</span></span>
                </a>
            @endforeach
        </div>
        <a class="sb11-roles__link" href="{{ url('/roles') }}">Compare roles and access options ↗</a>
    </div></section>

    <section class="sb11-end" aria-labelledby="sb11-end-heading"><div class="sb11-container sb11-end__grid">
        <div class="sb11-end__copy sb11-inview"><span class="sb11-eyebrow">04 / EVERY ADVENTURE STARTS SOMEWHERE</span><h2 id="sb11-end-heading">Start with one <em>little wonder.</em></h2><p>Open a learning app, create a personal goal, or explore how StudyBuddy can help your school.</p><div class="sb11-hero__actions"><a class="sb11-button sb11-button--primary" href="{{ url('/apps') }}">Choose an app ↗</a><a class="sb11-button sb11-button--secondary" href="{{ url('/for-schools') }}">Explore for schools ↗</a></div></div>
        <div class="sb11-end__art"><img src="{{ asset($sb11Artwork.'hero-dolphin-book.webp') }}" width="900" height="900" loading="lazy" decoding="async" alt="StudyBuddy dolphin and magical book"></div>
    </div></section>
</div>
