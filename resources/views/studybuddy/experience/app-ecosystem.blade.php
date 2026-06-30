@extends('layouts.app')

@section('title', 'App Ecosystem | StudyBuddy')

@section('content')
<main class="sbx-shell" data-sbx-page="app-ecosystem">
    @include('studybuddy.experience.partials.experience-nav')

    <section class="sbx-hero sbx-reveal">
        <div>
            <span class="sbx-kicker">Connected mini-app vision</span>
            <h1>One dashboard. Many learning apps.</h1>
            <p>
                StudyBuddy is being shaped so separate mini-apps can later connect back to one learner dashboard.
                Learners can eventually play on web, download for their device, earn points, and manage progress centrally.
            </p>
            <div class="sbx-hero__actions">
                <a href="{{ route('apps') }}" class="sbx-btn sbx-btn--primary">View app launcher</a>
                <a href="{{ route('studybuddy.rewards') }}" class="sbx-btn sbx-btn--ghost">View rewards</a>
            </div>
        </div>
        <aside class="sbx-orbit-card">
            <span class="sbx-orbit-card__icon">🕹️</span>
            <strong>Important</strong>
            <p>Actual web hosting, iOS, Android, and Windows build delivery is planned for the final app distribution phase.</p>
        </aside>
    </section>

    <section class="sbx-app-table sbx-reveal">
        @foreach($miniApps as $app)
            <article>
                <div>
                    <strong>{{ $app['name'] }}</strong>
                    <span>{{ $app['type'] }} • {{ $app['focus'] }}</span>
                </div>
                <div class="sbx-app-table__actions">
                    <button type="button" disabled>Web play planned</button>
                    <button type="button" disabled>Downloads planned</button>
                    <em>{{ $app['points'] }} pts idea</em>
                </div>
            </article>
        @endforeach
    </section>

    <section class="sbx-panel sbx-reveal">
        <div class="sbx-section-head">
            <span class="sbx-kicker">Distribution phase later</span>
            <h2>What app hosting will need</h2>
        </div>
        <div class="sbx-grid sbx-grid--3">
            <article class="sbx-card"><h3>Web builds</h3><p>Browser-playable versions hosted under StudyBuddy routes/subdomains.</p></article>
            <article class="sbx-card"><h3>Downloads</h3><p>Device-based links for iOS, Android, Windows, and possibly macOS.</p></article>
            <article class="sbx-card"><h3>Progress API</h3><p>Apps send points, badges, and completion data back to the dashboard.</p></article>
        </div>
    </section>
</main>
@endsection
