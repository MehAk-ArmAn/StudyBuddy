@extends('layouts.app')

@section('title', 'Rewards')
@section('body_class', 'page-shell page-buddy-rewards')

@section('content')
@php
    $asset = fn (string $file): string => asset('assets/studybuddy/' . $file);
    $shopItems = ($rewards ?? \App\Support\Cms::rewards())->map(fn ($reward) => [
        'name' => $reward->name,
        'price' => $reward->points_required,
        'image_path' => $reward->image_path,
        'tone' => $reward->category === 'Glasses' ? 'gold' : ($reward->category === 'Outfits' ? 'blue' : 'purple'),
    ])->values();
    $categories = collect(['Hat', 'Glasses', 'Accessory', 'Outfit', 'Aura', 'Pet'])->map(fn ($label) => ['label' => $label]);
@endphpp

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
                <a href="#"><span class="sb-nav-dot"></span>Dashboard</a><a href="{{ route('apps.index') }}"><span class="sb-nav-dot"></span>My Apps</a><a href="#"><span class="sb-nav-dot"></span>Homework</a><a href="#"><span class="sb-nav-dot"></span>Focus Timer</a><a href="#"><span class="sb-nav-dot"></span>Quizzes</a><a href="#"><span class="sb-nav-dot"></span>Flashcards</a><a href="#"><span class="sb-nav-dot"></span>Progress</a><a href="{{ route('rewards') }}"><span class="sb-nav-dot"></span>Rewards</a><a class="is-active" href="#"><span class="sb-nav-dot"></span>Buddy</a><a href="#"><span class="sb-nav-dot"></span>Costumes</a><a href="#"><span class="sb-nav-dot"></span>Themes</a><a href="#"><span class="sb-nav-dot"></span>Badges</a><a href="#"><span class="sb-nav-dot"></span>Settings</a>
            </nav>
            <div class="buddy-profile-mini"><span class="sb-avatar-dot"></span><div><strong>Mehak</strong><small>Level 12</small><i><b style="width:58%"></b></i><em>2,350 / 4,000 XP</em></div></div>
            <a class="buddy-profile-button" href="#">View Profile</a>
        </aside>

        <main class="buddy-customizer-pro">
            <header class="buddy-shop-header">
                <div><h1 id="buddy-shop-title">{{ \App\Support\Cms::text('rewards', 'hero', 'title', 'Customize Your Buddy') }}</h1><p>{{ \App\Support\Cms::text('rewards', 'hero', 'subtitle', 'Make your study buddy uniquely yours! Earn coins, unlock awesome items, and show off your style.') }}</p></div>
                <div class="buddy-economy-row"><div class="buddy-coin-balance"><span class="sb-coin-dot"></span><strong>320</strong><small>Buddy Coins</small><button type="button">+</button></div><div class="buddy-daily-bonus"><span class="sb-gift-dot"></span><div><small>Daily Bonus</small><strong>+20</strong></div><time>07:45:12</time></div></div>
            </header>

            <section class="buddy-preview-area" aria-label="Buddy preview">
                <div class="buddy-preview-stage">
                    <span class="buddy-wizard-hat"></span>
                    <span class="buddy-backpack"></span>
                    <img src="{{ $asset('hero-dolphin-book.png') }}" alt="StudyBuddy dolphin buddy preview">
                    <span class="buddy-preview-platform"></span>
                </div>
                <div class="buddy-category-row" aria-label="Selected categories">
                    @foreach($categories as $index => $category)
                        <button class="{{ $index === 0 ? 'is-active' : '' }}" type="button"><span class="sb-category-dot"></span>{{ $category['label'] }}</button>
                    @endforeach
                </div>
                <div class="buddy-action-row"><button class="buddy-reset" type="button">{{ \App\Support\Cms::text('rewards', 'hero', 'reset', 'Reset') }}</button><button class="buddy-save" type="button">{{ \App\Support\Cms::text('rewards', 'hero', 'save', 'Save Changes') }}</button></div>
            </section>

            <section class="buddy-items-area" aria-labelledby="buddy-items-title">
                <h2 id="buddy-items-title" class="sr-only">Buddy customization items</h2>
                <div class="buddy-filter-tabs" role="tablist" aria-label="Item filters"><button class="is-active" type="button">All</button><button type="button">Hats</button><button type="button">Glasses</button><button type="button">Accessories</button><button type="button">Outfits</button><button type="button">Stars</button><button type="button">Themed</button></div>
                <div class="buddy-item-grid">
                    @foreach($shopItems as $item)
                        <article class="buddy-item-card item-{{ $item['tone'] }}"><span>@include('partials.cms-image', ['path' => $item['image_path'] ?? null, 'alt' => $item['name']])</span><h3>{{ $item['name'] }}</h3><p><span class="sb-coin-dot"></span> {{ $item['price'] }}</p></article>
                    @endforeach
                </div>
                <aside class="buddy-earn-card"><span class="sb-badge-dot"></span><p>Earn Buddy Coins by completing lessons, quizzes, and daily goals!</p><a href="#">How to Earn</a></aside>
            </section>
        </main>
    </div>
</section>
@endsection
