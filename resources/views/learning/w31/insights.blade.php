@extends('layouts.app')

@section('title', 'Learning Insights — StudyBuddy')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w31/connected.css') }}?v=31.0">
@endpush

@section('content')
<main class="sb31-insights">
    <div class="sb31-insights__stars" aria-hidden="true"></div>
    <div class="sb31-insights__inner">
        <header class="sb31-insights__hero">
            <div>
                <span class="sb31-insights__eyebrow">✦ YOUR STUDYBUDDY UNIVERSE</span>
                <h1>Your curiosity has a <em>story.</em></h1>
                <p>Your private learning visits, in one place. Built from your real StudyBuddy session records, not invented scores.</p>
                <div class="sb31-insights__hero-actions"><a href="{{ route('studybuddy.learning.activity') }}">My activity journal ↗</a><a href="{{ route('studybuddy.apps') }}">Explore apps</a></div>
            </div>
            <div class="sb31-insights__planet" aria-hidden="true">
                @if(file_exists(public_path('assets/studybuddy-w26/images/dolphin-portal.webp')))
                    <img src="{{ asset('assets/studybuddy-w26/images/dolphin-portal.webp') }}" alt="" loading="eager" decoding="async">
                @else
                    <span>✧</span>
                @endif
            </div>
        </header>
        <section class="sb31-insights__stats" aria-label="Your learning visit summary">
            <article><strong>{{ number_format($openedCount) }}</strong><span>Connected launches</span></article>
            <article><strong>{{ number_format($savedCount) }}</strong><span>Visits saved</span></article>
            <article><strong>{{ number_format($uniqueApps) }}</strong><span>Apps explored</span></article>
            <article><strong>{{ number_format($estimatedMinutes) }}</strong><span>Est. visible minutes</span></article>
        </section>
        <p class="sb31-insights__disclaimer">Connected launches are recorded by the StudyBuddy wrapper. Visible-time estimates can be incomplete and don't prove attention, skill mastery, level completion, or rewards. @if($limitedWindow)This overview reflects your 150 most recent entries.@endif</p>
        <section class="sb31-insights__section">
            <div class="sb31-insights__heading"><span class="sb31-insights__eyebrow">YOUR LEARNING WORLDS</span><h2>Where you've explored</h2></div>
            @if($byApp->isEmpty())
                <div class="sb31-insights__empty"><strong>Your story begins with one app ✨</strong><p>Open a published learning app, and connected visits will start appearing here.</p><a href="{{ route('studybuddy.apps') }}">Explore your first world →</a></div>
            @else
                <div class="sb31-insights__world-grid">
                    @foreach($byApp as $world)
                        <article class="sb31-insights__world">
                            <span class="sb31-insights__world-spark" aria-hidden="true">✧</span>
                            <h3>{{ $world->name }}</h3>
                            <p>{{ $world->visits }} {{ \Illuminate\Support\Str::plural('visit', $world->visits) }} · {{ $world->saved }} saved · {{ (int) floor($world->observed_seconds / 60) }} estimated visible min</p>
                            {{-- STUDYBUDDY_W32_REVISIT_LINK --}}<a href="{{ $world->slug === 'studybuddy-math-sprint' ? route('studybuddy.w32.index') : route('studybuddy.final.web-play', $world->slug) }}">Visit again ↗</a>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
        <section class="sb31-insights__section">
            <div class="sb31-insights__heading"><span class="sb31-insights__eyebrow">ONLY YOU CAN SEE THIS</span><h2>Recent activity</h2></div>
            @forelse($recent as $visit)
                <div class="sb31-insights__history"><span aria-hidden="true">{{ $visit->status === 'active' ? '◌' : '✦' }}</span><div><strong>{{ $visit->app_name }}</strong><small>{{ \Illuminate\Support\Carbon::parse($visit->started_at)->format('j M Y · g:i a') }} · {{ ucfirst($visit->status) }} · {{ ($visit->launch_source ?? '') === 'web_launcher' ? 'Connected' : 'Journal' }}</small></div><span>{{ (int) floor(($visit->observed_seconds ?? 0) / 60) }} est. min</span></div>
            @empty
                <div class="sb31-insights__empty"><p>No learning visits yet. Your progress story starts whenever you explore.</p></div>
            @endforelse
        </section>
        @if($suggested->isNotEmpty())
        <section class="sb31-insights__section">
            <div class="sb31-insights__heading"><span class="sb31-insights__eyebrow">NEW PLACES TO EXPLORE</span><h2>Your next discovery</h2><p>Published apps available to your account that aren't in your recent history—not AI-generated skill assessments.</p></div>
            <div class="sb31-insights__world-grid">
                @foreach($suggested as $app)
                <article class="sb31-insights__world sb31-insights__world--new"><span class="sb31-insights__world-spark" aria-hidden="true">✷</span><h3>{{ $app->name }}</h3><p>{{ $app->category ?: 'Learning world' }}</p><a href="{{ $app->browserLaunchUrl() }}">Explore now ↗</a></article>
                @endforeach
            </div>
        </section>
        @endif
                {{-- STUDYBUDDY_W32_MATH_SPRINT_DISCOVERY --}}
        <section class="sb31-insights__section" aria-label="Try a server-graded mini-app">
            <div class="sb31-insights__heading"><span class="sb31-insights__eyebrow">PLAY. SOLVE. GROW.</span><h2>Math Sprint ✨</h2><p>Five real number puzzles graded by the StudyBuddy server. Earn your own Math Star and Perfect Orbit badges; no invented gameplay scores or points.</p></div>
            <div class="sb31-insights__world-grid"><article class="sb31-insights__world"><span class="sb31-insights__world-spark" aria-hidden="true">✷</span><h3>Math Sprint</h3><p>First-party mini-app · Server-graded · Private achievements</p><a href="{{ route('studybuddy.w32.index') }}">Play Math Sprint ↗</a></article></div>
        </section>
        <footer class="sb31-insights__footer"><a href="{{ route('studybuddy.learning.activity') }}">Review or delete your private activity entries ↗</a></footer>
    </div>
</main>
@endsection
