@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
<section class="dash-wrap">
    <div class="dash-hero auth-panel">
        <div>
            <p class="eyebrow">{{ ucfirst($role) }} dashboard</p>
            <h1>Welcome back, {{ $user->name }} ✨</h1>
            <p>Your StudyBuddy space is calm, readable, and built around what you need next.</p>
        </div>
        <div class="dash-avatar">🐬</div>
    </div>

    @if(session('status'))
        <div class="notice auth-notice">{{ session('status') }}</div>
    @endif

    <div class="dash-grid">
        @foreach($metrics as [$label, $value, $icon])
            <article class="auth-panel metric-card">
                <span>{{ $icon }}</span>
                <strong>{{ $value }}</strong>
                <p>{{ $label }}</p>
            </article>
        @endforeach
    </div>

    <div class="dash-columns">
        <article class="auth-panel">
            <h2>Today’s gentle plan</h2>
            <ul class="mission-list">
                @foreach($missions as $mission)
                    <li><span>✓</span>{{ $mission }}</li>
                @endforeach
            </ul>
        </article>

        <article class="auth-panel">
            <h2>Quick actions</h2>
            <div class="action-list">
                @foreach($quickActions as [$label, $url])
                    <a href="{{ $url }}">{{ $label }} →</a>
                @endforeach
            </div>
        </article>
    </div>

    <form class="auth-panel auth-form compact" method="POST" action="{{ route('dashboard.profile.update') }}">
        @csrf
        @method('PUT')
        <h2>Profile</h2>
        <label>Name <input name="name" value="{{ old('name', $user->name) }}" required></label>
        <label>Role
            <select name="role" required>
                @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','professional'=>'Professional'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Learning stage <input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}"></label>
        <label>Buddy style <input name="avatar_style" value="{{ old('avatar_style', $user->avatar_style) }}"></label>
        <button class="btn" type="submit">Save profile</button>
    </form>
</section>
@endsection
