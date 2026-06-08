@extends('layouts.app')

@section('title', 'Math Quest')
@section('body_class', 'page-shell page-math-quest premium-apps-page')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);
@endphp

<section class="math-quest-page reveal-on-load" aria-labelledby="math-quest-title">
    <div class="math-scene" aria-hidden="true">
        <img class="math-scene-planet math-scene-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt="">
        <img class="math-scene-planet math-scene-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt="">
        <img class="math-scene-sparkles" src="{{ $asset('sparkles-pack.png') }}" alt="">
        <span class="math-comet math-comet-a"></span>
        <span class="math-comet math-comet-b"></span>
        <span class="math-star math-star-a"></span>
        <span class="math-star math-star-b"></span>
        <span class="math-orb math-orb-a"></span>
        <span class="math-orb math-orb-b"></span>
    </div>

    <div class="math-page-shell">
        <div class="math-top-row">
            <a class="math-back-link" href="{{ route('apps.index') }}"><span aria-hidden="true">←</span> Back to All Apps</a>
            <div class="math-user-actions" aria-label="User shortcuts">
                <button type="button" aria-label="Notifications">🔔</button>
                <button class="math-user-chip" type="button"><span>👩🏽</span> Mehak⌄</button>
            </div>
        </div>

        <div class="math-hero-grid">
            <main class="math-hero-content">
                <div class="math-title-row">
                    <span class="math-app-icon-card">
                        <span></span>
                        <img src="{{ $asset('app-math-quest.png') }}" alt="Math Quest app icon">
                    </span>
                    <div>
                        <h1 id="math-quest-title">Math Quest <span class="math-title-star" aria-hidden="true"></span></h1>
                        <p>Practice math in a fun and interactive way!</p>
                    </div>
                </div>

                <div class="math-age-row" aria-label="App age and level badges">
                    <span>Ages 6–14</span>
                    <span>Primary &amp; Secondary</span>
                </div>

                <div class="math-action-row">
                    <a class="math-primary-button" href="{{ route('apps.math-quest.play') }}">Continue in Browser</a>
                    <button class="math-download-button" type="button"><span aria-hidden="true">⇩</span> Download App <span aria-hidden="true">⌄</span></button>
                </div>

                <section class="math-availability-panel" aria-label="Available on">
                    <div class="math-available-top">
                        <div>
                            <h2>Available on</h2>
                            <div class="math-store-badges">
                                <a href="#"><span class="math-play-icon"></span><strong><small>GET IT ON</small>Google Play</strong></a>
                                <a href="#"><span class="math-apple-icon"></span><strong><small>Download on the</small>App Store</strong></a>
                                <a href="{{ route('apps.math-quest.play') }}"><span class="math-web-icon">◎</span><strong><small>Launch in</small>Web App</strong></a>
                            </div>
                        </div>
                        <div class="math-qr-placeholder" aria-label="QR code placeholder">
                            <span></span><span></span><span></span><span></span><i></i>
                        </div>
                    </div>

                    <div class="math-feature-cards">
                        <article><span class="math-feature-icon math-feature-game"></span><div><strong>Fun Challenges</strong><small>100+ levels</small></div></article>
                        <article><span class="math-feature-icon math-feature-reward"></span><div><strong>Earn Rewards</strong><small>Stars, badges &amp; coins</small></div></article>
                        <article><span class="math-feature-icon math-feature-progress"></span><div><strong>Track Progress</strong><small>See how you improve</small></div></article>
                    </div>
                </section>
            </main>

            <aside class="math-visual-zone" aria-label="Math Quest artwork">
                <div class="math-big-planet">
                    <span class="math-planet-ring math-planet-ring-a"></span>
                    <span class="math-planet-ring math-planet-ring-b"></span>
                    <img src="{{ $asset('app-math-quest.png') }}" alt="Large Math Quest planet icon">
                </div>
            </aside>

            <aside class="math-download-card" aria-label="Download Math Quest app">
                <div class="math-download-card-copy">
                    <h2>Get the best experience!</h2>
                    <p>Download Math Quest App</p>
                </div>
                <a class="math-download-store math-download-google" href="#"><span class="math-play-icon"></span> Google Play</a>
                <a class="math-download-store" href="#"><span class="math-apple-icon"></span> App Store</a>
                <div class="math-divider"><span></span>or<span></span></div>
                <a class="math-download-store math-download-web" href="{{ route('apps.math-quest.play') }}"><span class="math-web-icon">◎</span> Launch Web App</a>
                <div class="math-promo-mascot">
                    <span class="math-promo-star"></span>
                    <img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin mascot">
                    <span class="math-promo-star"></span>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
