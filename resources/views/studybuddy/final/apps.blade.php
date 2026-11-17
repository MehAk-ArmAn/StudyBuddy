@extends('layouts.app')

@section('title', 'Apps')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-sparkle-discovery.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-sparkle-discovery.css')) }}">
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-apps-v3.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-apps-v3.css')) ? filemtime(public_path('assets/css/studybuddy-apps-v3.css')) : time() }}"
>
@endpush

@php
    $imageUrl = function (?string $path): string {
        if (blank($path)) {
            return asset('assets/studybuddy-control/apps.svg');
        }

        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    };

    $readyApps = $apps->filter(
        fn ($app) => $app->hasPublishedWebApp()
    )->count();

    $roleLabels = [
        'student' => 'Learners',
        'parent' => 'Parents',
        'teacher' => 'Teachers',
        'independent_learner' => 'Independent',
    ];
    $sb8GalleryItems = $apps->filter(fn ($app) => filled($app->safeHeroImage()))->take(12)->map(function ($app) use ($imageUrl) {
        $demo = str_starts_with((string) $app->slug, 'demo-next-');
        return [
            'src' => $imageUrl($app->safeHeroImage()),
            'alt' => 'Artwork for '.(string) $app->name,
            'title' => (string) $app->name,
            'subtitle' => $demo ? 'DEMO · NOT PLAYABLE' : (string) ($app->category ?: 'Learning activity'),
            'url' => route('studybuddy.apps.show', $app->slug),
            'demo' => $demo,
        ];
    })->values()->all();
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-wave10-features.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-wave10-features.css')) }}">
@endpush

@section('content')
<div
    class="sb-catalog-v3 sbx-catalog-upgraded"
    data-app-catalog
>
    <div class="sb-v5-catalog-heading"><span class="sb-v5-pill"><span class="sb-v5-pill__dot"></span> THE DISCOVERY UNIVERSE</span><span class="sb-v5-catalog-heading__right">EXPLORE · EXPERIMENT · LEARN</span></div>
    <section class="sb-catalog-v3__hero sb-v6-discovery-hero" aria-labelledby="sb-v6-discovery-title" data-sb-v6-hero>
        <canvas data-sb-v6-flow class="sb-v6-flow-canvas" aria-hidden="true"></canvas>
        <div class="sb-catalog-v3__intro sb-v6-discovery-hero__copy">
            <p class="sb-catalog-v3__eyebrow"><span class="sb-v6-dot" aria-hidden="true"></span> YOUR PORTAL TO SOMETHING NEW</p>
            @if($apps->isEmpty())
                <h1 id="sb-v6-discovery-title">Every adventure <em>starts somewhere.</em></h1>
                <p>Activities will appear here when they are ready. Meanwhile, explore the rest of StudyBuddy.</p>
            @else
                <h1 id="sb-v6-discovery-title">Find a world.<br><em>Follow the wonder.</em></h1>
                <p>Discover learning activities by subject, search for something specific, or let curiosity lead the way. Demo concepts are marked clearly and are not playable.</p>
            @endif
            <div class="sb-catalog-v3__stats" aria-label="Current catalogue overview">
                <div><strong class="sbfb-stat-shell"><span class="sbfb-stat-static">{{ $apps->count() }}</span><span class="sbfb-stat-react" data-sb-react="SBCounter" data-sb-react-props='@json(["value" => $apps->count()])'></span></strong><span>{{ \Illuminate\Support\Str::plural('Catalogue entry', $apps->count()) }}</span></div>
                @if($readyApps > 0)<div><strong class="sbfb-stat-shell"><span class="sbfb-stat-static">{{ $readyApps }}</span><span class="sbfb-stat-react" data-sb-react="SBCounter" data-sb-react-props='@json(["value" => $readyApps])'></span></strong><span>Browser playable</span></div>@endif
                @if($categories->count() > 0)<div><strong class="sbfb-stat-shell"><span class="sbfb-stat-static">{{ $categories->count() }}</span><span class="sbfb-stat-react" data-sb-react="SBCounter" data-sb-react-props='@json(["value" => $categories->count()])'></span></strong><span>{{ \Illuminate\Support\Str::plural('Subject', $categories->count()) }}</span></div>@endif
            </div>
            <div class="sb-catalog-v3__hero-actions">
                <a class="sb-app-button sb-app-button--primary sb-v6-chromatic" href="#sb-v6-catalog-filters">Explore the catalogue <span aria-hidden="true">↗</span></a>
                @auth<a class="sb-app-button sb-app-button--secondary" href="{{ route('dashboard') }}">My dashboard</a>
                @else<a class="sb-app-button sb-app-button--secondary" href="{{ route('login') }}">Sign in</a>@endauth
                @if(auth()->user()?->is_admin)<a class="sb-app-button sb-app-button--secondary" href="{{ route('admin.control-room.apps.index') }}">Manage apps</a>@endif
            </div>
        </div>
        <div class="sb8-showcase-hero" aria-label="Featured StudyBuddy learning activity">
          <span class="sb8-showcase-halo" aria-hidden="true"></span>
          <div class="sb8-showcase-holo" data-sb8-holo-host>
            @if(count($sb8GalleryItems) > 0)
              <div data-sb8-component="holo" data-sb8-config="sb8-apps-gallery" aria-label="Interactive holographic app artwork"></div>
            @endif
            <img class="sb8-holo-fallback" src="{{ count($sb8GalleryItems) ? $sb8GalleryItems[0]['src'] : asset('assets/studybuddy-brand/pages/hero-dolphin-book.webp') }}" alt="Featured learning activity illustration" loading="eager" decoding="async">
          </div>
          <div class="sb8-showcase-label"><span>✦ THE DISCOVERY COLLECTION</span><strong>Made to spark curiosity.</strong></div>
        </div>
    </section>

    @if(count($sb8GalleryItems) >= 3)
      <section class="sb8-orbit-section" aria-labelledby="sb8-orbit-title" data-sb8-orbit-section>
        <div class="sb8-orbit-header">
          <div><span class="sb8-orbit-eyebrow">A NEW WAY TO EXPLORE</span><h2 id="sb8-orbit-title">Learning, in <em>motion.</em></h2><p>Spin through the collection. The full catalogue and its filters are right below.</p></div>
          <a href="#sb-v6-catalog-filters" class="sb8-orbit-link">Go to all activities ↗</a>
        </div>
        <div class="sb8-circle-mount" data-sb8-component="circular" data-sb8-config="sb8-apps-gallery"></div>
        <div class="sb10-gallery-fallback" aria-label="Learning activities without animation">
          @foreach(array_slice($sb8GalleryItems,0,6) as $item)<a href="{{ $item['url'] }}"><img src="{{ $item['src'] }}" alt=""><strong>{{ $item['title'] }}</strong><small>{{ $item['subtitle'] }}</small></a>@endforeach
        </div>
        <script type="application/json" id="sb8-apps-gallery">@json($sb8GalleryItems)</script>
      </section>
    @elseif(count($sb8GalleryItems) > 0)
        <script type="application/json" id="sb8-apps-gallery">@json($sb8GalleryItems)</script>
    @endif

    @if($apps->isNotEmpty())
    <section class="sb-v5-world-chooser" aria-label="Browse by subject">
        <div class="sb-v5-world-chooser__head"><div><p class="sb-v5-kicker">THE WORLD SELECTOR</p><h2>Choose your orbit. <em>See what sparks.</em></h2></div><button type="button" class="sb-v5-surprise" data-v5-surprise>✦ Surprise me <span aria-hidden="true">↗</span></button></div>
        <div class="sb-v5-world-chooser__chips" data-v5-world-chips>
            <button type="button" class="sb-v5-world-chip" data-v5-category="" aria-pressed="{{ empty($category) ? 'true' : 'false' }}"><span class="sb-v5-world-chip__symbol">✦</span> All worlds</button>
            @foreach($categories as $itemCategory)
                <button type="button" class="sb-v5-world-chip" data-v5-category="{{ \Illuminate\Support\Str::lower((string)$itemCategory) }}" aria-pressed="{{ \Illuminate\Support\Str::lower((string)$category) === \Illuminate\Support\Str::lower((string)$itemCategory) ? 'true' : 'false' }}"><span class="sb-v5-world-chip__symbol" aria-hidden="true">{{ ['Mathematics'=>'∑', 'English'=>'Aa', 'Geography'=>'◎', 'Science'=>'⚛', 'Visual Arts'=>'✿', 'Critical Thinking'=>'◇', 'Study Skills'=>'✦'][trim((string)$itemCategory)] ?? '✧' }}</span>{{ $itemCategory }}</button>
            @endforeach
        </div>
    </section>

    <section
        id="sb-v6-catalog-filters"
        class="sb-catalog-v3__toolbar"
        aria-label="Filter StudyBuddy apps"
    >
        <label class="sb-app-search">
            <span class="sb-app-search__icon" aria-hidden="true">
                <svg
                    viewBox="0 0 24 24"
                    width="20"
                    height="20"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
            </span>

            <span class="sr-only">Search apps</span>

            <input
                type="search"
                value="{{ $search }}"
                placeholder="Search by app, category, or skill"
                autocomplete="off"
                data-app-search
            >
        </label>

        <label>
            <span>Category</span>

            <select data-app-category>
                <option value="">All categories</option>

                @foreach($categories as $itemCategory)
                    <option
                        value="{{ \Illuminate\Support\Str::lower($itemCategory) }}"
                        @selected(
                            \Illuminate\Support\Str::lower((string) $category)
                            === \Illuminate\Support\Str::lower($itemCategory)
                        )
                    >
                        {{ $itemCategory }}
                    </option>
                @endforeach
            </select>
        </label>

        <label>
            <span>Designed for</span>

            <select data-app-role>
                <option value="">Everyone</option>

                @foreach($roleLabels as $key => $label)
                    <option
                        value="{{ $key }}"
                        @selected($role === $key)
                    >
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </label>

        <div
            class="sb-app-view-toggle"
            aria-label="Change app view"
        >
            <button
                type="button"
                class="is-active"
                data-app-view="grid"
                aria-label="Grid view"
                aria-pressed="true"
            >
                <svg
                    viewBox="0 0 24 24"
                    width="19"
                    height="19"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <rect x="3" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1"></rect>
                </svg>
            </button>

            <button
                type="button"
                data-app-view="list"
                aria-label="List view"
                aria-pressed="false"
            >
                <svg
                    viewBox="0 0 24 24"
                    width="19"
                    height="19"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path d="M8 6h13"></path>
                    <path d="M8 12h13"></path>
                    <path d="M8 18h13"></path>
                    <path d="M3 6h.01"></path>
                    <path d="M3 12h.01"></path>
                    <path d="M3 18h.01"></path>
                </svg>
            </button>
        </div>

        <button
            type="button"
            class="sb-app-reset"
            data-app-reset
        >
            Reset
        </button>
    </section>

    <div
        class="sb-catalog-v3__result"
        aria-live="polite"
    >
        <span>
            Showing
            <strong data-app-count>{{ $apps->count() }}</strong>
            {{ \Illuminate\Support\Str::plural('app', $apps->count()) }}
        </span>

    </div>

    <section
        class="sb-app-grid-v3"
        data-app-grid
        aria-label="StudyBuddy app catalog"
    >
        @foreach($apps as $app)
            @php
                $ready = $app->isAvailable();

                $roles = collect(
                    $app->audience_roles
                    ?: [
                        'student',
                        'parent',
                        'teacher',
                        'independent_learner',
                    ]
                );

                $searchText = \Illuminate\Support\Str::lower(
                    implode(' ', [
                        $app->name,
                        $app->category,
                        $app->tagline,
                        $app->description,
                        $app->preview_text,
                        $roles->implode(' '),
                    ])
                );
            @endphp

            <article
                class="sb-app-card-v3 sb-v5-spotlight-card"
                data-app-card
                data-search="{{ e($searchText) }}"
                data-category="{{ \Illuminate\Support\Str::lower((string) $app->category) }}"
                data-roles="{{ $roles->implode(' ') }}"
                data-ready="{{ $ready ? '1' : '0' }}"
            >
                <a
                    class="sb-app-card-v3__media"
                    href="{{ route('studybuddy.apps.show', $app->slug) }}"
                    aria-label="View {{ $app->name }}"
                >
                    <img
                        src="{{ $imageUrl($app->cardImage()) }}"
                        alt="{{ $app->name }} preview"
                        loading="lazy"
                        decoding="async"
                        onerror="this.onerror=null;this.src='{{ asset('assets/studybuddy-control/apps.svg') }}'"
                    >

                    @if($app->is_featured)
                        <span class="sb-app-card-v3__featured">
                            Featured
                        </span>
                    @endif
                </a>

                <div class="sb-app-card-v3__body">
                    <div class="sb-app-card-v3__topline">
                        @if(str_starts_with((string) $app->slug, 'demo-next-'))<span class="sb-glow-catalog-demo">DEMO · TEST ONLY</span>@endif
                        <span>
                            {{ $app->category ?: 'Learning' }}
                        </span>

                        <span class="{{ $ready ? 'is-ready' : '' }}">
                            {{ $app->availabilityLabel() }}
                        </span>
                    </div>

                    <div class="sb-app-card-v3__copy">
                        <h2>
                            <a href="{{ route('studybuddy.apps.show', $app->slug) }}">
                                {{ $app->name }}
                            </a>
                        </h2>

                        @if($app->tagline)
                            <p class="sb-app-card-v3__tagline">
                                {{ $app->tagline }}
                            </p>
                        @endif

                        <p>
                            {{
                                \Illuminate\Support\Str::limit(
                                    $app->preview_text
                                    ?: $app->description
                                    ?: 'A focused StudyBuddy learning experience.',
                                    150
                                )
                            }}
                        </p>
                    </div>

                    @if(!str_starts_with((string) $app->slug, 'demo-next-'))
                    <dl class="sb-app-card-v3__facts">
                        <div>
                            <dt>Session</dt>
                            <dd>{{ max(1, (int) $app->estimated_minutes) }} min</dd>
                        </div>

                        <div>
                            <dt>Reward</dt>
                            <dd>{{ max(0, (int) $app->points_reward) }} points</dd>
                        </div>

                        <div>
                            <dt>Age</dt>
                            <dd>
                                @if($app->age_min && $app->age_max)
                                    {{ $app->age_min }}–{{ $app->age_max }}
                                @elseif($app->age_min)
                                    {{ $app->age_min }}+
                                @elseif($app->age_max)
                                    Up to {{ $app->age_max }}
                                @else
                                    All
                                @endif
                            </dd>
                        </div>
                    </dl>

                    @else
                        <p class="sb-glow-card-only">Concept artwork only · no points, sessions, or downloads</p>
                    @endif
                    <div class="sb-app-card-v3__roles">
                        @foreach($roles->take(4) as $audienceRole)
                            <span>
                                {{ $roleLabels[$audienceRole] ?? \Illuminate\Support\Str::headline($audienceRole) }}
                            </span>
                        @endforeach
                    </div>

                    <div class="sb-app-card-v3__actions">
                        {{-- The card shows the single best real action; the
                             detail page lists every platform. --}}
                        @php($primaryAction = collect($app->availableActions())->firstWhere('primary', true))

                        @if($primaryAction)
                            <a
                                class="sb-app-button sb-app-button--primary"
                                href="{{ $primaryAction['url'] }}"
                                @if($primaryAction['key'] !== 'browser') target="_blank" rel="noopener" @endif
                            >
                                {{ $primaryAction['label'] }}
                            </a>
                        @else
                            <span
                                class="sb-app-button sb-app-button--disabled"
                                aria-disabled="true"
                            >
                                Coming soon
                            </span>
                        @endif

                        <a
                            class="sb-app-button sb-app-button--secondary"
                            href="{{ route('studybuddy.apps.show', $app->slug) }}"
                        >
                            Details
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </section>
    @else
        {{-- Catalogue empty: an intentional state, not an error. --}}
        <section class="sb-catalog-empty">
            <span class="sb-catalog-empty__mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.6">
                    <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                    <path d="M17.5 14.5v6M14.5 17.5h6"></path>
                </svg>
            </span>

            {{-- The hero above already says the shelf is empty. This block is
                 for what someone can usefully do instead. --}}
            <h2>Have a look around</h2>

            <p>
                See how StudyBuddy works while the first activities are on
                their way.
            </p>

            <div class="sb-catalog-empty__actions">
                <a class="sb-app-button sb-app-button--primary" href="{{ route('studybuddy.roles') }}">
                    How roles work
                </a>

                @guest
                    <a class="sb-app-button sb-app-button--secondary" href="{{ route('register') }}">
                        Create an account
                    </a>
                @else
                    <a class="sb-app-button sb-app-button--secondary" href="{{ url('/') }}">
                        Back to the homepage
                    </a>
                @endguest
            </div>
        </section>
    @endif

    <section
        class="sb-app-empty-v3"
        data-app-empty
        hidden
    >
        <img
            src="{{ asset('assets/studybuddy-control/apps.svg') }}"
            alt=""
        >

        <h2>Nothing matched that</h2>

        <p>
            Try a different word, or clear the filters to see everything.
        </p>

        <button
            type="button"
            class="sb-app-button sb-app-button--primary"
            data-app-reset
        >
            Show every app
        </button>
    </section>

    @if(auth()->user()?->is_admin)
        <aside class="sb-app-admin-v3">
            <div>
                <strong>You're signed in as an admin</strong>

                <p>
                    This page shows exactly what visitors see.
                </p>
            </div>

            <a
                class="sb-app-button sb-app-button--secondary"
                href="{{ route('admin.control-room.apps.index') }}"
            >
                Manage apps
            </a>
        </aside>
    @endif
</div>
@endsection

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-apps-v3.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-apps-v3.js')) ? filemtime(public_path('assets/js/studybuddy-apps-v3.js')) : time() }}"
    defer
></script>
@endpush

@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('assets/css/studybuddy-apps-roles-color.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-apps-roles-color.css')) ? filemtime(public_path('assets/css/studybuddy-apps-roles-color.css')) : time() }}"
>
@endpush

@push('scripts')
<script
    src="{{ asset('assets/js/studybuddy-apps-roles-color.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-apps-roles-color.js')) ? filemtime(public_path('assets/js/studybuddy-apps-roles-color.js')) : time() }}"
    defer
></script>
@endpush

@push('styles')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.css')))
<link rel="stylesheet" href="{{ asset('assets/studybuddy-reactbits/reactbits.css') }}?v={{ filemtime(public_path('assets/studybuddy-reactbits/reactbits.css')) }}">
@endif
@endpush
@push('scripts')
@if(file_exists(public_path('assets/studybuddy-reactbits/reactbits.js')))
<script src="{{ asset('assets/studybuddy-reactbits/reactbits.js') }}?v={{ filemtime(public_path('assets/studybuddy-reactbits/reactbits.js')) }}" defer></script>
@endif
@endpush
