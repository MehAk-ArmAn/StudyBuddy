@extends('layouts.app')

@section('title', 'Apps')
@section('body_class', 'page-shell page-apps premium-apps-page')

@section('content')
@php
    $asset = fn (string $path): string => str_starts_with($path, 'assets/') ? asset($path) : asset('assets/studybuddy/' . $path);
    $fallbackImages = [
        'math-quest' => 'assets/studybuddy/app-math-quest.png',
        'spelling-sprint' => 'assets/studybuddy/app-spelling-sprint.png',
        'reading-garden' => 'assets/studybuddy/app-reading-garden.png',
        'focus-forest' => 'assets/studybuddy/app-focus-forest.png',
        'planner-city' => 'assets/studybuddy/app-planner-city.png',
        'quiz-galaxy' => 'assets/studybuddy/app-quiz-galaxy.png',
        'shapes-lab' => 'assets/studybuddy/app-shapes-lab.png',
        'flashcard-castle' => 'assets/studybuddy/app-flashcard-castle.png',
    ];
@endphp

<section class="apps-store-page reveal-on-load" aria-labelledby="apps-store-title">
    <div class="apps-scene" aria-hidden="true">
        <img class="apps-scene-planet apps-scene-planet-left" src="{{ asset('assets/studybuddy/planet-ringed-lg.png') }}" alt="">
        <img class="apps-scene-planet apps-scene-planet-right" src="{{ asset('assets/studybuddy/planet-purple-lg.png') }}" alt="">
        <img class="apps-scene-sparkles apps-scene-sparkles-a" src="{{ asset('assets/studybuddy/sparkles-pack.png') }}" alt="">
        <span class="apps-scene-comet apps-scene-comet-a"></span>
        <span class="apps-scene-comet apps-scene-comet-b"></span>
        <span class="apps-scene-orb apps-scene-orb-a"></span>
        <span class="apps-scene-orb apps-scene-orb-b"></span>
    </div>

    <div class="apps-store-shell">
        <div class="apps-store-panel">
            <header class="apps-store-header">
                <div class="apps-store-heading">
                    <span class="apps-store-badge">
                        <span class="apps-store-badge-glow"></span>
                        <span class="apps-store-badge-shop" aria-hidden="true"><i></i><i></i><i></i></span>
                    </span>
                    <div>
                        <h1 id="apps-store-title">Apps Store</h1>
                        <p>Discover playable missions and upcoming StudyBuddy learning apps.</p>
                    </div>
                </div>

                <label class="apps-search" aria-label="Search apps">
                    <span aria-hidden="true"></span>
                    <input type="search" placeholder="Search apps...">
                </label>
            </header>

            <nav class="apps-filter-pills" aria-label="App filters">
                <button class="is-active" type="button">All</button>
                <button type="button">Playable</button>
                <button type="button">Primary</button>
                <button type="button">Secondary</button>
                <button type="button">Coming Soon</button>
            </nav>

            <div class="apps-card-grid">
                @foreach($apps as $app)
                    @php
                        $imagePath = $app->image_path ?? $fallbackImages[$app->slug] ?? 'assets/studybuddy/app-math-quest.png';
                        $canPlay = !empty($app->launch_path);
                    @endphp
                    <article class="apps-premium-card apps-card-{{ $app->card_tone ?? 'violet' }}" data-tilt-card>
                        <span class="apps-card-shine"></span>
                        <a class="apps-card-art" href="{{ route('apps.show', $app->slug) }}" aria-label="Open {{ $app->title }}">
                            <span class="apps-card-halo"></span>
                            <img src="{{ $asset($imagePath) }}" alt="{{ $app->title }} app icon">
                        </a>
                        <div class="apps-card-copy">
                            <h2>{{ $app->title }}</h2>
                            <p>{{ $app->description }}</p>
                            <div class="apps-card-bottom">
                                <span class="apps-rating">{{ $canPlay ? '⭐ ' . ($app->hero_metric ?? 'Live') : 'Coming Soon' }}</span>
                                @if($canPlay)
                                    <a class="apps-start-button" href="{{ route('apps.play', $app->slug) }}">Play</a>
                                @else
                                    <button class="apps-start-button is-disabled" type="button" disabled aria-disabled="true">Soon</button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
