@extends('layouts.app')

@section('content')
@php
    $rolesForApp = $app->audience_roles ?: ['student','parent','teacher','independent_learner'];
    $outcomes = $app->learning_outcomes ?: ['Build confidence through small missions', 'Practice safely at your own pace', 'Connect progress back to StudyBuddy'];
    $sections = $app->detail_sections ?: [
        ['title' => 'How it works', 'body' => 'Open the mini-app, complete a short mission, and return to StudyBuddy for your progress.'],
        ['title' => 'Why it helps', 'body' => 'Small focused activities make learning easier to start and easier to repeat.'],
    ];
    $assetFor = function ($path) {
        if (! $path) return null;
        if (preg_match('/^https?:\/\//i', $path)) return $path;
        return asset($path);
    };
    $image = $assetFor($app->safeHeroImage());
@endphp
<main class="sb-final-shell sb-app-detail-page">
    <section class="sb-app-detail-hero">
        <div class="sb-app-detail-copy">
            <p class="sb-final-kicker">{{ $app->category }} mini-app</p>
            <h1>{{ $app->icon }} {{ $app->name }}</h1>
            <p>{{ $app->description }}</p>
            <div class="sb-role-chips">
                <span class="sb-final-status sb-status-{{ $app->status }}">{{ ucfirst($app->status) }}</span>
                <span>⭐ {{ $app->points_reward }} points</span>
                <span>⏱ {{ $app->estimated_minutes }} minutes</span>
                <span>{{ $app->age_min ? $app->age_min.'+' : 'All ages' }}</span>
            </div>
            <div class="sb-final-actions">
                @if($app->is_web_enabled)
                    @auth
                        <a href="{{ route('studybuddy.final.web-play', $app->slug) }}" class="sb-final-btn">Play on Web</a>
                    @else
                        <a href="{{ route('login') }}" class="sb-final-btn">Login to Play</a>
                    @endauth
                @else
                    <span class="sb-final-btn sb-final-btn-disabled">Web Play Coming Soon</span>
                @endif
                <a href="{{ route('studybuddy.apps') }}" class="sb-final-btn sb-final-btn-soft">Back to Apps</a>
            </div>
        </div>
        <aside class="sb-app-detail-media {{ $image ? 'has-image' : '' }}">
            @if($image)<img src="{{ $image }}" alt="{{ $app->name }} artwork" loading="lazy">@else<span>{{ $app->icon ?: '✨' }}</span>@endif
        </aside>
    </section>

    <section class="sb-app-detail-grid">
        <article class="sb-final-panel">
            <h2>What you’ll build</h2>
            <ul class="sb-clean-list">
                @foreach($outcomes as $outcome)
                    <li>{{ is_array($outcome) ? ($outcome['text'] ?? implode(' ', $outcome)) : $outcome }}</li>
                @endforeach
            </ul>
        </article>
        <article class="sb-final-panel">
            <h2>Who it is for</h2>
            <div class="sb-role-chips">
                @foreach($rolesForApp as $roleName)<span>{{ ucwords(str_replace('_', ' ', $roleName)) }}</span>@endforeach
            </div>
            <p>{{ $app->safety_note ?: 'StudyBuddy keeps learning safe, clear, and focused. Admin controls app details, points, and platform links.' }}</p>
        </article>
    </section>

    <section class="sb-app-detail-grid">
        @foreach($sections as $section)
            <article class="sb-final-panel">
                <h2>{{ $section['title'] ?? 'App section' }}</h2>
                <p>{{ $section['body'] ?? $section['description'] ?? 'Admin-editable app detail content.' }}</p>
            </article>
        @endforeach
    </section>

    <section class="sb-final-panel">
        <h2>Platform options</h2>
        <div class="sb-final-platforms big">
            @foreach(['web' => 'Web Play', 'ios' => 'iOS', 'android' => 'Android', 'windows' => 'Windows', 'mac' => 'Mac'] as $key => $label)
                @php($url = $key === 'web' ? ($app->is_web_enabled ? route('studybuddy.final.web-play', $app->slug) : null) : ($app->{$key.'_url'} ?? null))
                @if($url)
                    @auth<a href="{{ $url }}" class="sb-final-chip is-live" @if($key !== 'web') target="_blank" rel="noopener" @endif>{{ $label }}</a>
                    @else<a href="{{ route('login') }}" class="sb-final-chip is-locked">{{ $label }} preview</a>@endauth
                @else
                    <span class="sb-final-chip">{{ $label }} soon</span>
                @endif
            @endforeach
        </div>
    </section>

    @if($related->count())
        <section class="sb-final-panel">
            <h2>Related learning worlds</h2>
            <div class="sb-mini-related">
                @foreach($related as $mini)
                    <a href="{{ route('studybuddy.apps.show', $mini->slug) }}"><span>{{ $mini->icon ?: '✨' }}</span><strong>{{ $mini->name }}</strong><small>{{ $mini->category }}</small></a>
                @endforeach
            </div>
        </section>
    @endif
</main>
@endsection
