@extends('layouts.app')

@section('title', 'StudyBuddy Community Profiles')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-profile-customizer.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-profile-customizer.css')) ? filemtime(public_path('assets/css/studybuddy-profile-customizer.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-living-platform.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-living-platform.css')) ? filemtime(public_path('assets/css/studybuddy-living-platform.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-living-platform.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-living-platform.js')) ? filemtime(public_path('assets/js/studybuddy-living-platform.js')) : time() }}" defer></script>

<div class="sb-user-hub sb-community-living">
    <section class="sb-page-hero">
        <p class="sb-hub-kicker">Community Profiles</p>
        <h1>Discover StudyBuddy learners.</h1>
        <p>Search by name, role, theme, focus, or badge. Public profiles show learner style without direct messaging.</p>
    </section>

    <section class="community-filter-panel">
        <label>
            <span>Search</span>
            <input type="search" placeholder="Search learners, focus, badge..." data-community-search>
        </label>

        <label>
            <span>Role</span>
            <select data-community-role>
                <option value="all">All roles</option>
                <option value="student">Students</option>
                <option value="parent">Parents</option>
                <option value="teacher">Teachers</option>
                <option value="independent_learner">Independent learners</option>
            </select>
        </label>

        <label>
            <span>Theme</span>
            <select data-community-theme>
                <option value="all">All themes</option>
                <option value="cosmic">Cosmic</option>
                <option value="ocean">Ocean</option>
                <option value="forest">Forest</option>
                <option value="sunrise">Sunrise</option>
                <option value="rose">Rose</option>
                <option value="neon">Neon</option>
            </select>
        </label>
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
                $badgeLabel = $customizations['profile_badge'][$badge]['label'] ?? 'Learning Spark';

                $photoUrl = null;
                if (!empty($member->profile_photo_path)) {
                    $photoUrl = preg_match('/^https?:\/\//i', $member->profile_photo_path)
                        ? $member->profile_photo_path
                        : asset('storage/'.$member->profile_photo_path);
                }

                $searchText = strtolower($member->name.' '.($member->role ?? '').' '.($profile['headline'] ?? '').' '.($profile['current_focus'] ?? '').' '.$badgeLabel.' '.$theme);
            @endphp

            <a href="{{ route('studybuddy.profile.public', $member->id) }}"
               class="community-card theme-{{ $theme }} frame-{{ $frame }} color-{{ $color }} shape-{{ $shape }}"
               data-community-card
               data-role="{{ $member->role ?? 'student' }}"
               data-theme="{{ $theme }}"
               data-search="{{ $searchText }}">
                <span class="community-avatar">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $member->name }} profile picture">
                    @else
                        {{ strtoupper(substr($member->name ?? 'S', 0, 1)) }}
                    @endif
                </span>
                <strong>{{ $member->name }}</strong>
                <p>{{ $profile['headline'] ?? 'StudyBuddy learner' }}</p>
                <small>{{ $badgeLabel }}</small>
            </a>
        @empty
            <article class="empty-panel">
                <strong>No public profiles yet</strong>
                <p>Turn on your public profile from Profile Studio to appear here.</p>
                @auth<a href="{{ route('profile') }}">Open Profile Studio</a>@endauth
            </article>
        @endforelse
    </section>

    <p class="community-empty" data-community-empty hidden>No profiles match that search yet.</p>
</div>
@endsection
