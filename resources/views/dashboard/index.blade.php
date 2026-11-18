@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-wave10-features.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/css/studybuddy-wave10-features.css') }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-learning-snapshot.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/css/studybuddy-learning-snapshot.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-class-progress.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/css/studybuddy-class-progress.css') }}">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-p07/dashboard.css') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-p07/dashboard.css') }}">
@endpush
@push('scripts')
<script defer src="{{ asset('assets/studybuddy-p07/dashboard.js') }}?v={{ \App\Support\StudyBuddyAssetVersion::for('assets/studybuddy-p07/dashboard.js') }}"></script>
@endpush
@section('content')
<!-- SB_ADAPTIVE_DASHBOARD_V1 -->
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-adaptive-dashboard.css') }}?v={{ filemtime(public_path('assets/css/studybuddy-adaptive-dashboard.css')) }}">

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

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-role-dashboards.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-role-dashboards.css')) ? \App\Support\StudyBuddyAssetVersion::for('assets/css/studybuddy-role-dashboards.css') : time() }}">
<script src="{{ asset('assets/js/studybuddy-role-dashboards.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-role-dashboards.js')) ? \App\Support\StudyBuddyAssetVersion::for('assets/js/studybuddy-role-dashboards.js') : time() }}" defer></script>

<div class="sb-role-dashboard role-{{ $role }}">
    @include('dashboard.partials.p07-adaptive-command')
    @include('dashboard.partials.p08-onboarding-card')
    @include('dashboard.partials.p09-pulse-card')
    @if($learningSnapshot !== null)
        @include('dashboard.partials.studybuddy-learning-snapshot', ['snapshot' => $learningSnapshot])
    @endif

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

    <div id="sbp07-workspace" class="sbp07-tools-head">
        <div><p class="sbp07-eyebrow">WORKSPACE TOOLS</p><h2>Everything you can do, in one place.</h2><p>Your existing forms, class tools, and learning records are right here.</p></div>
        <label data-sbp07-density>Display <select aria-label="Dashboard display density"><option value="comfortable">Comfortable</option><option value="compact">Compact</option></select></label>
    </div>
    @if($role === 'parent')
        @include('dashboard.partials.parent-dashboard')
    @elseif($role === 'teacher')
        @include('dashboard.partials.teacher-dashboard')
    @elseif($role === 'independent_learner')
        @include('dashboard.partials.independent-dashboard')
    @else
        @include('dashboard.partials.student-dashboard')
    @endif
    <details class="sbp07-extras" id="sbp07-explore">
        <summary>Explore more learning shortcuts and your journey</summary>
        <div class="sbp07-extras__inside">
            @include('dashboard.partials.studybuddy-wave10-quick-actions')
            @include('dashboard.partials.studybuddy-w36-hub')
            @include('dashboard.partials.studybuddy-w24-journey')
        </div>
    </details>

</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w30/activity.css') }}?v=30">
@endpush
{{-- STUDYBUDDY_W30_ACTIVITY_CARD --}}
@include('dashboard.partials.studybuddy-w30-activity-card')

@endsection
