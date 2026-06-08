@extends('layouts.app')

@section('title', 'Apps')
@section('body_class', 'page-shell page-apps premium-apps-page')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $storeApps = [
        ['title' => 'Math Quest', 'description' => 'Practice math in a fun way.', 'rating' => '4.8', 'img' => 'app-math-quest.png', 'tone' => 'violet', 'url' => route('apps.math-quest')],
        ['title' => 'Spelling Sprint', 'description' => 'Improve spelling and vocabulary.', 'rating' => '4.7', 'img' => 'app-spelling-sprint.png', 'tone' => 'blue', 'url' => route('apps.index')],
        ['title' => 'Reading Garden', 'description' => 'Read stories and build reading skills.', 'rating' => '4.8', 'img' => 'app-reading-garden.png', 'tone' => 'green', 'url' => route('apps.index')],
        ['title' => 'Focus Forest', 'description' => 'Stay focused and calm.', 'rating' => '4.8', 'img' => 'app-focus-forest.png', 'tone' => 'teal', 'url' => route('apps.index')],
        ['title' => 'Planner City', 'description' => 'Organize tasks and homework.', 'rating' => '4.6', 'img' => 'app-planner-city.png', 'tone' => 'indigo', 'url' => route('apps.index')],
        ['title' => 'Quiz Galaxy', 'description' => 'Test knowledge and earn stars.', 'rating' => '4.7', 'img' => 'app-quiz-galaxy.png', 'tone' => 'gold', 'url' => route('apps.index')],
        ['title' => 'Shapes Lab', 'description' => 'Learn shapes and their world.', 'rating' => '4.6', 'img' => 'app-shapes-lab.png', 'tone' => 'orange', 'url' => route('apps.index')],
        ['title' => 'Flashcard Castle', 'description' => 'Study anywhere with flashcards.', 'rating' => '4.8', 'img' => 'app-flashcard-castle.png', 'tone' => 'pink', 'url' => route('apps.index')],
    ];
@endphp

<section class="apps-store-page reveal-on-load" aria-labelledby="apps-store-title">
    <div class="apps-scene" aria-hidden="true">
        <img class="apps-scene-planet apps-scene-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt="">
        <img class="apps-scene-planet apps-scene-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt="">
        <img class="apps-scene-sparkles apps-scene-sparkles-a" src="{{ $asset('sparkles-pack.png') }}" alt="">
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
                        <p>Discover fun learning apps to play, practice and grow.</p>
                    </div>
                </div>

                <label class="apps-search" aria-label="Search apps">
                    <span aria-hidden="true"></span>
                    <input type="search" placeholder="Search apps...">
                </label>
            </header>

            <nav class="apps-filter-pills" aria-label="App filters">
                <button class="is-active" type="button">All</button>
                <button type="button">Popular</button>
                <button type="button">Primary (1–6)</button>
                <button type="button">Secondary (7–11)</button>
                <button type="button">New</button>
            </nav>

            <div class="apps-card-grid">
                @foreach($storeApps as $storeApp)
                    <article class="apps-premium-card apps-card-{{ $storeApp['tone'] }}" data-tilt-card>
                        <span class="apps-card-shine"></span>
                        <a class="apps-card-art" href="{{ $storeApp['url'] }}" aria-label="Open {{ $storeApp['title'] }}">
                            <span class="apps-card-halo"></span>
                            <img src="{{ $asset($storeApp['img']) }}" alt="{{ $storeApp['title'] }} app icon">
                        </a>
                        <div class="apps-card-copy">
                            <h2>{{ $storeApp['title'] }}</h2>
                            <p>{{ $storeApp['description'] }}</p>
                            <div class="apps-card-bottom">
                                <span class="apps-rating">⭐ {{ $storeApp['rating'] }}</span>
                                <a class="apps-start-button" href="{{ $storeApp['url'] }}">Start</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
