@extends('layouts.app')

@section('title', $profileUser->name.' | StudyBuddy Profile')

@section('content')
@php
    $theme = $profile['profile_theme'] ?? 'cosmic';
    $themeClass = 'theme-'.$theme;

    $assetUrl = function ($path) {
        if (!$path) return asset('assets/studybuddy-imgs/brand/logo-icon.png');
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        $clean = ltrim($path, '/');
        return file_exists(public_path($clean)) ? asset($clean) : asset('assets/studybuddy-imgs/brand/logo-icon.png');
    };
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">

<main id="main-content" class="sb-public-profile {{ $themeClass }}">
    <section class="public-profile-hero">
        <div class="public-avatar"><span>{{ strtoupper(substr($profileUser->name ?? 'S', 0, 1)) }}</span></div>
        <p class="sb-hub-kicker">StudyBuddy Profile</p>
        <h1>{{ $profileUser->name }}</h1>
        <p>{{ $profile['headline'] ?? 'Learning with StudyBuddy' }}</p>

        @if(!($profile['public_profile_enabled'] ?? false) && $isOwner)
            <div class="private-note">This profile is private. Turn on public profile in Profile Studio when you are ready.</div>
        @endif

        <div class="public-profile-stats">
            @if($profile['show_role'] ?? false)<article><span>Role</span><strong>{{ ucwords(str_replace('_', ' ', $profileUser->role ?? 'learner')) }}</strong></article>@endif
            @if($profile['show_points'] ?? false)<article><span>Points</span><strong>{{ number_format((int) ($profileUser->cosmic_points ?? 0)) }}</strong></article>@endif
            <article><span>Focus</span><strong>{{ $profile['current_focus'] ?? $profileUser->learning_stage ?? 'Learning' }}</strong></article>
        </div>
    </section>

    <section class="public-profile-grid">
        <article>
            <p class="sb-hub-kicker">About</p>
            <h2>Learning vibe</h2>
            <p>{{ $profile['bio'] ?? 'This learner is building their StudyBuddy profile.' }}</p>
        </article>

        <article>
            <p class="sb-hub-kicker">Goals</p>
            <h2>Current mission</h2>
            <p>{{ $profile['learning_goal'] ?? 'Building confidence one small step at a time.' }}</p>
        </article>

        <article>
            <p class="sb-hub-kicker">Subjects</p>
            <h2>Favorites</h2>
            <p>{{ $profile['favorite_subjects'] ?? 'Exploring different learning worlds.' }}</p>
        </article>
    </section>

    <section class="public-profile-apps">
        <div class="section-title">
            <p class="sb-hub-kicker">Favorite worlds</p>
            <h2>App showcase</h2>
        </div>

        <div class="mini-app-grid">
            @foreach($apps as $app)
                <a href="{{ url('/apps/'.$app->slug) }}" class="mini-app-card">
                    <img src="{{ $assetUrl($app->hero_image ?? $app->image_path ?? null) }}" alt="{{ $app->name }} artwork">
                    <span>{{ $app->icon ?? '✨' }}</span>
                    <strong>{{ $app->name }}</strong>
                    <small>{{ $app->category }}</small>
                </a>
            @endforeach
        </div>
    </section>
</main>
@endsection
