@extends('layouts.app')

@section('title', 'How StudyBuddy Roles Work')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-info-pages.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-info-pages.css')) ? filemtime(public_path('assets/css/studybuddy-info-pages.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-info-pages.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-info-pages.js')) ? filemtime(public_path('assets/js/studybuddy-info-pages.js')) : time() }}" defer></script>

<main id="main-content" class="sb-info-page sb-roles-page">
    <section class="sb-info-hero roles-hero" data-info-card>
        <p class="sb-info-kicker">{{ $pageData['eyebrow'] }}</p>
        <h1>{{ $pageData['title'] }}</h1>
        <p>{{ $pageData['subtitle'] }}</p>
        <div class="sb-info-hero-body">{{ $pageData['body'] }}</div>

        <div class="role-orbit" aria-hidden="true">
            @foreach($roleCards as $role)
                <span>{{ $role['icon'] ?? '✨' }}</span>
            @endforeach
        </div>
    </section>

    <section class="role-tabs" aria-label="Choose a StudyBuddy role">
        @foreach($roleCards as $role)
            <button type="button" data-role-tab="{{ $role['key'] }}" @class(['active' => $loop->first])>
                <span>{{ $role['icon'] ?? '✨' }}</span>
                {{ $role['title'] }}
            </button>
        @endforeach
    </section>

    <section class="role-stage">
        @foreach($roleCards as $role)
            <article class="role-card-panel {{ $loop->first ? 'active' : '' }}" data-role-panel="{{ $role['key'] }}" data-info-card>
                <div class="role-panel-main">
                    <span class="role-big-icon">{{ $role['icon'] ?? '✨' }}</span>
                    <p class="sb-info-kicker">{{ $role['title'] }}</p>
                    <h2>{{ $role['tagline'] }}</h2>
                    <p>{{ $role['best_for'] }}</p>

                    <a href="{{ url($role['cta_url'] ?? '/apps') }}">{{ $role['cta_label'] ?? 'Continue' }}</a>
                </div>

                <div class="role-detail-grid">
                    <article>
                        <span>01</span>
                        <h3>Dashboard</h3>
                        <p>{{ $role['dashboard'] }}</p>
                    </article>

                    <article>
                        <span>02</span>
                        <h3>Controls</h3>
                        <p>{{ $role['controls'] }}</p>
                    </article>

                    <article>
                        <span>03</span>
                        <h3>Safety</h3>
                        <p>{{ $role['safety'] }}</p>
                    </article>
                </div>
            </article>
        @endforeach
    </section>

    <section class="roles-comparison" data-info-card>
        <p class="sb-info-kicker">One system</p>
        <h2>Different experiences, same StudyBuddy universe.</h2>

        <div class="comparison-grid">
            <article><span>🎮</span><strong>Apps</strong><p>Every role can explore learning worlds, but recommendations change by user type.</p></article>
            <article><span>🪄</span><strong>Profiles</strong><p>Users control profile colours, badges, avatar styles, and public visibility.</p></article>
            <article><span>⭐</span><strong>Points</strong><p>Coins can unlock some profile customizations and reward progress.</p></article>
            <article><span>🌍</span><strong>Community</strong><p>Public profiles create a safe showcase space without direct messaging pressure.</p></article>
        </div>
    </section>
</main>
@endsection
