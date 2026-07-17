@extends('layouts.app')

@section('title', 'Profile Studio')

@section('content')
@php
    $selectedApps = collect($profile['favorite_app_slugs'] ?? []);
    $unlocked = collect($profile['unlocked_profile_items'] ?? [
        'profile_theme:cosmic',
        'profile_theme:ocean',
        'profile_theme:forest',
        'profile_frame:none',
        'profile_badge:learning-spark',
        'profile_color:purple',
        'profile_color:cyan',
        'avatar_shape:rounded',
        'avatar_shape:circle',
    ]);

    $photoUrl = null;
    if (!empty($user->profile_photo_path)) {
        $photoUrl = preg_match('/^https?:\/\//i', $user->profile_photo_path)
            ? $user->profile_photo_path
            : asset('storage/'.$user->profile_photo_path);
    }

    $currentTheme = old('profile_theme', $profile['profile_theme'] ?? 'cosmic');
    $currentFrame = old('profile_frame', $profile['profile_frame'] ?? 'none');
    $currentBadge = old('profile_badge', $profile['profile_badge'] ?? 'learning-spark');
    $currentColor = old('profile_color', $profile['profile_color'] ?? 'purple');
    $currentShape = old('avatar_shape', $profile['avatar_shape'] ?? 'rounded');

    $customGroups = [
        'profile_theme' => 'Profile theme',
        'profile_frame' => 'Avatar frame',
        'profile_badge' => 'Showcase badge',
        'profile_color' => 'Accent colour',
        'avatar_shape' => 'Avatar shape',
    ];
@endphp

<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-dashboard-system.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-dashboard-system.css')) ? filemtime(public_path('assets/css/studybuddy-dashboard-system.css')) : time() }}">
<link rel="stylesheet" href="{{ asset('assets/css/studybuddy-profile-customizer.css') }}?v={{ file_exists(public_path('assets/css/studybuddy-profile-customizer.css')) ? filemtime(public_path('assets/css/studybuddy-profile-customizer.css')) : time() }}">
<script src="{{ asset('assets/js/studybuddy-profile-customizer.js') }}?v={{ file_exists(public_path('assets/js/studybuddy-profile-customizer.js')) ? filemtime(public_path('assets/js/studybuddy-profile-customizer.js')) : time() }}" defer></script>

<div class="sb-user-hub">
    <section class="sb-page-hero compact">
        <p class="sb-hub-kicker">Profile Studio</p>
        <h1>Customize your StudyBuddy showcase.</h1>
        <p>Upload a profile picture, choose your colours, unlock frames with coins, and decide what your public profile shows.</p>
    </section>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-studio customizer-studio">
        @csrf
        @method('PATCH')

        @if(session('status'))
            <div class="success-box">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="error-box">
                <strong>Fix these first:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="profile-form-card profile-preview-card theme-{{ $currentTheme }} frame-{{ $currentFrame }} color-{{ $currentColor }} shape-{{ $currentShape }}" data-profile-preview>
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Live preview</p>
                    <h2>Your showcase card</h2>
                </div>
                <span class="coin-pill">⭐ {{ number_format((int) ($user->cosmic_points ?? 0)) }} coins</span>
            </div>

            <div class="showcase-preview">
                <div class="showcase-avatar">
                    @if($photoUrl)
                        <img src="{{ $photoUrl }}" alt="{{ $user->name }} profile picture" data-pfp-preview>
                    @else
                        <span data-pfp-letter>{{ strtoupper(substr($user->name ?? 'S', 0, 1)) }}</span>
                        <img src="" alt="" data-pfp-preview hidden>
                    @endif
                </div>

                <div>
                    <strong data-preview-name>{{ $user->name }}</strong>
                    <p data-preview-headline>{{ $profile['headline'] ?? 'Learning with StudyBuddy' }}</p>
                    <span class="showcase-badge" data-preview-badge>{{ $customizations['profile_badge'][$currentBadge]['label'] ?? 'Learning Spark' }}</span>
                </div>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Profile picture</p>
                    <h2>Choose your avatar.</h2>
                </div>
            </div>

            <div class="pfp-upload-row">
                <label class="pfp-drop">
                    <input type="file" name="profile_photo" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" data-pfp-input>
                    <span>Upload profile picture</span>
                    <small>PNG, JPG, WEBP, or GIF. Max 2MB.</small>
                </label>

                <div class="pfp-tips">
                    <strong>Tip:</strong>
                    <p>Use a clear square image. Your frame and colours will appear on your public profile and community card.</p>
                </div>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Basics</p>
                    <h2>Account display</h2>
                </div>
            </div>

            <div class="profile-form-grid">
                <label>
                    <span>Display name</span>
                    <input name="name" value="{{ old('name', $user->name) }}" required data-name-input>
                </label>

                <label>
                    <span>Real name</span>
                    <input name="real_name" value="{{ old('real_name', $user->real_name) }}">
                </label>

                <label>
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </label>

                <label>
                    <span>Country</span>
                    <input name="country" value="{{ old('country', $user->country) }}" placeholder="Example: UAE">
                </label>

                <label>
                    <span>Learning focus</span>
                    <input name="learning_stage" value="{{ old('learning_stage', $user->learning_stage) }}" placeholder="Math, reading, focus...">
                </label>

                <label>
                    <span>Avatar style</span>
                    <select name="avatar_style">
                        <option value="dolphin-cadet" @selected(old('avatar_style', $user->avatar_style) === 'dolphin-cadet')>Dolphin Cadet</option>
                        <option value="cosmic-explorer" @selected(old('avatar_style', $user->avatar_style) === 'cosmic-explorer')>Cosmic Explorer</option>
                        <option value="parent-guide" @selected(old('avatar_style', $user->avatar_style) === 'parent-guide')>Parent Guide</option>
                        <option value="teacher-mentor" @selected(old('avatar_style', $user->avatar_style) === 'teacher-mentor')>Teacher Mentor</option>
                        <option value="star-builder" @selected(old('avatar_style', $user->avatar_style) === 'star-builder')>Star Builder</option>
                    </select>
                </label>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Public profile</p>
                    <h2>Your showcase text</h2>
                </div>
            </div>

            <div class="profile-form-grid">
                <label>
                    <span>Headline</span>
                    <input name="headline" value="{{ old('headline', $profile['headline'] ?? '') }}" placeholder="Example: Focus queen building daily wins" data-headline-input>
                </label>

                <label>
                    <span>Profile mood</span>
                    <input name="profile_mood" value="{{ old('profile_mood', $profile['profile_mood'] ?? '') }}" placeholder="Calm, creative, exam mode...">
                </label>

                <label class="full">
                    <span>Bio</span>
                    <textarea name="bio" rows="4" placeholder="Tell people what you are learning and what motivates you.">{{ old('bio', $profile['bio'] ?? '') }}</textarea>
                </label>

                <label>
                    <span>Favorite subjects</span>
                    <input name="favorite_subjects" value="{{ old('favorite_subjects', $profile['favorite_subjects'] ?? '') }}" placeholder="Math, reading, spelling...">
                </label>

                <label>
                    <span>Learning goal</span>
                    <input name="learning_goal" value="{{ old('learning_goal', $profile['learning_goal'] ?? '') }}" placeholder="Build confidence, revise daily...">
                </label>

                <label>
                    <span>Current focus</span>
                    <input name="current_focus" value="{{ old('current_focus', $profile['current_focus'] ?? '') }}" placeholder="Mental maths, quizzes, focus blocks...">
                </label>
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Style shop</p>
                    <h2>Unlock with coins</h2>
                </div>
                <span class="coin-pill">⭐ {{ number_format((int) ($user->cosmic_points ?? 0)) }}</span>
            </div>

            <?php foreach ($customGroups as $field => $label): ?>
                <?php
                    $current = old($field, $profile[$field] ?? array_key_first($customizations[$field] ?? []));
                    $items = $customizations[$field] ?? [];
                ?>

                <div class="custom-shop-group">
                    <h3>{{ $label }}</h3>

                    <div class="custom-shop-grid">
                        <?php foreach ($items as $value => $item): ?>
                            <?php
                                $unlockKey = $field . ':' . $value;
                                $cost = (int) ($item['cost'] ?? 0);
                                $isUnlocked = $cost === 0 || $unlocked->contains($unlockKey);
                                $itemLabel = $item['label'] ?? ucfirst(str_replace('-', ' ', $value));
                            ?>

                            <label class="custom-option {{ $isUnlocked ? 'is-unlocked' : 'is-locked' }}">
                                <input
                                    type="radio"
                                    name="{{ $field }}"
                                    value="{{ $value }}"
                                    @checked($current === $value)
                                    data-custom-field="{{ $field }}"
                                    data-custom-value="{{ $value }}"
                                    data-custom-label="{{ $itemLabel }}"
                                >
                                <span>{{ $itemLabel }}</span>
                                <small>{{ $isUnlocked ? 'Unlocked' : '⭐ '.$cost.' coins' }}</small>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Favorite apps</p>
                    <h2>Choose your worlds</h2>
                </div>
            </div>

            <div class="favorite-app-picker">
                @forelse($apps as $app)
                    <label>
                        <input type="checkbox" name="favorite_app_slugs[]" value="{{ $app->slug }}" @checked($selectedApps->contains($app->slug))>
                        <span>{{ $app->icon ?? '✨' }}</span>
                        <strong>{{ $app->name }}</strong>
                        <small>{{ $app->category }}</small>
                    </label>
                @empty
                    <article class="empty-panel">
                        <strong>No apps yet</strong>
                        <p>Your favorite app worlds will appear here once apps are available.</p>
                    </article>
                @endforelse
            </div>
        </section>

        <section class="profile-form-card">
            <div class="section-title">
                <div>
                    <p class="sb-hub-kicker">Visibility</p>
                    <h2>Privacy controls</h2>
                </div>
            </div>

            <div class="visibility-grid">
                <label>
                    <input type="checkbox" name="public_profile_enabled" value="1" @checked(old('public_profile_enabled', $profile['public_profile_enabled'] ?? false))>
                    <span>Make my profile public</span>
                </label>

                <label>
                    <input type="checkbox" name="show_points" value="1" @checked(old('show_points', $profile['show_points'] ?? false))>
                    <span>Show my points</span>
                </label>

                <label>
                    <input type="checkbox" name="show_role" value="1" @checked(old('show_role', $profile['show_role'] ?? false))>
                    <span>Show my role</span>
                </label>

                <label>
                    <input type="checkbox" name="show_favorite_apps" value="1" @checked(old('show_favorite_apps', $profile['show_favorite_apps'] ?? true))>
                    <span>Show favorite apps</span>
                </label>
            </div>

            <div class="profile-actions">
                <button type="submit">Save profile</button>
                <a href="{{ route('studybuddy.profile.public', $user->id) }}">Preview public page</a>
                <a class="soft" href="{{ route('studybuddy.community') }}">Open community</a>
            </div>
        </section>
    </form>
</div>
@endsection
