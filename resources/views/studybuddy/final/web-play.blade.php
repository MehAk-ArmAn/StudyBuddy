
@extends('layouts.app')

@section('content')
@php
    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };

    $worlds = [
        'math-quest' => ['✦', '#7c3cff', '#246bff', '#22d3ee'],
        'spelling-sprint' => ['Aa', '#ff4f9a', '#7c3cff', '#ffd166'],
        'reading-garden' => ['☘', '#16a34a', '#22c55e', '#22d3ee'],
        'focus-forest' => ['◌', '#0f766e', '#22c55e', '#22d3ee'],
        'planner-city' => ['▦', '#f59e0b', '#ef4444', '#7c3cff'],
        'quiz-galaxy' => ['◎', '#4f46e5', '#ec4899', '#22d3ee'],
        'shapes-lab' => ['△', '#06b6d4', '#8b5cf6', '#facc15'],
        'flashcard-castle' => ['▣', '#9333ea', '#f97316', '#fde68a'],
    ];

    $world = $worlds[$app->slug] ?? [$app->icon ?: '✨', $app->accent ?: '#7c3cff', '#246bff', '#22d3ee'];
    $image = $assetUrl($app->safeHeroImage());
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-connected-apps.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-connected-apps.css')) ? filemtime(public_path('assets/css/studybuddy-connected-apps.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-connected-apps.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-connected-apps.js')) ? filemtime(public_path('assets/js/studybuddy-connected-apps.js')) : time() }}" defer></script>

<main id="main-content" class="sb-app-play-final" style="--app-one: {{ $world[1] }}; --app-two: {{ $world[2] }}; --app-three: {{ $world[3] }};">
    <section class="play-stage" data-magic-card>
        <div class="play-copy">
            <a class="back-to-apps" href="{{ route('studybuddy.apps.show', $app->slug) }}">← Back to {{ $app->name }}</a>
            <p class="sb-apps-kicker">Web Play</p>
            <h1>{{ $app->name }}</h1>
            <p>{{ $canPlay ? 'Complete a quick demo session and collect your StudyBuddy points.' : 'Login to start this learning session and save your progress.' }}</p>

            <div class="detail-stat-row">
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} minutes</span>
                <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
            </div>

            @if($canPlay)
                <form method="POST" action="{{ route('studybuddy.final.session.complete') }}" class="play-form">
                    @csrf
                    <input type="hidden" name="app_slug" value="{{ $app->slug }}">
                    <button type="submit">Finish demo session</button>
                </form>
            @else
                <div class="sb-apps-hero-actions">
                    <a href="{{ route('login') }}">Login to Play</a>
                    <a class="soft" href="{{ route('register') }}">Create account</a>
                </div>
            @endif
        </div>

        <aside class="play-art">
            <div class="art-glow"></div>
            <img src="{{ $image }}" alt="{{ $app->name }} artwork">
            <span>{{ $world[0] }}</span>
            <div class="generated-sparkles detail" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></div>
        </aside>
    </section>

    <section class="play-steps">
        <article><span>1</span><strong>Start small</strong><p>Open the session and focus on one tiny win.</p></article>
        <article><span>2</span><strong>Try calmly</strong><p>Practice without pressure. StudyBuddy is built for friendly progress.</p></article>
        <article><span>3</span><strong>Collect progress</strong><p>Finish the demo and return to your points wallet.</p></article>
    </section>
</main>
@endsection
