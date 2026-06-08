@extends('layouts.app')

@section('title', 'StudyBuddy Mobile App')
@section('body_class', 'page-shell page-mobile-preview')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);
@endphp

<section class="mobile-preview-page reveal-on-load" aria-labelledby="mobile-preview-title">
    <div class="mobile-preview-scene" aria-hidden="true">
        <img class="mobile-preview-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt="">
        <img class="mobile-preview-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt="">
        <img class="mobile-preview-sparkles" src="{{ $asset('sparkles-pack.png') }}" alt="">
        <span class="mobile-preview-comet"></span>
    </div>

    <header class="mobile-preview-header">
        <a class="mobile-preview-brand" href="{{ route('home') }}"><img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo"><strong>Study<span>Buddy</span></strong></a>
        <div class="mobile-preview-nav"><a href="{{ route('apps.index') }}">Apps</a><a href="{{ route('rewards') }}">Rewards</a><a href="{{ route('showcase') }}">Showcase</a></div>
    </header>

    <main class="mobile-preview-shell">
        <section class="mobile-preview-copy" aria-labelledby="mobile-preview-title">
            <div class="mobile-preview-kicker">✨ StudyBuddy mobile app</div>
            <h1 id="mobile-preview-title">Learn. Play. Grow. <span>Anywhere.</span></h1>
            <p>Take Math Quest, rewards, focus missions, and your buddy with you on every device. A magical learning universe made for quick practice and daily wins.</p>
            <div class="mobile-store-buttons" aria-label="Download StudyBuddy apps">
                <a class="store-button google" href="#"><span></span><strong><small>GET IT ON</small>Google Play</strong></a>
                <a class="store-button apple" href="#"><span></span><strong><small>Download on the</small>App Store</strong></a>
            </div>
            <div class="mobile-feature-list">
                <article><span>🎮</span><div><strong>Practice in minutes</strong><small>Mini lessons, quests, quizzes, and flashcards.</small></div></article>
                <article><span>⭐</span><div><strong>Earn rewards</strong><small>Collect stars, badges, coins, streaks, and buddy items.</small></div></article>
                <article><span>☁</span><div><strong>Sync anywhere</strong><small>Continue learning across web, tablet, and phone.</small></div></article>
            </div>
        </section>

        <section class="mobile-phone-stage" aria-label="StudyBuddy app phone previews">
            <div class="phone-mockup phone-mockup-left">
                <div class="phone-glass"></div>
                <div class="phone-notch"></div>
                <div class="phone-screen-content home-screen">
                    <div class="phone-status"><span>9:41</span><i></i></div>
                    <header><img src="{{ $asset('logo-icon.png') }}" alt=""><div><small>Welcome back</small><strong>Hi Mehak! 👋</strong></div></header>
                    <section class="phone-level-card"><span>⭐</span><div><small>Level</small><strong>12</strong></div><div><small>Coins</small><strong>320</strong></div></section>
                    <div class="phone-mission-list"><h3>Today’s Mission</h3><p><span>➕</span>Complete 2 Math Quest lessons <b>1/2</b></p><p><span>📚</span>Read a story in Reading Garden <b>0/1</b></p><p><span>🔥</span>Keep your streak alive <b>5</b></p></div>
                    <nav><span></span><span></span><span></span><span></span></nav>
                </div>
            </div>

            <div class="phone-mockup phone-mockup-center">
                <div class="phone-glass"></div>
                <div class="phone-notch"></div>
                <div class="phone-screen-content quest-screen">
                    <div class="phone-status"><span>9:41</span><i></i></div>
                    <a href="{{ route('apps.math-quest') }}">‹ Math Quest</a>
                    <img src="{{ $asset('app-math-quest.png') }}" alt="Math Quest app icon">
                    <h2>Choose Mode</h2>
                    <div class="quest-choice active"><span>⚡</span><div><strong>Practice</strong><small>Learn at your own pace</small></div></div>
                    <div class="quest-choice"><span>🔥</span><div><strong>Challenge</strong><small>Test your skills</small></div></div>
                    <div class="quest-choice"><span>🛡</span><div><strong>Time Attack</strong><small>Beat the clock</small></div></div>
                    <button type="button">Continue</button>
                </div>
            </div>

            <div class="phone-mockup phone-mockup-right">
                <div class="phone-glass"></div>
                <div class="phone-notch"></div>
                <div class="phone-screen-content rewards-screen">
                    <div class="phone-status"><span>9:41</span><i></i></div>
                    <h2>Rewards</h2>
                    <div class="reward-balance"><span>🏆</span><div><small>Class 12</small><strong>Star Learner</strong></div></div>
                    <h3>Badges</h3>
                    <div class="reward-badges"><span>📖</span><span>➕</span><span>🏆</span><span>⭐</span><span>💎</span><span>🛡</span></div>
                    <h3>Buddy Items</h3>
                    <div class="reward-items"><span>🎩</span><span>🕶️</span><span>🧣</span></div>
                </div>
            </div>

            <div class="mobile-dolphin-cloud" aria-hidden="true"><img src="{{ $asset('hero-dolphin-book.png') }}" alt=""><span></span><span></span><span></span></div>
        </section>
    </main>

    <section class="mobile-bottom-strip" aria-label="StudyBuddy mobile features">
        <article><span>📱</span><strong>Mobile-first practice</strong><small>Quick daily sessions</small></article>
        <article><span>🧠</span><strong>Smart progress</strong><small>Track skills and streaks</small></article>
        <article><span>🎁</span><strong>Buddy rewards</strong><small>Unlock cosmic items</small></article>
        <article><span>🔒</span><strong>Safe for learners</strong><small>Kid-friendly experience</small></article>
    </section>
</section>
@endsection
