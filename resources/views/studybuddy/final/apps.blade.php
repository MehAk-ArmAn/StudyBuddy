@extends('layouts.app')

@section('content')
@php
    $currentRole = auth()->user()?->normalizedRole();
    $assetFor = function ($path) {
        if (! $path) return null;
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        return asset($path);
    };
@endphp
<main class="sb-final-shell sb-apps-unified" data-sb-final-app>
    <section class="sb-final-hero sb-apps-hero">
        <div>
            <p class="sb-final-kicker">StudyBuddy App Universe</p>
            <h1>{{ $settings['launchpad_heading'] ?? 'Choose your learning world.' }}</h1>
            <p>{{ $settings['launchpad_intro'] ?? 'Preview every mini-app, open detail pages, save quests, and play web builds when available.' }}</p>
            <div class="sb-final-actions">
                @auth
                    <a href="{{ route('studybuddy.final.points-wallet') }}" class="sb-final-btn">My Points Wallet</a>
                    <a href="{{ route('studybuddy.quests.index') }}" class="sb-final-btn sb-final-btn-soft">My Quest</a>
                @else
                    <a href="{{ route('register') }}" class="sb-final-btn">Create free account</a>
                    <a href="{{ route('login') }}" class="sb-final-btn sb-final-btn-soft">Login</a>
                @endauth
                <a href="{{ route('studybuddy.final.roadmap') }}" class="sb-final-btn sb-final-btn-soft">Roadmap</a>
            </div>
        </div>
        <div class="sb-final-orb-card">
            <span>🚀</span>
            <strong>{{ $apps->count() }}</strong>
            <p>mini-app worlds controlled from admin</p>
        </div>
    </section>

    <section class="sb-final-toolbar sb-app-toolbar" aria-label="App filters">
        <input type="search" placeholder="Search apps, skills, subjects..." data-sb-app-search>
        <select data-sb-app-filter>
            <option value="all">All categories</option>
            @foreach($categories as $category)
                <option value="{{ Str::slug($category) }}">{{ $category }}</option>
            @endforeach
        </select>
        <select data-sb-role-filter>
            <option value="all">All roles</option>
            @foreach($roles as $key => $label)
                <option value="{{ $key }}" @selected($currentRole === $key)>{{ $label }}</option>
            @endforeach
        </select>
    </section>

    <section class="sb-app-preview-note" aria-label="Preview mode note">
        @guest
            <strong>Preview mode:</strong> You can browse every app. Login to save quests, play web builds, and earn points.
        @else
            <strong>{{ ucfirst(str_replace('_', ' ', $currentRole ?? 'learner')) }} mode:</strong> Apps are filtered and labeled for your role. Admin controls app text, status, links, and rewards.
        @endguest
    </section>

    <section class="sb-final-grid sb-app-card-grid">
        @foreach($apps as $app)
            @php
                $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];
                $searchText = Str::lower($app->name.' '.$app->tagline.' '.$app->description.' '.$app->category.' '.implode(' ', $rolesForApp));
                $image = $assetFor($app->safeHeroImage());
            @endphp
            <article class="sb-final-app-card sb-app-card" data-app-card data-category="{{ Str::slug($app->category) }}" data-roles="{{ implode(' ', $rolesForApp) }}" data-search="{{ $searchText }}">
                <div class="sb-app-media {{ $image ? 'has-image' : '' }}">
                    @if($image)
                        <img src="{{ $image }}" alt="{{ $app->name }} preview" loading="lazy">
                    @else
                        <span>{{ $app->icon ?: '✨' }}</span>
                    @endif
                </div>
                <div class="sb-final-app-top">
                    <span class="sb-final-status sb-status-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                    <span class="sb-app-age">{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
                </div>
                <h2>{{ $app->name }}</h2>
                <p class="sb-final-tagline">{{ $app->tagline }}</p>
                <p>{{ $app->preview_text ?: Str::limit($app->description, 145) }}</p>
                <div class="sb-role-chips">
                    @foreach($rolesForApp as $roleName)
                        <span>{{ ucwords(str_replace('_', ' ', $roleName)) }}</span>
                    @endforeach
                </div>
                <div class="sb-final-meta">
                    <span>⭐ {{ $app->points_reward }} pts</span>
                    <span>⏱ {{ $app->estimated_minutes }} min</span>
                    <span>{{ $app->category }}</span>
                </div>
                <div class="sb-final-platforms">
                    <a href="{{ route('studybuddy.apps.show', $app->slug) }}" class="sb-final-chip is-live">View Details</a>
                    @if($app->is_web_enabled)
                        @auth
                            <a href="{{ route('studybuddy.final.web-play', $app->slug) }}" class="sb-final-chip is-live">Play Web</a>
                        @else
                            <a href="{{ route('login') }}" class="sb-final-chip is-locked">Preview Web</a>
                        @endauth
                    @else
                        <span class="sb-final-chip">Web soon</span>
                    @endif
                </div>
            </article>
        @endforeach
    </section>
</main>
@endsection
