@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $isVerified = $user && $user->hasVerifiedEmail();
    $platforms = [
        'ios_url' => 'iOS',
        'android_url' => 'Android',
        'windows_url' => 'Windows',
        'mac_url' => 'Mac',
    ];
@endphp

<div class="sb-app-detail-shell">
    <nav class="sb-app-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ url('/apps') }}">Apps</a>
        <span>/</span>
        <strong>{{ $app->name }}</strong>
    </nav>

    <section class="sb-app-detail-hero">
        <div class="sb-app-detail-copy">
            <p class="sb-apps-kicker">{{ $app->category }} · {{ $app->age_range ?? 'All ages' }}</p>
            <h1>{{ $app->hero_heading ?: $app->name }}</h1>
            <p class="sb-app-detail-lead">{{ $app->long_description ?: $app->description }}</p>
            <div class="sb-app-detail-badges">
                <span class="sb-app-status status-{{ $app->status }}">{{ $app->statusLabel() }}</span>
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} min</span>
            </div>
            <div class="sb-app-detail-actions">
                @if($app->is_web_enabled)
                    @if($isVerified)
                        <a class="sb-app-btn" href="{{ route('studybuddy.final.web-play', $app->slug) }}">{{ $app->detail_cta_label ?: 'Play on Web' }}</a>
                    @else
                        <button class="sb-app-btn" type="button" data-sb-lock-trigger data-lock-kind="{{ auth()->check() ? 'verify' : 'login' }}">Preview Web Play</button>
                    @endif
                @else
                    <button class="sb-app-btn" type="button" data-sb-lock-trigger data-lock-kind="soon">Preview Coming Soon</button>
                @endif

                @if($isVerified && Route::has('studybuddy.quests.store'))
                    <form method="POST" action="{{ route('studybuddy.quests.store') }}" class="sb-inline-form">
                        @csrf
                        <input type="hidden" name="app_slug" value="{{ $app->slug }}">
                        <input type="hidden" name="app_title" value="{{ $app->name }}">
                        <input type="hidden" name="mission_title" value="Explore {{ $app->name }}">
                        <input type="hidden" name="mission_description" value="{{ $app->preview_text ?: $app->description }}">
                        <input type="hidden" name="estimated_minutes" value="{{ $app->estimated_minutes }}">
                        <input type="hidden" name="source_url" value="{{ route('studybuddy.apps.show', $app->slug) }}">
                        <button class="sb-app-btn soft" type="submit">Save to My Quest</button>
                    </form>
                @else
                    <button class="sb-app-btn soft" type="button" data-sb-lock-trigger data-lock-kind="{{ auth()->check() ? 'verify' : 'login' }}">Save Preview</button>
                @endif
            </div>
        </div>
        <aside class="sb-app-detail-media">
            @if($app->image_url)
                <img src="{{ str_starts_with($app->image_url, 'http') ? $app->image_url : asset(ltrim($app->image_url, '/')) }}" alt="{{ $app->name }} preview">
            @else
                <span>{{ $app->icon ?: '' }}</span>
            @endif
        </aside>
    </section>

    <section class="sb-app-detail-grid">
        <article class="sb-app-detail-panel">
            <h2>What you’ll learn</h2>
            <ul class="sb-pretty-list">
                @forelse($app->outcomesList() as $outcome)
                    <li>{{ $outcome }}</li>
                @empty
                    <li>{{ $app->description }}</li>
                @endforelse
            </ul>
        </article>
        <article class="sb-app-detail-panel">
            <h2>How it works</h2>
            <ol class="sb-pretty-list numbered">
                @forelse($app->howItWorksList() as $step)
                    <li>{{ $step }}</li>
                @empty
                    <li>Preview the app world.</li>
                    <li>Login and verify your email to unlock saving and points.</li>
                    <li>Track your progress from the StudyBuddy dashboard.</li>
                @endforelse
            </ol>
        </article>
    </section>

    <section class="sb-platform-panel">
        <div>
            <p class="sb-apps-kicker">Platforms</p>
            <h2>Play on web or download when ready</h2>
            <p>{{ $app->platform_notes ?: 'Web play and app downloads appear here when they are available.' }}</p>
        </div>
        <div class="sb-platform-grid">
            @if($app->is_web_enabled)
                @if($isVerified)
                    <a class="sb-platform-card live" href="{{ route('studybuddy.final.web-play', $app->slug) }}"><strong>Web</strong><span>Play now</span></a>
                @else
                    <button class="sb-platform-card live" type="button" data-sb-lock-trigger data-lock-kind="{{ auth()->check() ? 'verify' : 'login' }}"><strong>Web</strong><span>Preview only</span></button>
                @endif
            @else
                <button class="sb-platform-card" type="button" data-sb-lock-trigger data-lock-kind="soon"><strong>Web</strong><span>Coming soon</span></button>
            @endif

            @foreach($platforms as $field => $label)
                @if($app->{$field})
                    <a class="sb-platform-card live" href="{{ $app->{$field} }}" target="_blank" rel="noopener"><strong>{{ $label }}</strong><span>Open download</span></a>
                @else
                    <button class="sb-platform-card" type="button" data-sb-lock-trigger data-lock-kind="soon"><strong>{{ $label }}</strong><span>Coming soon</span></button>
                @endif
            @endforeach
        </div>
    </section>

    <section class="sb-app-preview-note">
        <h2>Preview note</h2>
        <p>{{ $app->preview_text ?: $app->locked_preview_note ?: 'Guests can preview this app. Saving, playing, and earning points require a verified StudyBuddy account.' }}</p>
        <p class="sb-safety-note">{{ $app->safety_note ?: 'StudyBuddy keeps app actions connected to safe account rules and role-aware access.' }}</p>
    </section>

    @if($related->isNotEmpty())
        <section class="sb-related-apps">
            <h2>Related learning worlds</h2>
            <div class="sb-related-grid">
                @foreach($related as $relatedApp)
                    <a href="{{ route('studybuddy.apps.show', $relatedApp->slug) }}" class="sb-related-card">
                        <span>{{ $relatedApp->icon ?: '' }}</span>
                        <strong>{{ $relatedApp->name }}</strong>
                        <small>{{ $relatedApp->category }} · {{ $relatedApp->statusLabel() }}</small>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>

@include('studybuddy.apps.partials.locked-modal')
@endsection
