@extends('layouts.app')

@section('title', 'Apps')

@push('styles')
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
@endphp

@section('content')
<div
    class="sb-catalog-v3"
    data-app-catalog
>
    <section class="sb-catalog-v3__hero">
        <div class="sb-catalog-v3__intro">
            <p class="sb-catalog-v3__eyebrow">
                Apps
            </p>

            @if($apps->isEmpty())
                <h1>
                    New learning worlds are on the way.
                </h1>

                <p>
                    Playful new ways to practise, explore and grow will appear
                    here soon.
                </p>
            @else
                <h1>
                    Pick one. Play for ten minutes.
                </h1>

                <p>
                    Short games that teach something without announcing it.
                    Some run straight in your browser, some live on your phone.
                </p>

                <div class="sb-catalog-v3__stats">
                    <div>
                        <strong>{{ $apps->count() }}</strong>
                        <span>{{ \Illuminate\Support\Str::plural('App', $apps->count()) }}</span>
                    </div>

                    @if($readyApps > 0)
                        <div>
                            <strong>{{ $readyApps }}</strong>
                            <span>Play in browser</span>
                        </div>
                    @endif

                    @if($categories->count() > 0)
                        <div>
                            <strong>{{ $categories->count() }}</strong>
                            <span>{{ \Illuminate\Support\Str::plural('Subject', $categories->count()) }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="sb-catalog-v3__hero-actions">
            @auth
                <a
                    class="sb-app-button sb-app-button--secondary"
                    href="{{ route('dashboard') }}"
                >
                    Dashboard
                </a>
            @else
                <a
                    class="sb-app-button sb-app-button--secondary"
                    href="{{ route('login') }}"
                >
                    Sign in
                </a>
            @endauth

            @if(auth()->user()?->is_admin)
                <a
                    class="sb-app-button sb-app-button--primary"
                    href="{{ route('admin.control-room.apps.index') }}"
                >
                    Manage apps
                </a>
            @endif
        </div>
    </section>

    @if($apps->isNotEmpty())
    <section
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
                class="sb-app-card-v3"
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

            <h2>New learning worlds are on the way</h2>

            <p>
                Check back soon for the first collection of playful learning
                experiences.
            </p>

            <div class="sb-catalog-empty__actions">
                <a class="sb-app-button sb-app-button--primary" href="{{ url('/') }}">
                    Back to the homepage
                </a>

                @guest
                    <a class="sb-app-button sb-app-button--secondary" href="{{ route('register') }}">
                        Create an account
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
