@extends('layouts.app')

@section('title', $app->title)
@section('body_class', 'page-shell premium-apps-page')

@section('content')
@php
    $assetPath = $app->image_path ?? 'assets/studybuddy/app-math-quest.png';
    $canPlay = !empty($app->launch_path);
@endphp

<section class="math-quest-page reveal-on-load" aria-labelledby="app-title">
    <div class="math-page-shell">
        <div class="math-top-row">
            <a class="math-back-link" href="{{ route('apps.index') }}"><span aria-hidden="true">←</span> Back to All Apps</a>
        </div>
        <div class="math-hero-grid">
            <main class="math-hero-content glass-panel">
                <div class="math-title-row">
                    <span class="math-app-icon-card"><img src="{{ asset($assetPath) }}" alt="{{ $app->title }} app icon"></span>
                    <div>
                        <h1 id="app-title">{{ $app->title }}</h1>
                        <p>{{ $app->description }}</p>
                    </div>
                </div>
                <div class="math-age-row">
                    <span>{{ $app->subject }}</span>
                    <span>{{ $app->age_band }}</span>
                    <span>{{ $canPlay ? 'Playable' : 'Coming Soon' }}</span>
                </div>
                <div class="math-action-row">
                    @if($canPlay)
                        <a class="math-primary-button" href="{{ route('apps.play', $app->slug) }}">Play Now</a>
                    @else
                        <button class="math-primary-button is-disabled" type="button" disabled aria-disabled="true">Coming Soon</button>
                    @endif
                    <a class="button button-ghost" href="{{ route('register') }}">Save to My Apps</a>
                </div>
            </main>
        </div>
    </div>
</section>
@endsection
