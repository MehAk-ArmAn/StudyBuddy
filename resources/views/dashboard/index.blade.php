@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
@endpush

@section('content')
@include('dashboard.partials.command-center-link')
@php
    $selectedTheme = old('avatar_style', $currentTheme ?? $user->avatar_style ?? 'cosmic-dolphin');
@endphp

<section class="dash-wrap role-{{ $role }}" aria-labelledby="dashboard-title">
    <div class="dash-hero auth-panel">
        <div class="dash-hero-copy">
            <p class="eyebrow">{{ $roleEyebrow }}</p>
            <h1 id="dashboard-title">{{ $roleLabel }}</h1>
            <p>Hello {{ $user->name }} — {{ $dashboardIntro }}</p>

            @if(session('status'))
                <div class="auth-success inline-status" role="status">{{ session('status') }}</div>
            @endif

            <div class="dash-hero-actions" aria-label="Dashboard quick actions">
                @foreach(array_slice($quickActions, 0, 2) as [$label, $url])
                    <a class="btn {{ $loop->first ? '' : 'btn-ghost' }}" href="{{ $url }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="dash-hero-art">
            <img src="{{ asset($heroImage) }}" alt="{{ $roleLabel }} illustration">
        </div>
    </div>

    @if($errors->any())
        <div class="auth-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="dash-grid" aria-label="Dashboard metrics">
        @foreach($metrics as [$label, $value, $note])
            <article class="auth-panel metric-card">
                <span class="metric-orb" aria-hidden="true"></span>
                <strong>{{ $value }}</strong>
                <p>{{ $label }}</p>
                <small>{{ $note }}</small>
            </article>
        @endforeach
    </div>

    <div class="role-control-grid" aria-label="Role controls">
        @foreach($controlPanels as [$title, $body, $image, $url, $button])
            <article class="auth-panel control-panel-card">
                <div class="control-image-wrap">
                    <img src="{{ asset($image) }}" alt="{{ $title }} illustration">
                </div>
                <div>
                    <h2>{{ $title }}</h2>
                    <p>{{ $body }}</p>
                    <a class="control-link" href="{{ $url }}">{{ $button }} <span>→</span></a>
                </div>
            </article>
        @endforeach
    </div>

    <div class="dash-columns">
        <article class="auth-panel">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Today</p>
                    <h2>Role-specific plan</h2>
                </div>
                <span class="soft-pill">Clear steps</span>
            </div>
            <ul class="mission-list">
                @foreach($missions as $mission)
                    <li><span aria-hidden="true">✓</span>{{ $mission }}</li>
                @endforeach
            </ul>
        </article>

        <article class="auth-panel">
            <div class="panel-head">
                <div>
                    <p class="eyebrow">Controls</p>
                    <h2>Your tools</h2>
                </div>
                <span class="soft-pill">Fast access</span>
            </div>
            <div class="action-list">
                @foreach($quickActions as [$label, $url])
                    <a href="{{ $url }}">{{ $label }} <span>→</span></a>
                @endforeach
            </div>
        </article>
    </div>

    <div class="learning-card-grid" aria-label="Focus zones">
        @foreach($focusZones as $zone)
            <article class="auth-panel learning-card">
                <span class="learning-dot" aria-hidden="true"></span>
                <h3>{{ $zone }}</h3>
                <p>Keep this area clear, calm, and easy to access from your dashboard.</p>
            </article>
        @endforeach
    </div>

    <div class="dash-columns">
        <form class="auth-panel auth-form compact" method="POST" action="{{ route('dashboard.profile.update') }}">
            @csrf
            @method('PUT')
            <div>
                <p class="eyebrow">Profile</p>
                <h2>Personalize your space</h2>
                <p class="soft-copy">Changing your role and theme reshapes your controls, colors, cards, and learning vibe across StudyBuddy.</p>
            </div>

            <label>Name
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>

            <label>Role
                <select name="role" required>
                    @foreach(['student'=>'Student','parent'=>'Parent','teacher'=>'Teacher','independent_learner'=>'Independent Learner'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $role) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <label>Learning stage or focus
                <input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}" placeholder="Example: Primary 4, family routine, Year 8 class">
            </label>

            <label>Dashboard style
                <select name="avatar_style" required data-theme-select>
                    @foreach($themeOptions as $theme)
                        <option value="{{ $theme['slug'] }}" @selected($selectedTheme === $theme['slug'])>
                            {{ $theme['label'] }} — {{ $theme['description'] }}
                        </option>
                    @endforeach
                </select>
            </label>

            <div class="theme-preview-grid" data-theme-picker aria-label="Theme preview options">
                @foreach($themeOptions as $theme)
                    <button
                        type="button"
                        class="theme-preview-card {{ $selectedTheme === $theme['slug'] ? 'active' : '' }}"
                        data-theme-choice="{{ $theme['slug'] }}"
                    >
                        <img src="{{ asset($theme['image']) }}" alt="{{ $theme['label'] }} theme preview">
                        <strong>{{ $theme['label'] }}</strong>
                        <span>{{ $theme['description'] }}</span>
                    </button>
                @endforeach
            </div>

            <button class="btn" type="submit">Save profile</button>
        </form>

        <form class="auth-panel auth-form compact" method="POST" action="{{ route('dashboard.password.update') }}">
            @csrf
            @method('PUT')
            <div>
                <p class="eyebrow">Security</p>
                <h2>Update access key</h2>
                <p class="soft-copy">Use at least 8 characters. Keep your account safe.</p>
            </div>

            <label>Current access key
                <input type="password" name="current_password" autocomplete="current-password" required>
            </label>

            <label>New access key
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <label>Confirm new access key
                <input type="password" name="password_confirmation" autocomplete="new-password" required>
            </label>

            <button class="btn btn-ghost" type="submit">Update key</button>
        </form>
    </div>

    <article class="auth-panel resource-shelf">
        <div>
            <p class="eyebrow">Saved for this role</p>
            <h2>Resource shelf</h2>
            <p>These links change depending on whether the account is a learner, family account, teacher, or independent learner.</p>
        </div>
        <div class="action-list shelf-actions">
            @foreach($resourceShelf as [$label, $url])
                <a href="{{ $url }}">{{ $label }} <span>→</span></a>
            @endforeach
        </div>
    </article>
</section>

@include('dashboard.partials.phase5-experience-links')


@include('dashboard.partials.phase6-final-links')
@endsection

@includeIf('admin.studybuddy.content-studio.partials.admin-shortcut')
