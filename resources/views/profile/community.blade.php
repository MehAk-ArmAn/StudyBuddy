@extends('layouts.app')

@section('title', 'StudyBuddy Community Profiles')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-profile-customizer.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-profile-customizer.css')) ? filemtime(public_path('assets/css/studybuddy-profile-customizer.css')) : time() }}">

<main id="main-content" class="sb-user-hub">
    <section class="sb-page-hero">
        <p class="sb-hub-kicker">Community Profiles</p>
        <h1>Discover StudyBuddy learners.</h1>
        <p>Public profiles showcase goals, favourite app worlds, profile pictures, badges, colours, and learning style.</p>
    </section>

    <section class="community-grid">
        @forelse($profiles as $member)
            @php
                $profile = is_array($member->role_profile) ? $member->role_profile : (json_decode($member->role_profile ?? '[]', true) ?: []);
                $theme = $profile['profile_theme'] ?? 'cosmic';
                $frame = $profile['profile_frame'] ?? 'none';
                $color = $profile['profile_color'] ?? 'purple';
                $shape = $profile['avatar_shape'] ?? 'rounded';
                $badge = $profile['profile_badge'] ?? 'learning-spark';

                $photoUrl = null;
                if (!empty($member->profile_photo_path)) {
                    $photoUrl = preg_match('/^https?:\/\//i', $member->profile_photo_path)
                        ? $member->profile_photo_path
                        : asset('storage/'.$member->profile_photo_path);
                }
            @endphp

            <a href="{{ route('studybuddy.profile.public', $member->id) }}" class="community-card theme-{{ $theme }} frame-{{ $frame }} color-{{ $color }} shape-{{ $shape }}">
                <span class="community-avatar">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $member->name }} profile picture">
                    @else
                        {{ strtoupper(substr($member->name ?? 'S', 0, 1)) }}
                    @endif
                </span>
                <strong>{{ $member->name }}</strong>
                <p>{{ $profile['headline'] ?? 'StudyBuddy learner' }}</p>
                <small>{{ $customizations['profile_badge'][$badge]['label'] ?? 'Learning Spark' }}</small>
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
