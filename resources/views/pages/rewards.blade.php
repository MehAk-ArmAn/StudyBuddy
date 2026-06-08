@extends('layouts.app')

@section('title', 'Rewards')
@section('body_class', 'page-shell page-buddy-rewards')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);

    $shopItems = [
        ['name' => 'Star Cap', 'price' => 100, 'icon' => '🧢', 'tone' => 'blue'],
        ['name' => 'Wizard Hat', 'price' => 150, 'icon' => '🧙‍♂️', 'tone' => 'purple'],
        ['name' => 'Astronaut Helmet', 'price' => 200, 'icon' => '🧑‍🚀', 'tone' => 'cyan'],
        ['name' => 'Galaxy Headband', 'price' => 120, 'icon' => '🌟', 'tone' => 'pink'],
        ['name' => 'Cool Shades', 'price' => 80, 'icon' => '🕶️', 'tone' => 'violet'],
        ['name' => 'Round Glasses', 'price' => 70, 'icon' => '👓', 'tone' => 'gold'],
        ['name' => 'Bow Tie', 'price' => 60, 'icon' => '🎀', 'tone' => 'purple'],
        ['name' => 'Neck Scarf', 'price' => 90, 'icon' => '🧣', 'tone' => 'blue'],
        ['name' => 'Hoodie', 'price' => 180, 'icon' => '🧥', 'tone' => 'purple'],
        ['name' => 'Space Jacket', 'price' => 220, 'icon' => '🧥', 'tone' => 'blue'],
        ['name' => 'Star Aura', 'price' => 150, 'icon' => '💫', 'tone' => 'pink'],
        ['name' => 'Comet Trail', 'price' => 200, 'icon' => '☄️', 'tone' => 'cyan'],
        ['name' => 'Mini Planet Pet', 'price' => 250, 'icon' => '🪐', 'tone' => 'cyan'],
        ['name' => 'Rocket Pet', 'price' => 250, 'icon' => '🚀', 'tone' => 'blue'],
        ['name' => 'Floating Book', 'price' => 200, 'icon' => '📘', 'tone' => 'purple'],
        ['name' => 'Stardust Wings', 'price' => 300, 'icon' => '🦋', 'tone' => 'pink'],
    ];

    $categories = [
        ['label' => 'Hat', 'icon' => '🧙‍♂️'],
        ['label' => 'Glasses', 'icon' => '🕶️'],
        ['label' => 'Accessory', 'icon' => '🎀'],
        ['label' => 'Outfit', 'icon' => '👕'],
        ['label' => 'Aura', 'icon' => '✧'],
        ['label' => 'Pet', 'icon' => '🚀'],
    ];
@endphp

<section class="buddy-shop-page reveal-on-load" aria-labelledby="buddy-shop-title">
    <div class="buddy-shop-scene" aria-hidden="true">
        <img class="buddy-shop-planet-left" src="{{ $asset('planet-ringed-lg.png') }}" alt="">
        <img class="buddy-shop-planet-right" src="{{ $asset('planet-purple-lg.png') }}" alt="">
        <img class="buddy-shop-sparkles" src="{{ $asset('sparkles-pack.png') }}" alt="">
        <span class="buddy-shop-comet"></span>
    </div>

    <div class="buddy-shop-topnav">
        <a class="buddy-shop-brand" href="{{ route('home') }}"><img src="{{ $asset('logo-icon.png') }}" alt="StudyBuddy logo"><strong>Study<span>Buddy</span></strong></a>
        <nav aria-label="Rewards navigation"><a href="{{ route('home') }}">Home</a><a href="{{ route('apps.index') }}">Apps</a><a href="{{ route('for-parents') }}">For Parents</a><a href="{{ route('for-teachers') }}">For Teachers</a><a href="{{ route('pricing') }}">Pricing</a><a href="{{ route('support') }}">Support</a></nav>
        <a class="buddy-signup" href="{{ route('apps.index') }}">Sign Up</a>
    </div>

    <div class="buddy-shop-shell">
        <aside class="buddy-shop-sidebar" aria-label="Buddy customization navigation">
            <a class="buddy-side-brand" href="{{ route('home') }}"><img src="{{ $asset('logo-icon.png') }}" alt=""><strong>StudyBuddy</strong></a>
            <nav>
                <a href="#"><span>⌂</span>Dashboard</a><a href="{{ route('apps.index') }}"><span>▦</span>My Apps</a><a href="#"><span>▣</span>Homework</a><a href="#"><span>⏱</span>Focus Timer</a><a href="#"><span>☑</span>Quizzes</a><a href="#"><span>▤</span>Flashcards</a><a href="#"><span>▥</span>Progress</a><a href="{{ route('rewards') }}"><span>🎁</span>Rewards</a><a class="is-active" href="#"><span>🐬</span>Buddy</a><a href="#"><span>♜</span>Costumes</a><a href="#"><span>⚙</span>Themes</a><a href="#"><span>🏵</span>Badges</a><a href="#"><span>⚙</span>Settings</a>
            </nav>
            <div class="buddy-profile-mini"><span>👦🏽</span><div><strong>Mehak 🌟</strong><small>Level 12</small><i><b style="width:58%"></b></i><em>2,350 / 4,000 XP</em></div></div>
            <a class="buddy-profile-button" href="#">View Profile</a>
        </aside>

        <main class="buddy-customizer-pro">
            <header class="buddy-shop-header">
                <div><h1 id="buddy-shop-title">Customize Your <span>Buddy</span></h1><p>Make your study buddy uniquely yours! Earn coins, unlock awesome items, and show off your style.</p></div>
                <div class="buddy-economy-row"><div class="buddy-coin-balance"><span>🪙</span><strong>320</strong><small>Buddy Coins</small><button type="button">＋</button></div><div class="buddy-daily-bonus"><span>🎁</span><div><small>Daily Bonus</small><strong>+20</strong></div><time>07:45:12</time></div></div>
            </header>

            <section class="buddy-preview-area" aria-label="Buddy preview">
                <div class="buddy-preview-stage">
                    <span class="buddy-wizard-hat">✦</span>
                    <span class="buddy-backpack"></span>
                    <img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin buddy preview">
                    <span class="buddy-preview-platform"></span>
                </div>
                <div class="buddy-category-row" aria-label="Selected categories">
                    @foreach($categories as $index => $category)
                        <button class="{{ $index === 0 ? 'is-active' : '' }}" type="button"><span>{{ $category['icon'] }}</span>{{ $category['label'] }}</button>
                    @endforeach
                </div>
                <div class="buddy-action-row"><button class="buddy-reset" type="button">⟳ Reset</button><button class="buddy-save" type="button">✦ Save Changes</button></div>
            </section>

            <section class="buddy-items-area" aria-labelledby="buddy-items-title">
                <h2 id="buddy-items-title" class="sr-only">Buddy customization items</h2>
                <div class="buddy-filter-tabs" role="tablist" aria-label="Item filters"><button class="is-active" type="button">All</button><button type="button">Hats</button><button type="button">Glasses</button><button type="button">Accessories</button><button type="button">Outfits</button><button type="button">Stars</button><button type="button">Themed</button></div>
                <div class="buddy-item-grid">
                    @foreach($shopItems as $item)
                        <article class="buddy-item-card item-{{ $item['tone'] }}"><span>{{ $item['icon'] }}</span><h3>{{ $item['name'] }}</h3><p>🪙 {{ $item['price'] }}</p></article>
                    @endforeach
                </div>
                <aside class="buddy-earn-card"><span>🏅</span><p>Earn Buddy Coins by completing lessons, quizzes, and daily goals!</p><a href="#">How to Earn</a></aside>
            </section>
        </main>
    </div>
</section>
@endsection
