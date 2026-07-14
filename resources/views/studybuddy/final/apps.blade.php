
@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $currentRole = $role ?? (auth()->check() ? (auth()->user()->role ?? null) : null);

    $worlds = [
        'math-quest' => ['✦', 'Cosmic Number Quest', '#7c3cff', '#246bff', '#22d3ee', 'Math missions that feel like a galaxy adventure.'],
        'spelling-sprint' => ['Aa', 'Word Speed Arena', '#ff4f9a', '#7c3cff', '#ffd166', 'Fast, friendly word practice with memory boosts.'],
        'reading-garden' => ['☘', 'Story Growth Garden', '#16a34a', '#22c55e', '#22d3ee', 'Calm reading, vocabulary blooms, and reflection.'],
        'focus-forest' => ['◌', 'Calm Focus Forest', '#0f766e', '#22c55e', '#22d3ee', 'Gentle focus sessions with peaceful routines.'],
        'planner-city' => ['▦', 'Routine Builder City', '#f59e0b', '#ef4444', '#7c3cff', 'Turn tasks into a clear daily map.'],
        'quiz-galaxy' => ['◎', 'Review Galaxy', '#4f46e5', '#ec4899', '#22d3ee', 'Quick quizzes, smart retries, and confidence.'],
        'shapes-lab' => ['△', 'Geometry Lab', '#06b6d4', '#8b5cf6', '#facc15', 'Visual thinking, shapes, and STEM puzzles.'],
        'flashcard-castle' => ['▣', 'Memory Castle', '#9333ea', '#f97316', '#fde68a', 'Recall practice inside a magical memory world.'],
    ];

    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $assetExists = function ($path) {
        if (!$path || preg_match('/^https?:\/\//i', $path)) return false;
        return file_exists(public_path(ltrim($path, '/')));
    };

    $questUrl = Route::has('studybuddy.quests.index') ? route('studybuddy.quests.index') : route('studybuddy.apps');
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-apps-final" data-sb-apps-page>
    <section class="sb-apps-hero-final">
        <div class="sb-apps-hero-copy">
            <p class="sb-apps-kicker">StudyBuddy App Universe</p>
            <h1>{{ $settings['launchpad_heading'] ?? 'Choose your learning world.' }}</h1>
            <p>{{ $settings['launchpad_intro'] ?? 'Pick a world, follow a tiny mission, collect progress, and make learning feel playful again.' }}</p>

            <div class="sb-apps-hero-actions">
                @auth
                    <a href="{{ route('studybuddy.final.points-wallet') }}">My Points Wallet</a>
                    <a class="soft" href="{{ $questUrl }}">My Quest</a>
                @else
                    <a href="{{ route('register') }}">Create free account</a>
                    <a class="soft" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>

        <aside class="sb-apps-hero-card" data-magic-card>
            <span class="hero-orbit">🚀</span>
            <strong>{{ $apps->count() }}</strong>
            <p>learning worlds ready to explore</p>
            <div class="hero-dots" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
        </aside>
    </section>

    <section class="sb-apps-controls" aria-label="Find learning worlds">
        <label>
            <span>Search</span>
            <input type="search" placeholder="Search math, reading, focus..." data-sb-app-search>
        </label>

        <label>
            <span>Category</span>
            <select data-sb-app-filter>
                <option value="all">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ \Illuminate\Support\Str::slug($category) }}">{{ $category }}</option>
                @endforeach
            </select>
        </label>

        <label>
            <span>Role</span>
            <select data-sb-role-filter>
                <option value="all">All roles</option>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" @selected($currentRole === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="sb-apps-friendly-note">
        @guest
            <strong>Preview mode:</strong> Browse every world. Login when you want to play, save progress, and earn points.
        @else
            <strong>{{ ucwords(str_replace('_', ' ', $currentRole ?? 'learner')) }} mode:</strong> Your learning worlds are ready. Pick one and start small.
        @endguest
    </section>

    @if($apps->count())
        <section class="sb-apps-grid-final" aria-label="StudyBuddy learning worlds">
            @foreach($apps as $app)
                @php
                    $rolesForApp = $app->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];
                    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->category ?: 'Learning World', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee', $app->tagline ?: 'A playful StudyBuddy learning world.'];

                    $mainImage = $assetUrl($app->safeHeroImage());

                    $galleryCandidates = [
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_main-icon.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_icon-512.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-glow.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-small.png",
                        "assets/studybuddy-imgs/02_apps/{$app->slug}/05_planets-bg/{$app->slug}_mini-planet.png",
                    ];

                    $gallery = collect($galleryCandidates)
                        ->filter(fn($path) => $assetExists($path))
                        ->map(fn($path) => $assetUrl($path))
                        ->unique()
                        ->take(3)
                        ->values();

                    $searchText = \Illuminate\Support\Str::lower($app->name.' '.$app->tagline.' '.$app->description.' '.$app->category.' '.implode(' ', $rolesForApp));
                @endphp

                <article
                    class="sb-app-card-final"
                    style="--app-one: {{ $world[2] }}; --app-two: {{ $world[3] }}; --app-three: {{ $world[4] }};"
                    data-app-card
                    data-magic-card
                    data-category="{{ \Illuminate\Support\Str::slug($app->category) }}"
                    data-roles="{{ implode(' ', $rolesForApp) }}"
                    data-search="{{ $searchText }}"
                >
                    <div class="sb-app-card-art">
                        <div class="generated-sparkles" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>
                        <img class="main-art" src="{{ $mainImage }}" alt="{{ $app->name }} artwork" loading="lazy">
                        <span class="app-symbol">{{ $world[0] }}</span>

                        @if($gallery->count())
                            <div class="mini-art-row" aria-hidden="true">
                                @foreach($gallery as $image)
                                    <img src="{{ $image }}" alt="">
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="sb-app-card-body">
                        <div class="sb-app-card-topline">
                            <span>{{ $world[1] }}</span>
                            <b>{{ ucfirst($app->status) }}</b>
                        </div>

                        <h2>{{ $app->name }}</h2>
                        <p class="tagline">{{ $app->tagline ?: $world[5] }}</p>
                        <p>{{ $app->preview_text ?: \Illuminate\Support\Str::limit($app->description, 145) }}</p>

                        <div class="role-pills">
                            @foreach($rolesForApp as $roleName)
                                <span>{{ ucwords(str_replace('_', ' ', $roleName)) }}</span>
                            @endforeach
                        </div>

                        <div class="app-mini-stats">
                            <span>⭐ {{ $app->points_reward }} pts</span>
                            <span>⏱ {{ $app->estimated_minutes }} min</span>
                            <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
                        </div>

                        <div class="app-card-actions">
                            <a href="{{ route('studybuddy.apps.show', $app->slug) }}">Explore World</a>
                            @if($app->is_web_enabled)
                                @auth
                                    <a class="soft" href="{{ route('studybuddy.final.web-play', $app->slug) }}">Play</a>
                                @else
                                    <a class="soft" href="{{ route('login') }}">Login to Play</a>
                                @endauth
                            @else
                                <span>Coming Soon</span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <p class="sb-apps-empty" data-sb-empty hidden>No worlds match that search yet. Try clearing the filters.</p>
    @else
        <section class="sb-apps-empty">
            <h2>Learning worlds are getting ready.</h2>
            <p>Please check back soon.</p>
        </section>
    @endif
</main>
@endsection
