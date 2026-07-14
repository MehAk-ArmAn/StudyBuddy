
@extends('layouts.app')

@section('content')
@php
    $worlds = [
        'math-quest' => ['✦', 'Cosmic Number Quest', '#7c3cff', '#246bff', '#22d3ee', '#fff3b0', 'Turn numbers into a glowing mission you actually want to finish.'],
        'spelling-sprint' => ['Aa', 'Word Speed Arena', '#ff4f9a', '#7c3cff', '#ffd166', '#fff0f8', 'Make spelling practice fast, friendly, and way less scary.'],
        'reading-garden' => ['☘', 'Story Growth Garden', '#16a34a', '#22c55e', '#22d3ee', '#f0fff6', 'Grow reading fluency one calm story at a time.'],
        'focus-forest' => ['◌', 'Calm Focus Forest', '#0f766e', '#22c55e', '#22d3ee', '#ecfeff', 'Build focus without making studying feel heavy.'],
        'planner-city' => ['▦', 'Routine Builder City', '#f59e0b', '#ef4444', '#7c3cff', '#fff7ed', 'Turn messy tasks into a city map you can follow.'],
        'quiz-galaxy' => ['◎', 'Review Galaxy', '#4f46e5', '#ec4899', '#22d3ee', '#eef2ff', 'Launch quick quizzes across your learning galaxy.'],
        'shapes-lab' => ['△', 'Geometry Discovery Lab', '#06b6d4', '#8b5cf6', '#facc15', '#ecfeff', 'Explore shapes, patterns, and visual problem solving.'],
        'flashcard-castle' => ['▣', 'Memory Castle', '#9333ea', '#f97316', '#fde68a', '#faf5ff', 'Protect your knowledge inside a memory castle.'],
    ];

    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->category ?: 'Learning World', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee', '#f7f9ff', $app->tagline ?: 'Explore this StudyBuddy learning world.'];

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

    $mainImage = $assetUrl($app->safeHeroImage());

    $galleryCandidates = [
        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_main-icon.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/01_app-icon/{$app->slug}_icon-512.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-glow.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/02_orbs/{$app->slug}_orb-small.png",
        "assets/studybuddy-imgs/02_apps/{$app->slug}/05_planets-bg/{$app->slug}_mini-planet.png",
    ];

    $gallery = collect($galleryCandidates)->filter(fn($path) => $assetExists($path))->map(fn($path) => $assetUrl($path))->unique()->take(4)->values();

    $rolesForApp = $app->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];

    $outcomes = collect($app->learning_outcomes ?: [])
        ->map(fn($x) => is_array($x) ? ($x['text'] ?? $x['title'] ?? implode(' ', $x)) : $x)
        ->filter()
        ->values();

    if (!$outcomes->count()) {
        $outcomes = collect(['Build confidence', 'Practice safely', 'Track progress', 'Keep learning fun']);
    }

    $sections = collect($app->detail_sections ?: [])
        ->map(fn($x) => is_array($x)
            ? ['title' => $x['title'] ?? 'Learning step', 'body' => $x['body'] ?? $x['text'] ?? $x['description'] ?? 'A focused learning step.']
            : ['title' => 'Learning step', 'body' => $x])
        ->filter()
        ->values();

    if (!$sections->count()) {
        $sections = collect([
            ['title' => 'Start Small', 'body' => 'Choose a short activity and begin with a tiny win.'],
            ['title' => 'Practice Gently', 'body' => 'Complete focused rounds with friendly feedback.'],
            ['title' => 'Grow Confident', 'body' => 'Review your progress and return when ready.'],
        ]);
    }

    $relatedImage = function ($mini) use ($assetUrl) {
        return $assetUrl($mini->safeHeroImage());
    };
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-app-detail-final" data-sb-app-detail style="--app-one: {{ $world[2] }}; --app-two: {{ $world[3] }}; --app-three: {{ $world[4] }};">
    <section class="sb-detail-hero-final">
        <div class="sb-detail-copy">
            <a class="back-to-apps" href="{{ route('studybuddy.apps') }}">← Back to App Universe</a>
            <p class="sb-apps-kicker">{{ $world[1] }}</p>
            <h1>{{ $app->name }}</h1>
            <p>{{ $app->description ?: $world[6] }}</p>

            <div class="detail-stat-row">
                <span>{{ ucfirst($app->status) }}</span>
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} minutes</span>
                <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
            </div>

            <div class="sb-apps-hero-actions">
                @if($app->is_web_enabled)
                    @auth
                        <a href="{{ route('studybuddy.final.web-play', $app->slug) }}">Start Playing</a>
                    @else
                        <a href="{{ route('login') }}">Login to Play</a>
                    @endauth
                @else
                    <a href="{{ route('studybuddy.apps') }}">Explore More Apps</a>
                @endif
                <a class="soft" href="{{ route('studybuddy.final.points-wallet') }}">Points Wallet</a>
            </div>
        </div>

        <aside class="detail-art-stage" data-magic-card>
            <div class="art-glow"></div>
            <img class="main-art" src="{{ $mainImage }}" alt="{{ $app->name }} artwork">
            <span class="app-symbol big">{{ $world[0] }}</span>
            <div class="generated-sparkles detail" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>

            @if($gallery->count())
                <div class="detail-gallery" aria-hidden="true">
                    @foreach($gallery as $image)
                        <img src="{{ $image }}" alt="">
                    @endforeach
                </div>
            @endif
        </aside>
    </section>

    <section class="detail-info-strip">
        <article><span>World</span><strong>{{ $world[1] }}</strong></article>
        <article><span>Best for</span><strong>{{ collect($rolesForApp)->map(fn($r) => ucwords(str_replace('_', ' ', $r)))->join(', ') }}</strong></article>
        <article><span>Experience</span><strong>Playful, calm, and progress-friendly</strong></article>
    </section>

    <section class="detail-section-final split">
        <div>
            <p class="sb-apps-kicker">What you’ll build</p>
            <h2>Small wins that turn into real confidence.</h2>
            <p>{{ $app->safety_note ?: 'StudyBuddy keeps practice friendly, clear, and easy to return to.' }}</p>
        </div>

        <div class="outcome-grid">
            @foreach($outcomes as $outcome)
                <article data-magic-card><span>✓</span><strong>{{ $outcome }}</strong></article>
            @endforeach
        </div>
    </section>

    <section class="detail-section-final">
        <div class="section-heading">
            <p class="sb-apps-kicker">Learning journey</p>
            <h2>How this world feels when you use it.</h2>
        </div>

        <div class="mission-grid">
            @foreach($sections as $section)
                <article data-magic-card>
                    <span>0{{ $loop->iteration }}</span>
                    <h3>{{ $section['title'] }}</h3>
                    <p>{{ $section['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="detail-section-final split">
        <div>
            <p class="sb-apps-kicker">Play options</p>
            <h2>Start where you are.</h2>
            <p>Open a quick web session now, then come back to your points and progress anytime.</p>
        </div>

        <div class="platform-choice-grid">
            @if($app->is_web_enabled)
                @auth
                    <a href="{{ route('studybuddy.final.web-play', $app->slug) }}">Web Play <span>→</span></a>
                @else
                    <a href="{{ route('login') }}">Login to Play <span>🔒</span></a>
                @endauth
            @else
                <span>Web Play soon</span>
            @endif

            <span>iOS soon</span>
            <span>Android soon</span>
            <span>Desktop soon</span>
        </div>
    </section>

    @if($related->count())
        <section class="detail-section-final">
            <div class="section-heading">
                <p class="sb-apps-kicker">Explore more</p>
                <h2>More learning worlds</h2>
            </div>

            <div class="related-worlds">
                @foreach($related as $mini)
                    <a href="{{ route('studybuddy.apps.show', $mini->slug) }}" data-magic-card>
                        <img src="{{ $relatedImage($mini) }}" alt="{{ $mini->name }} artwork">
                        <strong>{{ $mini->name }}</strong>
                        <span>{{ $mini->category }}</span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
