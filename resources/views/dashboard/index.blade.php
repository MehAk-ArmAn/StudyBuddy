@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $photoUrl = null;
    if (!empty($user->profile_photo_path)) {
        $photoUrl = preg_match('/^https?:\/\//i', $user->profile_photo_path)
            ? $user->profile_photo_path
            : asset('storage/'.ltrim($user->profile_photo_path, '/'));
    }

    $displayRole = ucwords(str_replace('_', ' ', $role));
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-role-dashboards.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-role-dashboards.css')) ? filemtime(public_path('assets/css/studybuddy-role-dashboards.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-role-dashboards.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-role-dashboards.js')) ? filemtime(public_path('assets/js/studybuddy-role-dashboards.js')) : time() }}" defer></script>

<div class="sb-role-dashboard role-{{ $role }}">
    <section class="role-dash-hero" data-role-card>
        <div>
            <p class="role-kicker">{{ $displayRole }} Dashboard</p>

            @if($role === 'parent')
                <h1>Guide your child’s learning without taking over.</h1>
                <p>Track connected child accounts, see learning signals, and support progress from one calm parent dashboard.</p>
            @elseif($role === 'teacher')
                <h1>Manage classes, tasks, and quizzes from one teaching space.</h1>
                <p>Create classes, add students, assign app-based tasks, and keep your organization details ready.</p>
            @elseif($role === 'independent_learner')
                <h1>Your self-paced learning cockpit.</h1>
                <p>Build routines, track points, choose app worlds, and turn progress into a personal learning portfolio.</p>
            @else
                <h1>Your learning mission control.</h1>
                <p>Complete tasks, play app worlds, collect points, and grow your StudyBuddy profile step by step.</p>
            @endif

            <div class="role-hero-actions">
                <a href="{{ url('/apps') }}">Open apps</a>
                <a class="soft" href="{{ url('/profile') }}">Profile Studio</a>
                <a class="soft" href="{{ url('/roles') }}">How roles work</a>
            </div>
        </div>

        <aside class="role-passport" data-role-card>
            <div class="role-avatar">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }} profile picture">
                @else
                    <span>{{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}</span>
                @endif
            </div>

            <strong>{{ $user->name }}</strong>
            <p>{{ $profile['headline'] ?? $displayRole.' StudyBuddy account' }}</p>

            <div class="role-passport-stats">
                <article><span>Points</span><b>{{ number_format((int) ($user->cosmic_points ?? 0)) }}</b></article>
                <article><span>Rank</span><b>{{ $rank ? '#'.$rank : 'New' }}</b></article>
                <article><span>Profile</span><b>{{ $completion }}%</b></article>
            </div>

            <div class="role-progress"><i style="width: {{ $completion }}%"></i></div>
        </aside>
    </section>

    @if(session('status'))
        <section class="role-status-good">{{ session('status') }}</section>
    @endif

    @if($errors->any())
        <section class="role-status-error">
            <strong>Fix this first:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    @if($role === 'parent')
        @include('dashboard.partials.parent-dashboard')
    @elseif($role === 'teacher')
        @include('dashboard.partials.teacher-dashboard')
    @elseif($role === 'independent_learner')
        @include('dashboard.partials.independent-dashboard')
    @else
        @include('dashboard.partials.student-dashboard')
    @endif
</div>
@endsection
