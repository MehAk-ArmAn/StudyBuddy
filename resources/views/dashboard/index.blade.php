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

    // Point rows and assignments carry raw database timestamps. Nobody reading
    // a dashboard wants "2026-07-15 09:07:10".
    $humanDate = function ($value): string {
        if (blank($value)) {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->diffForHumans();
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-role-dashboards.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-role-dashboards.css')) ? filemtime(public_path('assets/css/studybuddy-role-dashboards.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-role-dashboards.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-role-dashboards.js')) ? filemtime(public_path('assets/js/studybuddy-role-dashboards.js')) : time() }}" defer></script>

<div class="sb-role-dashboard role-{{ $role }}">
    <section class="role-dash-hero" data-role-card>
        <div>
            <p class="role-kicker">{{ $displayRole }} Dashboard</p>

            @if($role === 'parent')
                <h1>See how your child is getting on.</h1>
                <p>Connect a child with their code, then follow their progress without taking over.</p>
            @elseif($role === 'teacher')
                <h1>Your classes in one place.</h1>
                <p>Set up a class, add students with their code, and assign work from the apps.</p>
            @elseif($role === 'independent_learner')
                <h1>Learning at your own pace.</h1>
                <p>Set a focus, pick an app, and keep everything you have done in one place.</p>
            @else
                <h1>Pick something to practise.</h1>
                <p>Your apps, tasks and points are all here. Start with whatever looks good today.</p>
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
