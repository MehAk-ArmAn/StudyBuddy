@extends('layouts.app')

@section('title', 'My Learning Activity — StudyBuddy')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/studybuddy-w30/activity.css') }}?v=30">
@endpush

@section('content')
<div class="sb30-page">
    <div class="sb30-stars" aria-hidden="true"></div>
    <div class="sb30-wrap">
        <header class="sb30-hero">
            <div class="sb30-hero-copy">
                <p class="sb30-kicker"><span class="sb30-spark">✦</span> MY PERSONAL LEARNING UNIVERSE</p>
                <h1>Every little step<br><em>becomes a story.</em></h1>
                <p class="sb30-lead">This is your own private record of the learning apps you explore. Pick a published app, start a session, and return here when you're done.</p>
                <div class="sb30-links"><a href="{{ url('/dashboard') }}">← Dashboard</a><a href="{{ url('/my-quest') }}">My Quest ↗</a>{{-- STUDYBUDDY_W31_INSIGHTS_LINK --}}<a href="{{ route('studybuddy.connected.insights') }}">Learning insights ↗</a></div>
            </div>
            <div class="sb30-orbit" aria-hidden="true"><span class="sb30-orbit-a"></span><span class="sb30-orbit-b"></span><span class="sb30-orbit-c">✦</span><span class="sb30-orbit-d">✧</span><span class="sb30-orbit-core">📖</span></div>
        </header>

        @if(session('status'))<div role="status" class="sb30-alert">{{ session('status') }}</div>@endif
        @if($errors->any())<div role="alert" class="sb30-error"><strong>Check this first:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <section class="sb30-stats" aria-label="Your saved learning activity">
            <article><strong>{{ number_format($totalFinished) }}</strong><span>Sessions saved</span></article>
            <article><strong>{{ number_format($minutesLogged) }}</strong><span>Minutes between start and finish</span></article>
            <article><strong>{{ number_format($daysExplored) }}</strong><span>Days with saved sessions</span></article>
        </section>
        <p class="sb30-note">These are your own session records, not verified skill scores, active-play measurements, or achievement awards.</p>

        @if($active)
            <section class="sb30-active" aria-label="Current learning session">
                <div><span class="sb30-kicker">SESSION IN PROGRESS</span><h2>{{ $active->app_name }}</h2><p>Started {{ \Illuminate\Support\Carbon::parse($active->started_at)->diffForHumans() }}. Launch the app in a new tab, then come back to close this session.</p>
                    {{-- STUDYBUDDY_W32_ACTIVE_SESSION_LINK --}}
                    @if(($active->launch_source ?? null) === 'w32_server_graded')
                    <a class="sb30-launch" href="{{ route('studybuddy.w32.index') }}">Continue Math Sprint ↗</a>
                    @else
                    <a class="sb30-launch" href="{{ route('studybuddy.final.web-play', $active->app_slug) }}" target="_blank" rel="noopener">Open {{ $active->app_name }} ↗</a>
                    @endif
                </div>
                <form action="{{ route('studybuddy.learning.finish', $active->id) }}" method="post" class="sb30-finish">@csrf
                    <label for="sb30-reflection">One thing you explored <small>(optional, private)</small></label>
                    <textarea id="sb30-reflection" name="reflection" maxlength="280" rows="3" placeholder="I practised fractions, tried a new level…">{{ old('reflection') }}</textarea>
                    <button type="submit">Save this session ✦</button>
                </form>
                <form action="{{ route('studybuddy.learning.leave', $active->id) }}" method="post" class="sb30-leave">@csrf<button type="submit">Leave without saving progress</button></form>
            </section>
        @else
            <section class="sb30-picker"><div><span class="sb30-kicker">START SOMETHING NEW</span><h2>Where will curiosity take you?</h2><p>Only published browser-playable apps available to your account appear here.</p></div>
                @if($apps->isNotEmpty())<form method="post" action="{{ route('studybuddy.learning.start') }}" class="sb30-start-form">@csrf
                    <label for="sb30-app">Choose a learning app</label><select id="sb30-app" name="app_slug" required>@foreach($apps as $app)<option value="{{ $app->slug }}">{{ $app->name }} — {{ $app->category }}</option>@endforeach</select>
                    <button type="submit">Start learning ↗</button>
                </form>
                @else<p class="sb30-empty-notice">There are no published web learning apps available to your account yet. Browse the catalogue while new worlds are being prepared.</p><a class="sb30-launch" href="{{ url('/apps') }}">Explore learning apps ↗</a>@endif
            </section>
        @endif

        <section class="sb30-history" aria-label="Your session history"><div class="sb30-heading"><div><span class="sb30-kicker">YOUR EXPLORATION LOG</span><h2>Learning moments</h2></div><span class="sb30-private">🔒 PRIVATE</span></div>
            @forelse($sessions as $session)
                <article class="sb30-history-card"><div class="sb30-history-icon" aria-hidden="true">✦</div><div class="sb30-history-text"><h3>{{ $session->app_name }}</h3><p>{{ \Illuminate\Support\Carbon::parse($session->started_at)->format('D, j M Y · g:i A') }} · {{ ucfirst($session->status) }} @if($session->status !== 'active' && $session->duration_seconds !== null) · {{ max(0, round($session->duration_seconds / 60, 1)) }} min elapsed @endif</p>@if($session->reflection)<p class="sb30-reflection">“{{ $session->reflection }}”</p>@endif</div><form action="{{ route('studybuddy.learning.destroy', $session->id) }}" method="post">@csrf @method('DELETE')<button type="submit" aria-label="Delete activity for {{ $session->app_name }}">Delete</button></form></article>
            @empty<div class="sb30-no-history"><span aria-hidden="true">✧</span><h3>Your story begins here.</h3><p>No recorded sessions yet. Nothing is invented or filled with demo data.</p></div>@endforelse
            <div class="sb30-pages">{{ $sessions->links() }}</div>
        </section>
    </div>
</div>
@endsection
