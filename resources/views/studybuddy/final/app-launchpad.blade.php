@extends('layouts.app')

@section('content')
<main class="sb-final-shell" data-sb-final-app>
    <section class="sb-final-hero">
        <div>
            <p class="sb-final-kicker">StudyBuddy App Universe</p>
            <h1>{{ $settings['launchpad_heading'] ?? 'Choose your learning world.' }}</h1>
            <p>{{ $settings['launchpad_intro'] ?? 'Play on web or download the right app version when available.' }}</p>
            <div class="sb-final-actions">
                <a href="{{ route('studybuddy.final.points-wallet') }}" class="sb-final-btn">My Points Wallet</a>
                <a href="{{ route('studybuddy.final.roadmap') }}" class="sb-final-btn sb-final-btn-soft">Platform Roadmap</a>
                <a href="{{ route('studybuddy.final.launch-readiness') }}" class="sb-final-btn sb-final-btn-soft">Launch Readiness</a>
            </div>
        </div>
        <div class="sb-final-orb-card">
            <span>🚀</span>
            <strong>{{ $apps->count() }}</strong>
            <p>mini-app worlds connected to one dashboard</p>
        </div>
    </section>

    <section class="sb-final-toolbar">
        <input type="search" placeholder="Search apps..." data-sb-app-search>
        <select data-sb-app-filter>
            <option value="all">All categories</option>
            @foreach($categories as $category)
                <option value="{{ Str::slug($category) }}">{{ $category }}</option>
            @endforeach
        </select>
    </section>

    <section class="sb-final-grid">
        @foreach($apps as $app)
            <article class="sb-final-app-card" data-app-card data-category="{{ Str::slug($app->category) }}" data-search="{{ Str::lower($app->name.' '.$app->tagline.' '.$app->description.' '.$app->category) }}">
                <div class="sb-final-app-top">
                    <span class="sb-final-app-icon">{{ $app->icon ?: '✨' }}</span>
                    <span class="sb-final-status sb-status-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                </div>
                <h2>{{ $app->name }}</h2>
                <p class="sb-final-tagline">{{ $app->tagline }}</p>
                <p>{{ $app->description }}</p>
                <div class="sb-final-meta">
                    <span>⭐ {{ $app->points_reward }} pts</span>
                    <span>⏱ {{ $app->estimated_minutes }} min</span>
                    <span>{{ $app->category }}</span>
                </div>
                <div class="sb-final-platforms">
                    @if($app->is_web_enabled)
                        <a href="{{ route('studybuddy.final.web-play', $app->slug) }}" class="sb-final-chip is-live">Play Web</a>
                    @else
                        <span class="sb-final-chip">Web soon</span>
                    @endif
                    @foreach(['ios' => 'iOS', 'android' => 'Android', 'windows' => 'Windows', 'mac' => 'Mac'] as $key => $label)
                        @php($url = $app->{$key.'_url'})
                        @if($url)
                            <a href="{{ $url }}" class="sb-final-chip is-live" target="_blank" rel="noopener">{{ $label }}</a>
                        @else
                            <span class="sb-final-chip">{{ $label }} soon</span>
                        @endif
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
</main>
@endsection
