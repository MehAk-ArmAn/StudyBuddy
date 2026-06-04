@extends('layouts.app')

@section('title', 'Math Quest')

@section('content')
<section class="portal-page reveal-on-load">
    <div class="portal-panel glass-panel">
        <div class="portal-copy">
            <p class="eyebrow">03 App Portal Page</p>
            <h1>Math Quest</h1>
            <p>Practice math in a fun and interactive way! Solve neon number worlds, earn coins, and continue across browser, phone, or tablet.</p>
            <div class="pill-row"><span>Ages 6–14</span><span>Primary & Secondary</span></div>
            <div class="hero-actions"><a class="button" href="{{ route('apps.math-quest.play') }}">Continue in Browser</a><button class="button button-ghost" type="button">Download App⌄</button></div>
            <p class="available">Available on</p>
            <div class="store-badges"><span>▶ Google Play</span><span> App Store</span><span>▣ Web App</span></div>
        </div>
        <div class="portal-orb">@include('partials.image-placeholder', ['label' => 'APP_PORTAL_IMAGE_MATH_QUEST', 'src' => 'assets/studybuddy/app-math-quest.png', 'variant' => 'portal-big', 'caption' => 'Math Quest planet'])</div>
        <div class="qr-zone">@include('partials.image-placeholder', ['label' => 'QR_CODE_IMAGE', 'variant' => 'qr', 'caption' => 'QR code'])</div>
        <aside class="experience-popup tilt-card"><b>Get the best experience!</b><small>Download Math Quest App</small><button>▶ Google Play</button><button> App Store</button><a>Maybe Later</a></aside>
    </div>
    <div class="portal-features"><article>🎮 <b>Fun Challenges</b><span>100+ levels</span></article><article>⭐ <b>Earn Rewards</b><span>Stars, badges & coins</span></article><article>📊 <b>Track Progress</b><span>See how you improve</span></article><article>☁ <b>Works Offline</b><span>Learn anywhere</span></article></div>
</section>
@endsection
