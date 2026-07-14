@extends('layouts.app')

@section('title', 'Edit StudyBuddy Profile')

@section('content')
@php
    $selectedApps = collect($profile['favorite_app_slugs'] ?? []);
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-dashboard-system.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-dashboard-system.js')) ? filemtime(public_path('assets/js/studybuddy-dashboard-system.js')) : time() }}" defer></script>

<main id="main-content" class="sb-user-hub">
    <section class="sb-page-hero compact">
        <p class="sb-hub-kicker">Profile Studio</p>
        <h1>Control your StudyBuddy identity.</h1>
        <p>Choose what shows on your profile, what stays private, and which app worlds represent your learning style.</p>
    </section>

    <form method="POST" action="{{ route('profile.update') }}" class="profile-studio">
        @csrf
        @method('PATCH')

        @if(session('status'))
            <div class="success-box">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">
                <strong>Fix these first:</strong>
                <ul>
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <section class="profile-form-card">
            <div class="section-title">
                <p class="sb-hub-kicker">Basics</p>
                <h2>Account display</h2>
            </div>

            <div class="profile-form-grid">
                <label><span>Display name</span><input name="name" value="{{ old('name', $user->name) }}" required></label>
                <label><span>Real name</span><input name="real_name" value="{{ old('real_name', $user->real_name) }}"></label>
                <label><span>Email</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
                <label><span>Country</span><input name="country" value="{{ old('country', $user->country) }}" placeholder="Example: UAE"></label>
                <label><span>Learning focus</span><input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}" placeholder="Math, reading, focus..."></label>
                <label>
                    <span>Avatar style</span>
                    <select name="avatar_style">
                        @foreach(['dolphin-cadet' => 'Dolphin Cadet', 'cosmic-explorer' => 'Cosmic Explorer', 'parent-guide' => 'Parent Guide', 'teacher-mentor' => 'Teacher Mentor', 'star-builder' => 'Star Builder'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('avatar_style', $user->avatar_style) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <p class="sb-hub-kicker">Public profile</p>
                <h2>Your showcase</h2>
            </div>

            <div class="profile-form-grid">
                <label><span>Headline</span><input name="headline" value="{{ old('headline', $profile['headline'] ?? '') }}" placeholder="Example: Focus queen building daily wins"></label>
                <label><span>Profile mood</span><input name="profile_mood" value="{{ old('profile_mood', $profile['profile_mood'] ?? '') }}" placeholder="Calm, creative, exam mode..."></label>
                <label class="full"><span>Bio</span><textarea name="bio" rows="4" placeholder="Tell people what you are learning and what motivates you.">{{ old('bio', $profile['bio'] ?? '') }}</textarea></label>
                <label><span>Favorite subjects</span><input name="favorite_subjects" value="{{ old('favorite_subjects', $profile['favorite_subjects'] ?? '') }}" placeholder="Math, reading, spelling..."></label>
                <label><span>Learning goal</span><input name="learning_goal" value="{{ old('learning_goal', $profile['learning_goal'] ?? '') }}" placeholder="Build confidence, revise daily..."></label>
                <label><span>Current focus</span><input name="current_focus" value="{{ old('current_focus', $profile['current_focus'] ?? '') }}" placeholder="Mental maths, quizzes, focus blocks..."></label>
                <label>
                    <span>Profile theme</span>
                    <select name="profile_theme">
                        @foreach(['cosmic' => 'Cosmic Purple', 'ocean' => 'Ocean Blue', 'forest' => 'Focus Forest', 'sunrise' => 'Planner Sunrise', 'rose' => 'Spelling Rose'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('profile_theme', $profile['profile_theme'] ?? 'cosmic') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <p class="sb-hub-kicker">Favorite apps</p>
                <h2>Choose your worlds</h2>
            </div>

            <div class="favorite-app-picker">
                @foreach($apps as $app)
                    <label>
                        <input type="checkbox" name="favorite_app_slugs[]" value="{{ $app->slug }}" @checked($selectedApps->contains($app->slug))>
                        <span>{{ $app->icon ?? '✨' }}</span>
                        <strong>{{ $app->name }}</strong>
                        <small>{{ $app->category }}</small>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <p class="sb-hub-kicker">Visibility</p>
                <h2>Privacy controls</h2>
            </div>

            <div class="visibility-grid">
                <label><input type="checkbox" name="public_profile_enabled" value="1" @checked(old('public_profile_enabled', $profile['public_profile_enabled'] ?? false))><span>Make my profile public</span></label>
                <label><input type="checkbox" name="show_points" value="1" @checked(old('show_points', $profile['show_points'] ?? false))><span>Show my points</span></label>
                <label><input type="checkbox" name="show_role" value="1" @checked(old('show_role', $profile['show_role'] ?? false))><span>Show my role</span></label>
            </div>

            <div class="profile-actions">
                <button type="submit">Save profile</button>
                <a href="{{ route('studybuddy.profile.public', $user->id) }}">Preview public page</a>
                <a class="soft" href="{{ route('dashboard') }}">Back to dashboard</a>
            </div>
        </section>
    </form>
</main>
@endsection
