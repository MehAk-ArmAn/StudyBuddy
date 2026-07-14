@extends('layouts.app')

@section('title', 'StudyBuddy Community Profiles')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">

<main id="main-content" class="sb-user-hub">
    <section class="sb-page-hero">
        <p class="sb-hub-kicker">Community Profiles</p>
        <h1>Discover StudyBuddy learners.</h1>
        <p>Public profiles help learners share their goals, favorite app worlds, and progress style — without direct messaging or pressure.</p>
    </section>

    <section class="community-grid">
        @forelse($profiles as $member)
            @php($profile = is_array($member->role_profile) ? $member->role_profile : (json_decode($member->role_profile ?? '[]', true) ?: []))
            <a href="{{ route('studybuddy.profile.public', $member->id) }}" class="community-card">
                <span>{{ strtoupper(substr($member->name ?? 'S', 0, 1)) }}</span>
                <strong>{{ $member->name }}</strong>
                <p>{{ $profile['headline'] ?? 'StudyBuddy learner' }}</p>
                <small>{{ $profile['current_focus'] ?? $member->learning_stage ?? 'Learning' }}</small>
            </a>
        @empty
            <article class="empty-panel">
                <strong>No public profiles yet</strong>
                <p>Turn on your public profile from Profile Studio to appear here.</p>
                @auth<a href="{{ route('profile') }}">Open Profile Studio</a>@endauth
            </article>
        @endforelse
    </section>
</main>
@endsection
