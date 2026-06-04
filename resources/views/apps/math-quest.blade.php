@extends('layouts.app')

@section('title', 'Math Quest')

@section('content')
<section class="portal-shell reveal-on-load">
    <div class="portal-panel glass-panel">
        <div class="portal-copy">
            <p class="eyebrow">App Portal Page</p>
            <h1>Math Quest</h1>
            <p>Practice math in a fun and interactive way! Fly through glowing number planets, solve quick challenges, and earn cosmic rewards.</p>
            <div class="age-pills"><span>Ages 6–14</span><span>Primary & Secondary</span></div>
            <div class="hero-actions">
                <a class="button" href="{{ route('apps.math-quest.play') }}">Continue in Browser</a>
                <button class="button button-ghost" type="button">Download App</button>
            </div>
            <div class="download-row">
                <span class="store-badge">▶ Google Play</span>
                <span class="store-badge"> App Store</span>
                <span class="store-badge">▣ Web App</span>
            </div>
        </div>
        <div class="portal-art">
            @include('partials.image-placeholder', ['label' => 'APP_PORTAL_IMAGE_MATH_QUEST', 'variant' => 'portal', 'caption' => 'Math planet artwork'])
        </div>
        <div class="qr-card glass-panel">
            @include('partials.image-placeholder', ['label' => 'QR_CODE_IMAGE', 'variant' => 'qr', 'caption' => 'Scan to open Math Quest'])
        </div>
    </div>

    <div class="feature-grid">
        <article class="feature-card tilt-card"><span>🎮</span><h3>Fun Challenges</h3><p>100+ glowing number levels.</p></article>
        <article class="feature-card tilt-card"><span>⭐</span><h3>Earn Rewards</h3><p>Stars, badges, and coins.</p></article>
        <article class="feature-card tilt-card"><span>📊</span><h3>Track Progress</h3><p>See how you improve.</p></article>
        <article class="feature-card tilt-card"><span>☁</span><h3>Works Offline</h3><p>Keep learning anywhere.</p></article>
    </div>
</section>
@endsection
